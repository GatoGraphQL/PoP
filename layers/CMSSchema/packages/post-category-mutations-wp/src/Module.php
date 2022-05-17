<?php

declare(strict_types=1);

namespace PoPCMSSchema\PostCategoryMutationsWP;

use PoP\Root\Module\AbstractModule;

class Module extends AbstractModule
{
    /**
     * All component classes that this component satisfies
     *
     * @return string[]
     */
    public function getSatisfiedModuleClasses(): array
    {
        return [
            \PoPCMSSchema\PostCategoryMutations\Module::class,
        ];
    }

    /**
     * Classes from PoP components that must be initialized before this component
     *
     * @return string[]
     */
    public function getDependedModuleClasses(): array
    {
        return [
            \PoPCMSSchema\PostCategoryMutations\Module::class,
            \PoPCMSSchema\CustomPostMutationsWP\Module::class,
            \PoPCMSSchema\PostCategoriesWP\Module::class,
            \PoPCMSSchema\UserStateMutationsWP\Module::class,
        ];
    }

    /**
     * Initialize services
     *
     * @param string[] $skipSchemaModuleClasses
     */
    protected function initializeContainerServices(
        bool $skipSchema,
        array $skipSchemaModuleClasses,
    ): void {
        $this->initServices(dirname(__DIR__));
    }
}
