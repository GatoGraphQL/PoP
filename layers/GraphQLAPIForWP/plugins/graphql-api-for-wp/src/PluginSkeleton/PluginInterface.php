<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI\PluginSkeleton;

interface PluginInterface
{
    public function setup(): void;

    /**
     * Plugin name
     */
    public function getPluginName(): string;

    /**
     * Plugin base name
     */
    public function getPluginBaseName(): string;

    /**
     * Plugin main file
     */
    public function getPluginFile(): string;

    /**
     * Plugin version
     */
    public function getPluginVersion(): string;

    /**
     * Plugin dir
     */
    public function getPluginDir(): string;

    /**
     * Plugin URL
     */
    public function getPluginURL(): string;

    /**
     * PluginInfo class for the Plugin
     */
    public function getInfo(): ?PluginInfoInterface;
}
