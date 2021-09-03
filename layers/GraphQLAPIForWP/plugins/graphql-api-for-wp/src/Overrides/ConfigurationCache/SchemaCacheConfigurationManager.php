<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI\Overrides\ConfigurationCache;

use GraphQLAPI\GraphQLAPI\Facades\UserSettingsManagerFacade;
use GraphQLAPI\GraphQLAPI\ConfigurationCache\AbstractCacheConfigurationManager;

class SchemaCacheConfigurationManager extends AbstractCacheConfigurationManager
{
    /**
     * The timestamp from when last saving settings/modules to the DB
     */
    protected function getTimestamp(): int
    {
        $userSettingsManager = UserSettingsManagerFacade::getInstance();
        return $userSettingsManager->getSchemaTimestamp();
    }

    /**
     * Cache under the plugin's cache/ subfolder
     */
    protected function getDirectoryName(): string
    {
        return 'operational';
    }
}
