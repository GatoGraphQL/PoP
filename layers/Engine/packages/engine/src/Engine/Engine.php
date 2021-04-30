<?php

declare(strict_types=1);

namespace PoP\Engine\Engine;

use Exception;
use PoP\Hooks\HooksAPIInterface;
use PoP\Translation\TranslationAPIInterface;
use PoP\LooseContracts\LooseContractManagerInterface;
use PoP\ComponentModel\Settings\SettingsManagerFactory;
use PoP\CacheControl\Component as CacheControlComponent;
use PoP\CacheControl\Managers\CacheControlEngineInterface;
use PoP\ComponentModel\DataStructure\DataStructureManagerInterface;

class Engine extends \PoP\ComponentModel\Engine\Engine implements EngineInterface
{
    function __construct(
        TranslationAPIInterface $translationAPI,
        HooksAPIInterface $hooksAPI,
        DataStructureManagerInterface $dataStructureManager,
        protected LooseContractManagerInterface $looseContractManager,
        protected CacheControlEngineInterface $cacheControlEngine
    ) {
        parent::__construct($translationAPI, $hooksAPI, $dataStructureManager);
    }

    public function generateData()
    {
        // Check if there are hooks that must be implemented by the CMS, that have not been done so.
        // Check here, since we can't rely on addAction('popcms:init') to check, since we don't know if it was implemented!
        if ($notImplementedHooks = $this->looseContractManager->getNotImplementedRequiredHooks()) {
            throw new Exception(
                sprintf(
                    $this->translationAPI->__('The following hooks have not been implemented by the CMS: "%s". Hence, we can\'t continue.'),
                    implode($this->translationAPI->__('", "'), $notImplementedHooks)
                )
            );
        }
        if ($notImplementedNames = $this->looseContractManager->getNotImplementedRequiredNames()) {
            throw new Exception(
                sprintf(
                    $this->translationAPI->__('The following names have not been implemented by the CMS: "%s". Hence, we can\'t continue.'),
                    implode($this->translationAPI->__('", "'), $notImplementedNames)
                )
            );
        }

        parent::generateData();
    }

    public function maybeRedirectAndExit(): void
    {
        if ($redirect = SettingsManagerFactory::getInstance()->getRedirectUrl()) {
            if ($query = $_SERVER['QUERY_STRING']) {
                $redirect .= '?' . $query;
            }

            $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
            $cmsengineapi->redirect($redirect);
            exit;
        }
    }

    public function outputResponse(): void
    {
        // Before anything: check if to do a redirect, and exit
        $this->maybeRedirectAndExit();

        // 1. Generate the data
        $this->generateData();

        // 2. Get the data, and ask the formatter to output it
        $formatter = $this->dataStructureManager->getDataStructureFormatter();

        // If CacheControl is enabled, add it to the headers
        $headers = [];
        if (CacheControlComponent::isEnabled()) {
            if ($cacheControlHeader = $this->cacheControlEngine->getCacheControlHeader()) {
                $headers[] = $cacheControlHeader;
            }
        }
        $data = $this->getOutputData();
        $formatter->outputResponse($data, $headers);
    }
}
