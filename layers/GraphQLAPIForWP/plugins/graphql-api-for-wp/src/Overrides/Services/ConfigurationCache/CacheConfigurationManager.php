<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI\Overrides\Services\ConfigurationCache;

use GraphQLAPI\GraphQLAPI\Constants\RequestParams;
use GraphQLAPI\GraphQLAPI\Facades\UserSettingsManagerFacade;
use GraphQLAPI\GraphQLAPI\PluginInfo;
use PoP\ComponentModel\Cache\CacheConfigurationManagerInterface;

/**
 * Inject configuration to the cache
 *
 * @author Leonardo Losoviz <leo@getpop.org>
 */
class CacheConfigurationManager implements CacheConfigurationManagerInterface
{
    /**
     * Save into the DB, and inject to the FilesystemAdapter:
     * A string used as the subdirectory of the root cache directory, where cache
     * items will be stored
     *
     * @see https://symfony.com/doc/current/components/cache/adapters/filesystem_adapter.html
     */
    public function getNamespace(): string
    {
        // (Needed for development) Don't share cache among plugin versions
        $timestamp = '_v' . PluginInfo::get('version');
        // The timestamp from when last saving settings/modules to the DB
        $userSettingsManager = UserSettingsManagerFacade::getInstance();
        $timestamp .= '_' . $userSettingsManager->getTimestamp();
        // admin/non-admin screens have different services enabled
        if (\is_admin()) {
            // The WordPress editor can access the full GraphQL schema,
            // including "unrestricted" admin fields, so cache it individually
            $isSchemaForEditor = ($_REQUEST[RequestParams::SCHEMA_TARGET] ?? null) === RequestParams::SCHEMA_TARGET_EDITOR;
            $timestamp .= '_' . ($isSchemaForEditor ? 'editor' : 'admin');
        }
        return $timestamp;
    }
}
