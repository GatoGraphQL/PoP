<?php

declare(strict_types=1);

namespace PoP\Root;

use PoP\Root\Container\ContainerBuilderFactory;
use PoP\Root\Container\SystemContainerBuilderFactory;
use PoP\Root\Dotenv\DotenvBuilderFactory;
use PoP\Root\Managers\ComponentManager;

/**
 * Component Loader
 */
class AppLoader
{
    /**
     * Has the component been initialized?
     *
     * @var string[]
     */
    protected static $initializedClasses = [];
    /**
     * Component classes to be initialized
     *
     * @var string[]
     */
    protected static $componentClassesToInitialize = [];
    /**
     * Calculare in what order the Component classes must be initialized,
     * based on their dependencies on other Components
     *
     * @var string[]
     */
    protected static $orderedComponentClasses = [];
    /**
     * [key]: Component class, [value]: Configuration
     *
     * @var string[]
     */
    protected static $componentClassConfiguration = [];
    /**
     * List of `Component` class which must not initialize their Schema services
     *
     * @var string[]
     */
    protected static $skipSchemaComponentClasses = [];

    /**
     * Add Component classes to be initialized
     *
     * @param string[] $componentClasses List of `Component` class to initialize
     * @param array<string, mixed> $componentClassConfiguration [key]: Component class, [value]: Configuration
     * @param string[] $skipSchemaComponentClasses List of `Component` class which must not initialize their Schema services
     */
    public static function addComponentClassesToInitialize(
        array $componentClasses,
        array $componentClassConfiguration = [],
        array $skipSchemaComponentClasses = []
    ): void {
        self::$componentClassesToInitialize = array_merge(
            self::$componentClassesToInitialize,
            $componentClasses
        );
        self::$componentClassConfiguration = array_merge_recursive(
            self::$componentClassConfiguration,
            $componentClassConfiguration
        );
        self::$skipSchemaComponentClasses = array_merge(
            self::$skipSchemaComponentClasses,
            $skipSchemaComponentClasses
        );
    }

    /**
     * Get the array of components ordered by how they must be initialized,
     * following the Composer dependencies tree
     *
     * @param string[] $componentClasses List of `Component` class to initialize
     * @return string[]
     */
    protected static function getComponentsOrderedForInitialization(
        array $componentClasses
    ): array {
        $orderedComponentClasses = [];
        self::addComponentsOrderedForInitialization(
            $componentClasses,
            $orderedComponentClasses
        );
        return $orderedComponentClasses;
    }

    /**
     * Get the array of components ordered by how they must be initialized,
     * following the Composer dependencies tree
     *
     * @param string[] $componentClasses List of `Component` class to initialize
     * @param string[] $orderedComponentClasses List of `Component` class in order of initialization
     */
    protected static function addComponentsOrderedForInitialization(
        array $componentClasses,
        array &$orderedComponentClasses
    ): void {
        /**
         * If any component class has already been initialized,
         * then do nothing
         */
        $componentClasses = array_values(array_diff(
            $componentClasses,
            self::$initializedClasses
        ));
        foreach ($componentClasses as $componentClass) {
            self::$initializedClasses[] = $componentClass;

            // Initialize all depended-upon PoP components
            self::addComponentsOrderedForInitialization(
                $componentClass::getDependedComponentClasses(),
                $orderedComponentClasses
            );

            // Initialize all depended-upon PoP conditional components, if they are installed
            self::addComponentsOrderedForInitialization(
                array_filter(
                    $componentClass::getDependedConditionalComponentClasses(),
                    'class_exists'
                ),
                $orderedComponentClasses
            );

            // We reached the bottom of the rung, add the component to the list
            $orderedComponentClasses[] = $componentClass;
        }
    }

    /**
     * Boot the application
     *
     * @param boolean|null $cacheContainerConfiguration Indicate if to cache the container. If null, it gets the value from ENV
     * @param boolean|null $namespace Provide the namespace, to regenerate the cache whenever the application is upgraded. If null, it gets the value from ENV
     */
    public static function bootApplication(
        ?bool $cacheContainerConfiguration = null,
        ?string $containerNamespace = null
    ): void {
        // Initialize Dotenv (before the ContainerBuilder, since this one uses environment constants)
        DotenvBuilderFactory::init();

        /**
         * System container: initialize it and compile it already,
         * since it will be used to initialize the Application container
         */
        SystemContainerBuilderFactory::init(
            $cacheContainerConfiguration,
            $containerNamespace
        );

        /**
         * Calculate the components in their initialization order
         */
        self::$orderedComponentClasses = self::getComponentsOrderedForInitialization(
            self::$componentClassesToInitialize
        );

        /**
         * Have all Components register their Container services,
         * and already compile the container.
         * This way, these services become available for initializing
         * Application Container services.
         */
        foreach (self::$orderedComponentClasses as $componentClass) {
            $componentConfiguration = self::$componentClassConfiguration[$componentClass] ?? [];
            $componentClass::initializeSystemContainerServices(
                $componentConfiguration
            );
        }
        SystemContainerBuilderFactory::maybeCompileAndCacheContainer();

        /**
         * Initialize the Application container only
         */
        ContainerBuilderFactory::init(
            $cacheContainerConfiguration,
            $containerNamespace
        );

        /**
         * Allow each component to customize the configuration for itself,
         * and for its depended-upon components.
         * Hence this is executed from bottom to top
         */
        foreach (array_reverse(self::$orderedComponentClasses) as $componentClass) {
            $componentClass::customizeComponentClassConfiguration(self::$componentClassConfiguration);
        }

        /**
         * Initialize the container services by the Components
         */
        foreach (self::$orderedComponentClasses as $componentClass) {
            // Temporary solution until migrated:
            // Initialize all depended-upon migration plugins
            foreach ($componentClass::getDependedMigrationPlugins() as $migrationPluginPath) {
                require_once $migrationPluginPath;
            }

            // Initialize the component, passing its configuration, and checking if its schema must be skipped
            $componentConfiguration = self::$componentClassConfiguration[$componentClass] ?? [];
            $skipSchemaForComponent = in_array($componentClass, self::$skipSchemaComponentClasses);
            $componentClass::initialize(
                $componentConfiguration,
                $skipSchemaForComponent,
                self::$skipSchemaComponentClasses
            );
        }

        // Register CompilerPasses, Compile and Cache
        // Symfony's DependencyInjection Application Container
        $compilerPassClasses = self::getApplicationContainerCompilerPasses();
        ContainerBuilderFactory::maybeCompileAndCacheContainer($compilerPassClasses);

        // Finally boot the components
        self::bootComponents();
    }

    /**
     * @return string[]
     */
    protected static function getApplicationContainerCompilerPasses(): array
    {
        // Collect the compiler pass classes from all components
        $compilerPassClasses = [];
        foreach (ComponentManager::getComponentClasses() as $componentClass) {
            $compilerPassClasses = [
                ...$compilerPassClasses,
                ...$componentClass::getContainerCompilerPassClasses()
            ];
        }
        return array_values(array_unique($compilerPassClasses));
    }

    protected static function bootComponents(): void
    {
        ComponentManager::beforeBoot();
        ComponentManager::boot();
        ComponentManager::afterBoot();
    }
}
