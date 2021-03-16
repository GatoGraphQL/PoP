<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI;

use GraphQLAPI\GraphQLAPI\Admin\TableActions\ModuleListTableAction;
use GraphQLAPI\GraphQLAPI\Constants\RequestParams;
use GraphQLAPI\GraphQLAPI\Facades\Registries\ModuleRegistryFacade;
use GraphQLAPI\GraphQLAPI\Facades\UserSettingsManagerFacade;
use GraphQLAPI\GraphQLAPI\HybridServices\ModuleResolvers\PluginManagementFunctionalityModuleResolver;
use GraphQLAPI\GraphQLAPI\PluginConfiguration;
use GraphQLAPI\GraphQLAPI\Registries\ModuleRegistry;
use GraphQLAPI\GraphQLAPI\Security\UserAuthorization;
use GraphQLAPI\GraphQLAPI\Services\Helpers\EndpointHelpers;
use GraphQLAPI\GraphQLAPI\Services\Helpers\MenuPageHelper;
use GraphQLAPI\GraphQLAPI\Services\MenuPages\AboutMenuPage;
use GraphQLAPI\GraphQLAPI\Services\MenuPages\ModulesMenuPage;
use GraphQLAPI\GraphQLAPI\Services\MenuPages\SettingsMenuPage;
use GraphQLAPI\GraphQLAPI\Services\Menus\Menu;
use GraphQLAPI\PluginSkeleton\AbstractMainPlugin;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\Engine\AppLoader;

class Plugin extends AbstractMainPlugin
{
    /**
     * Plugin's namespace
     */
    public const NAMESPACE = __NAMESPACE__;

    /**
     * Store the plugin version in the Options table, to track when
     * the plugin is installed/updated
     */
    public const OPTION_PLUGIN_VERSION = 'graphql-api-plugin-version';

    /**
     * Plugin set-up, executed immediately when loading the plugin.
     */
    public function setup(): void
    {
        parent::setup();

        // Functions to execute when activating/deactivating the plugin
        \register_deactivation_hook($this->getPluginFile(), [$this, 'deactivate']);
        /**
         * PoP depends on hook "init" to set-up the endpoint rewrite,
         * as in function `addRewriteEndpoints` in `AbstractEndpointHandler`
         * However, activating the plugin takes place AFTER hooks "plugins_loaded"
         * and "init". Hence, the code cannot flush the rewrite_rules when the plugin
         * is activated, and any non-default GraphQL endpoint is not set.
         *
         * The solution (hack) is to check if the plugin has just been installed,
         * and then apply the logic, on every request in the admin!
         *
         * @see https://developer.wordpress.org/reference/functions/register_activation_hook/#process-flow
         */
        \register_activation_hook(
            $this->getPluginFile(),
            function (): void {
                // By removing the option (in case it already exists from a previously-installed version),
                // the next request will know the plugin was just installed
                \update_option(self::OPTION_PLUGIN_VERSION, false);
                // This is the proper activation logic
                $this->activate();
            }
        );
        \add_action(
            'admin_init',
            function (): void {
                // If there is no version stored, it's the first screen after activating the plugin
                $isPluginJustActivated = \get_option(self::OPTION_PLUGIN_VERSION) === false;
                if (!$isPluginJustActivated) {
                    return;
                }
                // Update to the current version
                \update_option(self::OPTION_PLUGIN_VERSION, \GRAPHQL_API_VERSION);
                // Required logic after plugin is activated
                \flush_rewrite_rules();
            }
        );
        /**
         * Show an admin notice with a link to the latest release notes
         */
        \add_action(
            'admin_init',
            function (): void {
                // Do not execute when doing Ajax, since we can't show the one-time
                // admin notice to the user then
                if (\wp_doing_ajax()) {
                    return;
                }
                // Check if the plugin has been updated: if the stored version in the DB
                // and the current plugin's version are different
                // It could also be false from the first time we install the plugin
                $storedVersion = \get_option(self::OPTION_PLUGIN_VERSION, \GRAPHQL_API_VERSION);
                if (!$storedVersion || $storedVersion == \GRAPHQL_API_VERSION) {
                    return;
                }
                // Update to the current version
                \update_option(self::OPTION_PLUGIN_VERSION, \GRAPHQL_API_VERSION);
                // Admin notice: Check if it is enabled
                $userSettingsManager = UserSettingsManagerFacade::getInstance();
                if (
                    !$userSettingsManager->getSetting(
                        PluginManagementFunctionalityModuleResolver::GENERAL,
                        PluginManagementFunctionalityModuleResolver::OPTION_ADD_RELEASE_NOTES_ADMIN_NOTICE
                    )
                ) {
                    return;
                }
                // Show admin notice only when updating MAJOR or MINOR versions. No need for PATCH versions
                $currentMinorReleaseVersion = $this->getMinorReleaseVersion(\GRAPHQL_API_VERSION);
                $previousMinorReleaseVersion = $this->getMinorReleaseVersion($storedVersion);
                if ($currentMinorReleaseVersion == $previousMinorReleaseVersion) {
                    return;
                }
                // All checks passed, show the release notes
                $this->showReleaseNotesInAdminNotice();
            }
        );
    }

