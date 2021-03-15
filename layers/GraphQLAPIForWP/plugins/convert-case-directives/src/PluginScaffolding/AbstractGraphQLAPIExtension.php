<?php

declare(strict_types=1);

namespace GraphQLAPI\ConvertCaseDirectives\PluginScaffolding;

use GraphQLAPI\GraphQLAPI\Facades\Registries\SystemModuleRegistryFacade;
use GraphQLAPI\GraphQLAPI\Facades\UserSettingsManagerFacade;
use GraphQLAPI\GraphQLAPI\Plugin as GraphQLAPIPlugin;
use PoP\ComponentModel\Misc\GeneralUtils;
use PoP\Engine\AppLoader;

abstract class AbstractGraphQLAPIExtension
{
    /**
     * The plugin name
     */
    abstract protected function getPluginName(): string;

    /**
     * Indicate if the main plugin is installed and activated
     */
    final protected function isGraphQLAPIPluginActive(): bool
    {
        return class_exists('\GraphQLAPI\GraphQLAPI\Plugin');
    }

    /**
     * If the GraphQL API plugin is not installed and activated,
     * show an error for the admin
     */
    protected function addAdminNoticeError(): void
    {
        if ($errorMessage = $this->getGraphQLAPIPluginInactiveAdminNoticeErrorMessage()) {
            \add_action('admin_notices', function () use ($errorMessage) {
                \_e(sprintf(
                    '<div class="notice notice-error is-dismissible">' .
                        '<p>%s</p>' .
                    '</div>',
                    $errorMessage
                ));
            });
        }
    }

    /**
     * The message to show in the admin notices, when the GraphQL API plugin
     * is not installed or activated
     */
    protected function getGraphQLAPIPluginInactiveAdminNoticeErrorMessage(): ?string
    {
        return sprintf(
            \__('Plugin <strong>%1$s</strong> is not installed or activated. Without it, plugin <strong>%2$s</strong> cannot be enabled.'),
            \__('GraphQL API for WordPress'),
            $this->getPluginName()
        );
    }

    /**
     * Plugin set-up, executed after the GraphQL API plugin is loaded,
     * and before it is initialized
     */
    final public function setup(): void
    {
        // Functions to execute when activating/deactivating the plugin
        \register_activation_hook($this->getPluginFile(), [$this, 'activate']);
        \register_deactivation_hook($this->getPluginFile(), [$this, 'deactivate']);

        /**
         * Priority 0: before the GraphQL API plugin is initialized
         */
        \add_action(
            'plugins_loaded',
            function (): void {
                /**
                 * Check that the GraphQL API plugin is installed and activated.
                 * Otherwise show an error, and skip initializing the plugin.
                 */
                if (!$this->isGraphQLAPIPluginActive()) {
                    // Show an error message to the admin
                    $this->addAdminNoticeError();
                    // Exit
                    return;
                }

                // Execute the plugin's custom setup, if any
                $this->doSetup();

                /**
                 * Initialize/configure/boot this extension plugin
                 */
                \add_action(
                    GraphQLAPIPlugin::HOOK_INITIALIZE_EXTENSION_PLUGIN,
                    [$this, 'initialize']
                );
                \add_action(
                    GraphQLAPIPlugin::HOOK_CONFIGURE_EXTENSION_PLUGIN,
                    [$this, 'configure']
                );
                \add_action(
                    GraphQLAPIPlugin::HOOK_BOOT_EXTENSION_PLUGIN,
                    [$this, 'boot']
                );
            },
            0
        );
    }

    /**
     * Add Component classes to be initialized
     *
     * @return string[] List of `Component` class to initialize
     */
    public function getComponentClassesToInitialize(): array
    {
        return [];
    }

    /**
     * Add configuration for the Component classes
     *
     * @return array<string, mixed> [key]: Component class, [value]: Configuration
     */
    public function getComponentClassConfiguration(): array
    {
        return [];
    }

