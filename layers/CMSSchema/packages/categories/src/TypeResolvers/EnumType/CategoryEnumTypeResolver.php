<?php

declare(strict_types=1);

namespace PoPCMSSchema\Categories\TypeResolvers\EnumType;

use PoP\ComponentModel\App;
use PoPCMSSchema\Categories\Module;
use PoPCMSSchema\Categories\ModuleConfiguration;
use PoP\ComponentModel\TypeResolvers\EnumType\AbstractEnumTypeResolver;

class CategoryEnumTypeResolver extends AbstractEnumTypeResolver
{
    public function getTypeName(): string
    {
        return 'CategoryEnum';
    }

    /**
     * @return string[]
     */
    public function getEnumValues(): array
    {
        /** @var ModuleConfiguration */
        $moduleConfiguration = App::getModule(Module::class)->getConfiguration();
        return $moduleConfiguration->getQueryableCategoryTaxonomies();
    }
}
