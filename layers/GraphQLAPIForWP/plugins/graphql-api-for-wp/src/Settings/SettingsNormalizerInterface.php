<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI\Settings;

interface SettingsNormalizerInterface
{
    /**
     * Normalize the form values:
     *
     * - If the input is empty, replace with the default
     * - Convert from string to int/bool
     *
     * @param array<string,string> $values All values submitted, each under its optionName as key
     * @return array<string,mixed> Normalized values
     */
    public function normalizeSettingsByCategory(
        array $values,
        string $settingsCategory,
    ): array;
    /**
     * Normalize the form values:
     *
     * - If the input is empty, replace with the default
     * - Convert from string to int/bool
     *
     * @param array<string,string> $values All values submitted, each under its optionName as key
     * @return array<string,mixed> Normalized values
     */
    public function normalizeSettingsByModule(
        array $values,
        string $module,
    ): array;
    /**
     * Return all the modules with settings
     *
     * @return array<array<string,mixed>> Each item is an array of prop => value
     */
    public function getAllSettingsItems(?string $settingsCategory = null): array;
}