    /**
     * Add a notice with a link to the latest release note,
     * to open in a modal window
     */
    protected function showReleaseNotesInAdminNotice(): void
    {
        // Load the assets to open in a modal
        \add_action('admin_enqueue_scripts', function () {
            /**
             * Hack to open the modal thickbox iframe with the documentation
             */
            \wp_enqueue_style(
                'thickbox'
            );
            \wp_enqueue_script(
                'plugin-install'
            );
        });
        // Add the admin notice
        \add_action('admin_notices', function () {
            $instanceManager = InstanceManagerFacade::getInstance();
            /**
             * @var AboutMenuPage
             */
            $aboutMenuPage = $instanceManager->getInstance(AboutMenuPage::class);
            // Calculate the minor release version.
            // Eg: if current version is 0.6.3, minor version is 0.6
            $minorReleaseVersion = $this->getMinorReleaseVersion(\GRAPHQL_API_VERSION);
            $releaseNotesURL = \admin_url(sprintf(
                'admin.php?page=%s&%s=%s&%s=%s&TB_iframe=true',
                $aboutMenuPage->getScreenID(),
                RequestParams::TAB,
                RequestParams::TAB_DOCS,
                RequestParams::DOC,
                sprintf(
                    'release-notes/%s',
                    $minorReleaseVersion
                )
            ));
            /**
             * @var SettingsMenuPage
             */
            $settingsMenuPage = $instanceManager->getInstance(SettingsMenuPage::class);
            $moduleRegistry = ModuleRegistryFacade::getInstance();
            $generalSettingsURL = \admin_url(sprintf(
                'admin.php?page=%s&tab=%s',
                $settingsMenuPage->getScreenID(),
                $moduleRegistry
                    ->getModuleResolver(PluginManagementFunctionalityModuleResolver::GENERAL)
                    ->getID(PluginManagementFunctionalityModuleResolver::GENERAL)
            ));
            _e(sprintf(
                '<div class="notice notice-success is-dismissible">' .
                '<p>%s</p>' .
                '</div>',
                sprintf(
                    __('Plugin <strong>GraphQL API for WordPress</strong> has been updated to version <code>%s</code>. <strong><a href="%s" class="%s">Check out what\'s new</a></strong> | <a href="%s">Disable this admin notice in the Settings</a>', 'graphql-api'),
                    \GRAPHQL_API_VERSION,
                    $releaseNotesURL,
                    'thickbox open-plugin-details-modal',
                    $generalSettingsURL
                )
            ));
        });
    }

    /**
     * Given a version in semver (MAJOR.MINOR.PATCH),
     * return the minor version (MAJOR.MINOR)
     */
    protected function getMinorReleaseVersion(string $version): string
    {
        $versionParts = explode('.', $version);
        return $versionParts[0] . '.' . $versionParts[1];
    }

