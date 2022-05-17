<?php

declare(strict_types=1);

namespace PoP\Root\Module;

use PoP\Root\App;
use PoP\Root\Helpers\ClassHelpers;

abstract class AbstractModule implements ModuleInterface
{
    use InitializeContainerServicesInModuleTrait;

    /**
     * Indicate what other component satisfies the contracts
     * by this component.
     *
     * For instance, the packages under CMSSchema have generic contracts
     * for any CMS, that require to be satisfied for some specific CMS
     * (such as WordPress).
     */
    private ?ModuleInterface $satisfyingComponent = null;
    private ?bool $enabled = null;
    protected ?ModuleConfigurationInterface $moduleConfiguration = null;
    protected ?ModuleInfoInterface $componentInfo = null;

    /**
     * Enable each component to set default configuration for
     * itself and its depended components
     *
     * @param array<string, mixed> $componentClassConfiguration
     */
    public function customizeComponentClassConfiguration(
        array &$componentClassConfiguration
    ): void {
    }

    /**
     * Initialize the component
     *
     * @param array<string, mixed> $configuration
     * @param boolean $skipSchema Indicate if to skip initializing the schema
     * @param string[] $skipSchemaComponentClasses
     */
    final public function initialize(
        array $configuration,
        bool $skipSchema,
        array $skipSchemaComponentClasses,
    ): void {
        // Set the configuration on the corresponding ModuleConfiguration
        $this->initializeConfiguration($configuration);

        // Have the Module set its own info on the corresponding ModuleInfo
        $this->initializeInfo();

        // Initialize the self component
        $this->initializeContainerServices($skipSchema, $skipSchemaComponentClasses);

        // Allow the component to define runtime constants
        $this->defineRuntimeConstants($skipSchema, $skipSchemaComponentClasses);
    }

    /**
     * Initialize services for the system container
     */
    final public function initializeSystem(): void
    {
        $this->initializeSystemContainerServices();
    }

    /**
     * Initialize services for the system container
     */
    protected function initializeSystemContainerServices(): void
    {
        // Override
    }

    /**
     * Indicate if this component requires some other component
     * to satisfy its contracts.
     *
     * For instance, the packages under CMSSchema have generic contracts
     * for any CMS, that require to be satisfied for some specific CMS
     * (such as WordPress).
     */
    protected function requiresSatisfyingComponent(): bool
    {
        return false;
    }

    /**
     * Indicate what other component satisfies the contracts by this component.
     */
    public function setSatisfyingComponent(ModuleInterface $component): void
    {
        $this->satisfyingComponent = $component;
    }

    /**
     * All component classes that this component satisfies
     *
     * @return string[]
     */
    public function getSatisfiedComponentClasses(): array
    {
        return [];
    }

    /**
     * All component classes that this component depends upon, to initialize them
     *
     * @return string[]
     */
    abstract public function getDependedComponentClasses(): array;

    /**
     * All DEV component classes that this component depends upon, to initialize them
     *
     * @return string[]
     */
    public function getDevDependedComponentClasses(): array
    {
        return [];
    }

    /**
     * All DEV PHPUnit component classes that this component depends upon, to initialize them
     *
     * @return string[]
     */
    public function getDevPHPUnitDependedComponentClasses(): array
    {
        return [];
    }

    /**
     * All conditional component classes that this component depends upon, to initialize them
     *
     * @return string[]
     */
    public function getDependedConditionalComponentClasses(): array
    {
        return [];
    }

    /**
     * Compiler Passes for the System Container
     *
     * @return string[]
     */
    public function getSystemContainerCompilerPassClasses(): array
    {
        return [];
    }

    /**
     * Initialize services
     *
     * @param string[] $skipSchemaComponentClasses
     */
    protected function initializeContainerServices(
        bool $skipSchema,
        array $skipSchemaComponentClasses,
    ): void {
    }

    /**
     * Define runtime constants
     */
    protected function defineRuntimeConstants(
        bool $skipSchema,
        array $skipSchemaComponentClasses
    ): void {
    }

    /**
     * Function called by the Bootloader before booting the system
     */
    public function configure(): void
    {
    }

