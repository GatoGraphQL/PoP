<?php

declare(strict_types=1);

namespace GatoGraphQL\GatoGraphQL\Marketplace;

use GatoGraphQL\GatoGraphQL\Module;
use GatoGraphQL\GatoGraphQL\ModuleConfiguration;
use GatoGraphQL\GatoGraphQL\PluginApp;
use PoP\ComponentModel\App;
use PoP\Root\Exception\ShouldNotHappenException;
use PoP\Root\Services\BasicServiceTrait;

/**
 * Copied code from `Make-Lemonade/lemonsqueezy-wp-updater-example`
 *
 * @see https://github.com/Make-Lemonade/lemonsqueezy-wp-updater-example
 * @see https://github.com/Make-Lemonade/lemonsqueezy-wp-updater-example/blob/7c0c71876309939b07d96e270f4db8568f3148cb/includes/class-plugin-updater.php
 */
abstract class AbstractMarketplaceProviderCommercialPluginUpdaterService implements MarketplaceProviderCommercialPluginUpdaterServiceInterface
{
    use BasicServiceTrait;

	protected bool $initialized = false;

    /**
     * @var array<string,array{id:string,version:string,licenseKey:string}> Key: plugin slug, Value: array of entries: id, version, license key
     */
    protected array $pluginSlugDataEntries = [];

	protected string $apiURL;

	/**
     * @var array<string,array{id:string,version:string}> Key: plugin slug, Value: Cache key
     */
    protected array $pluginSlugCacheKeys = [];

	/**
	 * Only disable this for debugging
	 */
	protected bool $cacheAllowed = true;

    /**
     * Use the Marketplace provider's service to
     * update the active commercial extensions
     *
     * @param array<string,string> $licenseKeys Key: Extension Slug, Value: License Key
     *
     * @throws ShouldNotHappenException If initializing the service more than once
     */
    public function setupMarketplacePluginUpdaterForExtensions(
        array $licenseKeys,
    ): void {
        if ($this->initialized) {
            throw new ShouldNotHappenException('This service must not be initialized more than once');
        }
        $this->initialized = true;

        if ($licenseKeys === []) {
            return;
        }

        /** @var ModuleConfiguration */
        $moduleConfiguration = App::getModule(Module::class)->getConfiguration();
        $this->apiURL = $this->providePluginUpdatesAPIURL($moduleConfiguration->getMarketplaceProviderPluginUpdatesServerURL());

        /**
         * Generate the entries for all the commercial plugins,
         * possibly including the main Plugin too
         */
        $mainPlugin = PluginApp::getMainPlugin();
        $extensionManager = PluginApp::getExtensionManager();
        $pluginBaseNameInstances = array_merge(
            $mainPlugin, // @todo Check if it's necessary to compare the main plugin
            $extensionManager->getExtensions(),
        );
        
        foreach ($licenseKeys as $pluginSlug => $pluginLicenseKey) {
            foreach ($pluginBaseNameInstances as $pluginBaseName => $extensionInstance) {
                if ($extensionInstance->getPluginSlug() !== $pluginSlug) {
                    continue;
                }
            }
            $this->pluginSlugDataEntries[$pluginSlug] = [
                'id' => $pluginBaseName,
                'version' => $extensionInstance->getPluginVersion(),
                'licenseKey' => $pluginLicenseKey,
            ];
            $this->pluginSlugCacheKeys[$pluginSlug] = str_replace('-', '_', $pluginSlug) . '_updater';
        }

		add_filter('plugins_api', $this->info(...), 20, 3);
		add_filter('site_transient_update_plugins', $this->update(...));
		add_action('upgrader_process_complete', $this->purge(...), 10, 2);
    }

    abstract protected function providePluginUpdatesAPIURL(string $pluginUpdatesServerURL): string;

}