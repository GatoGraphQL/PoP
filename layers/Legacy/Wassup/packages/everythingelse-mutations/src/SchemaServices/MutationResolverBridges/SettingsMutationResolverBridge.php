<?php

declare(strict_types=1);

namespace PoPSitesWassup\EverythingElseMutations\SchemaServices\MutationResolverBridges;

use PoP\ComponentModel\MutationResolvers\MutationResolverInterface;
use PoP\ComponentModel\QueryInputOutputHandlers\ResponseConstants;
use PoPSitesWassup\EverythingElseMutations\SchemaServices\MutationResolvers\SettingsMutationResolver;
use PoP\ComponentModel\MutationResolverBridges\AbstractComponentMutationResolverBridge;

class SettingsMutationResolverBridge extends AbstractComponentMutationResolverBridge
{
    public function __construct(
        \PoP\Hooks\HooksAPIInterface $hooksAPI,
        \PoP\Translation\TranslationAPIInterface $translationAPI,
        \PoP\ComponentModel\Instances\InstanceManagerInterface $instanceManager,
        \PoP\ComponentModel\MutationResolution\MutationResolutionManagerInterface $mutationResolutionManager,
        protected SettingsMutationResolver $SettingsMutationResolver,
    ) {
        parent::__construct(
            $hooksAPI,
            $translationAPI,
            $instanceManager,
            $mutationResolutionManager,
        );
    }
    
    public function getMutationResolver(): MutationResolverInterface
    {
        return $this->SettingsMutationResolver;
    }

    public function getFormData(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function executeMutation(array &$data_properties): ?array
    {
        $executed = parent::executeMutation($data_properties);
        if ($executed !== null && $executed[ResponseConstants::SUCCESS]) {
            // Add the result from the MutationResolver as hard redirect
            $redirect_to = $this->mutationResolutionManager->getResult($this);
            $executed[GD_DATALOAD_QUERYHANDLERRESPONSE_HARDREDIRECT] = $redirect_to;
        }
        return $executed;
    }
}
