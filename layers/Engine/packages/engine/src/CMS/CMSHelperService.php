<?php

declare(strict_types=1);

namespace PoP\Engine\CMS;

use PoP\ComponentModel\Misc\GeneralUtils;
use PoP\ComponentModel\Services\BasicServiceTrait;
use Symfony\Contracts\Service\Attribute\Required;

class CMSHelperService implements CMSHelperServiceInterface
{
    use BasicServiceTrait;
    
    private ?CMSServiceInterface $cmsService = null;

    public function setCMSService(CMSServiceInterface $cmsService): void
    {
        $this->cmsService = $cmsService;
    }
    protected function getCMSService(): CMSServiceInterface
    {
        return $this->cmsService ??= $this->instanceManager->getInstance(CMSServiceInterface::class);
    }

    //#[Required]
    final public function autowireCMSHelperService(CMSServiceInterface $cmsService): void
    {
        $this->cmsService = $cmsService;
    }

    public function getLocalURLPath(string $url): string | false
    {
        if (str_starts_with($url, $this->getCmsService()->getHomeURL())) {
            return GeneralUtils::getPath($url);
        }
        return false;
    }
}
