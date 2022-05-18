<?php

declare(strict_types=1);

namespace PoP\SPA\ComponentFilters;

use PoP\ComponentModel\ComponentFilters\AbstractComponentFilter;
use PoP\SPA\Modules\PageInterface;

class Page extends AbstractComponentFilter
{
    public function getName(): string
    {
        return 'page';
    }

    /**
     * Exclude until reaching the pageSection
     */
    public function excludeModule(array $componentVariation, array &$props): bool
    {
        $processor = $this->getComponentProcessorManager()->getProcessor($componentVariation);
        return !($processor instanceof PageInterface);
    }
}