    /**
     * Add Component classes to be initialized
     *
     * @return string[] List of `Component` class to initialize
     */
    public function getComponentClassesToInitialize(): array
    {
        return [
            Component::class,
        ];
    }

    /**
     * Add configuration for the Component classes
     *
     * @return array<string, mixed> [key]: Component class, [value]: Configuration
     */
    public function getComponentClassConfiguration(): array
    {
        return PluginConfiguration::getComponentClassConfiguration();
    }

    /**
     * Add schema Component classes to skip initializing
     *
     * @return string[] List of `Component` class which must not initialize their Schema services
     */
    public function getSchemaComponentClassesToSkip(): array
    {
        return PluginConfiguration::getSkippingSchemaComponentClasses();
    }

    /**
     * Plugin initialization, executed on hook "plugins_loaded"
     * to wait for all extensions to be loaded
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        /**
         * Watch out! If we are in the Modules page and enabling/disabling
         * a module, then already take that new state!
         * This is because `maybeProcessAction`, which is where modules are
         * enabled/disabled, must be executed before PluginConfiguration::initialize(),
         * which is where the plugin reads if a module is enabled/disabled as to
         * set the environment constants.
         *
         * This is mandatory, because only when it is enabled, can a module
         * have its state persisted when calling `flush_rewrite`
         */
        if (\is_admin()) {
            // We can't use the InstanceManager, since at this stage it hasn't
            // been initialized yet
            // We can create a new instance of MenuPageHelper and ModulesMenuPage
            // because their instantiation produces no side-effects
            // (maybe that happens under `initialize`)
            $menuPageHelper = new MenuPageHelper();
            $moduleRegistry = new ModuleRegistry();
            $userAuthorization = new UserAuthorization();
            $menu = new Menu($menuPageHelper, $moduleRegistry, $userAuthorization);
            $endpointHelpers = new EndpointHelpers($menu, $moduleRegistry);
            $modulesMenuPage = new ModulesMenuPage($menu, $menuPageHelper, $endpointHelpers);
            if (
                (isset($_GET['page']) && $_GET['page'] == $modulesMenuPage->getScreenID())
                && !$menuPageHelper->isDocumentationScreen()
            ) {
                // Instantiating ModuleListTableAction DOES have side-effects,
                // but they are needed, and won't be repeated when instantiating
                // the class through the Container Builder
                $moduleListTable = new ModuleListTableAction();
                $moduleListTable->maybeProcessAction();
            }
        }
    }

    /**
     * Boot the system
     */
    public function bootSystem(): void
    {
        // Boot all PoP components, from this plugin and all extensions
        AppLoader::bootSystem(
            ...PluginConfiguration::getContainerCacheConfiguration()
        );
    }

    /**
     * Configure the plugin.
     * This defines hooks to set environment variables,
     * so must be executed before those hooks are triggered for first time
     * (in ComponentConfiguration classes)
     */
    protected function callPluginConfiguration(): void
    {
        PluginConfiguration::initialize();
    }

    /**
     * Boot the application
     */
    public function bootApplication(): void
    {
        // Boot all PoP components, from this plugin and all extensions
        AppLoader::bootApplication(
            ...PluginConfiguration::getContainerCacheConfiguration()
        );
    }

    /**
     * Get permalinks to work when activating the plugin
     *
     * @see    https://codex.wordpress.org/Function_Reference/register_post_type#Flushing_Rewrite_on_Activation
     * @return void
     */
    public function activate(): void
    {
        // Initialize the timestamp
        $this->regenerateTimestamp();
    }

    /**
     * Remove permalinks when deactivating the plugin
     *
     * @see https://developer.wordpress.org/plugins/plugin-basics/activation-deactivation-hooks/
     */
    public function deactivate(): void
    {
        $this->unregisterPluginCustomPostTypes();

        // Remove the timestamp
        $this->removeTimestamp();
    }

    /**
     * Regenerate the timestamp
     */
    protected function removeTimestamp(): void
    {
        $userSettingsManager = UserSettingsManagerFacade::getInstance();
        $userSettingsManager->removeTimestamp();
    }
}