    /**
     * Function called by the Bootloader when booting the system
     */
    public function bootSystem(): void
    {
    }

    /**
     * Function called by the Bootloader after all components have been loaded
     */
    public function componentLoaded(): void
    {
    }

    /**
     * Function called by the Bootloader when booting the system
     */
    public function boot(): void
    {
    }

    /**
     * Function called by the Bootloader when booting the system
     */
    public function afterBoot(): void
    {
    }

    /**
     * Indicates if the Module is enabled
     */
    public function isEnabled(): bool
    {
        if ($this->enabled === null) {
            $this->enabled = $this->calculateIsEnabled(false);
        }
        return $this->enabled;
    }

    /**
     * Calculate if the component must be enabled or not.
     *
     * @param boolean $ignoreDependencyOnSatisfiedComponents Indicate if to check if the satisfied component is resolved or not. Needed to avoid circular references to enable both satisfying and satisfied components.
     */
    public function calculateIsEnabled(bool $ignoreDependencyOnSatisfiedComponents): bool
    {
        /**
         * Check that there is some other component that satisfies
         * the contracts of this component (if required), and
         * that this components is itself enabled.
         *
         * The satisfying component depends on the satisfied component,
         * and the other way around too. To avoid circular recursions
         * there is param $ignoreDependencyOnSatisfiedComponents.
         */
        if ($this->requiresSatisfyingComponent()) {
            if ($this->satisfyingComponent === null) {
                return false;
            }
            if (!$this->satisfyingComponent->calculateIsEnabled(true)) {
                return false;
            }
        }

        // If any dependency is disabled, then disable this component too
        if ($this->onlyEnableIfAllDependenciesAreEnabled()) {
            $satisfiedComponentClasses = $this->getSatisfiedComponentClasses();
            foreach ($this->getDependedComponentClasses() as $dependedComponentClass) {
                if ($ignoreDependencyOnSatisfiedComponents && in_array($dependedComponentClass, $satisfiedComponentClasses)) {
                    continue;
                }
                $dependedComponent = App::getModule($dependedComponentClass);
                if (!$dependedComponent->isEnabled()) {
                    return false;
                }
            }
        }

        return $this->resolveEnabled();
    }

    public function onlyEnableIfAllDependenciesAreEnabled(): bool
    {
        return true;
    }

    protected function resolveEnabled(): bool
    {
        return true;
    }

    /**
     * Indicates if the Module must skipSchema
     */
    public function skipSchema(): bool
    {
        return false;
    }

    /**
     * ModuleConfiguration class for the Module
     */
    public function getConfiguration(): ?ModuleConfigurationInterface
    {
        return $this->moduleConfiguration;
    }

    /**
     * ModuleInfo class for the Module
     */
    public function getInfo(): ?ModuleInfoInterface
    {
        return $this->componentInfo;
    }

    /**
     * @param array<string,mixed> $configuration
     */
    protected function initializeConfiguration(array $configuration): void
    {
        $moduleConfigurationClass = $this->getModuleConfigurationClass();
        if ($moduleConfigurationClass === null) {
            return;
        }
        $this->moduleConfiguration = new $moduleConfigurationClass($configuration);
    }

    /**
     * ModuleConfiguration class for the Module
     */
    protected function getModuleConfigurationClass(): ?string
    {
        $classNamespace = ClassHelpers::getClassPSR4Namespace(\get_called_class());
        $moduleConfigurationClass = $classNamespace . '\\ModuleConfiguration';
        if (!class_exists($moduleConfigurationClass)) {
            return null;
        }
        return $moduleConfigurationClass;
    }

    protected function initializeInfo(): void
    {
        $moduleInfoClass = $this->getModuleInfoClass();
        if ($moduleInfoClass === null) {
            return;
        }
        $this->componentInfo = new $moduleInfoClass($this);
    }

    /**
     * ModuleInfo class for the Module
     */
    protected function getModuleInfoClass(): ?string
    {
        $classNamespace = ClassHelpers::getClassPSR4Namespace(\get_called_class());
        $moduleInfoClass = $classNamespace . '\\ModuleInfo';
        if (!class_exists($moduleInfoClass)) {
            return null;
        }
        return $moduleInfoClass;
    }
}