    /**
     * Add schema Component classes to skip initializing
     *
     * @return string[] List of `Component` class which must not initialize their Schema services
     */
    public function getSchemaComponentClassesToSkip(): array
    {
        return static::getSkippingSchemaComponentClasses();
    }

    /**
     * Provide the classes of the components whose
     * schema initialization must be skipped
     *
     * @return string[]
     */
    protected static function getSkippingSchemaComponentClasses(): array
    {
        $moduleRegistry = SystemModuleRegistryFacade::getInstance();

        // Component classes are skipped if the module is disabled
        $skipSchemaModuleComponentClasses = array_filter(
            static::getModuleComponentClasses(),
            function ($module) use ($moduleRegistry) {
                return !$moduleRegistry->isModuleEnabled($module);
            },
            ARRAY_FILTER_USE_KEY
        );
        return GeneralUtils::arrayFlatten(
            array_values(
                $skipSchemaModuleComponentClasses
            )
        );
    }

    /**
     * Provide the list of modules to check if they are enabled and,
     * if they are not, what component classes must skip initialization
     *
     * @return string[]
     */
    abstract protected static function getModuleComponentClasses(): array;

    /**
     * Plugin's initialization
     */
    final public function initialize(): void
    {
        /**
         * Check that the GraphQL API plugin is installed and activated.
         */
        if (!$this->isGraphQLAPIPluginActive()) {
            // Exit
            return;
        }

        // Initialize the containers
        AppLoader::addComponentClassesToInitialize(
            $this->getComponentClassesToInitialize()
        );
    }

    /**
     * Plugin's configuration
     */
    final public function configure(): void
    {
        /**
         * Check that the GraphQL API plugin is installed and activated.
         */
        if (!$this->isGraphQLAPIPluginActive()) {
            // Exit
            return;
        }

        // Only after initializing the System Container,
        // we can obtain the configuration
        // (which may depend on hooks)
        AppLoader::addComponentClassConfiguration(
            $this->getComponentClassConfiguration()
        );
        AppLoader::addSchemaComponentClassesToSkip(
            $this->getSchemaComponentClassesToSkip()
        );
        // Execute the plugin's custom config
        $this->doConfigure();
    }

    /**
     * Plugin's booting
     */
    final public function boot(): void
    {
        /**
         * Check that the GraphQL API plugin is installed and activated.
         */
        if (!$this->isGraphQLAPIPluginActive()) {
            // Exit
            return;
        }
        // Execute the plugin's custom setup
        $this->doBoot();
    }

    /**
     * Plugin set-up
     */
    protected function doSetup(): void
    {
        // Function to override
    }

    /**
     * Plugin configuration
     */
    protected function doConfigure(): void
    {
        // Function to override
    }

    /**
     * Initialize plugin. Function to override
     */
    protected function doBoot(): void
    {
    }

    /**
     * Plugin main file
     */
    abstract protected function getPluginFile(): string;

    /**
     * Get permalinks to work when activating the plugin
     *
     * @see https://codex.wordpress.org/Function_Reference/register_post_type#Flushing_Rewrite_on_Activation
     */
    public function activate(): void
    {
        if (!$this->isGraphQLAPIPluginActive()) {
            return;
        }

        // Flush rewrite rules: needed if the extension registers CPTs
        \flush_rewrite_rules();

        $this->regenerateTimestamp();
    }

    /**
     * Remove permalinks when deactivating the plugin
     *
     * @see https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/
     */
    public function deactivate(): void
    {
        if (!$this->isGraphQLAPIPluginActive()) {
            return;
        }
        $this->regenerateTimestamp();
    }

    /**
     * Regenerate the timestamp
     */
    protected function regenerateTimestamp(): void
    {
        $userSettingsManager = UserSettingsManagerFacade::getInstance();
        $userSettingsManager->storeTimestamp();
    }
}
