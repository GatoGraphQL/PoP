<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer;

use PoP\API\ComponentConfiguration as APIComponentConfiguration;
use PoP\ComponentModel\ComponentConfiguration\EnvironmentValueHelpers;

class ComponentConfiguration extends \PoP\BasicService\Component\AbstractComponentConfiguration
{
    private static bool $exposeSelfFieldForRootTypeInGraphQLSchema = false;
    private static bool $sortGraphQLSchemaAlphabetically = true;
    private static bool $enableProactiveFeedback = true;
    private static bool $enableProactiveFeedbackDeprecations = true;
    private static bool $enableProactiveFeedbackNotices = true;
    private static bool $enableProactiveFeedbackTraces = true;
    private static bool $enableProactiveFeedbackLogs = true;
    private static bool $enableNestedMutations = false;
    private static ?bool $enableGraphQLIntrospection = null;
    private static bool $exposeSelfFieldInGraphQLSchema = false;
    private static bool $addFullSchemaFieldToGraphQLSchema = false;
    private static bool $addVersionToGraphQLSchemaFieldDescription = false;
    private static bool $enableSettingMutationSchemeByURLParam = false;
    private static bool $enableEnablingGraphQLIntrospectionByURLParam = false;
    private static bool $addGraphQLIntrospectionPersistedQuery = false;
    private static bool $addConnectionFromRootToQueryRootAndMutationRoot = false;
    private static bool $exposeSchemaIntrospectionFieldInSchema = false;
    private static bool $exposeGlobalFieldsInGraphQLSchema = false;

    public static function exposeSelfFieldForRootTypeInGraphQLSchema(): bool
    {
        // Define properties
        $envVariable = Environment::EXPOSE_SELF_FIELD_FOR_ROOT_TYPE_IN_GRAPHQL_SCHEMA;
        $selfProperty = &self::$exposeSelfFieldForRootTypeInGraphQLSchema;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function sortGraphQLSchemaAlphabetically(): bool
    {
        // Define properties
        $envVariable = Environment::SORT_GRAPHQL_SCHEMA_ALPHABETICALLY;
        $selfProperty = &self::$sortGraphQLSchemaAlphabetically;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableProactiveFeedback(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_PROACTIVE_FEEDBACK;
        $selfProperty = &self::$enableProactiveFeedback;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableProactiveFeedbackDeprecations(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_PROACTIVE_FEEDBACK_DEPRECATIONS;
        $selfProperty = &self::$enableProactiveFeedbackDeprecations;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableProactiveFeedbackNotices(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_PROACTIVE_FEEDBACK_NOTICES;
        $selfProperty = &self::$enableProactiveFeedbackNotices;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableProactiveFeedbackTraces(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_PROACTIVE_FEEDBACK_TRACES;
        $selfProperty = &self::$enableProactiveFeedbackTraces;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableProactiveFeedbackLogs(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_PROACTIVE_FEEDBACK_LOGS;
        $selfProperty = &self::$enableProactiveFeedbackLogs;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableNestedMutations(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_NESTED_MUTATIONS;
        $selfProperty = &self::$enableNestedMutations;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableGraphQLIntrospection(): ?bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_GRAPHQL_INTROSPECTION;
        $selfProperty = &self::$enableGraphQLIntrospection;
        $defaultValue = null;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function exposeSelfFieldInGraphQLSchema(): bool
    {
        // Define properties
        $envVariable = Environment::EXPOSE_SELF_FIELD_IN_GRAPHQL_SCHEMA;
        $selfProperty = &self::$exposeSelfFieldInGraphQLSchema;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function addFullSchemaFieldToGraphQLSchema(): bool
    {
        // Define properties
        $envVariable = Environment::ADD_FULLSCHEMA_FIELD_TO_GRAPHQL_SCHEMA;
        $selfProperty = &self::$addFullSchemaFieldToGraphQLSchema;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function addVersionToGraphQLSchemaFieldDescription(): bool
    {
        // Define properties
        $envVariable = Environment::ADD_VERSION_TO_GRAPHQL_SCHEMA_FIELD_DESCRIPTION;
        $selfProperty = &self::$addVersionToGraphQLSchemaFieldDescription;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableSettingMutationSchemeByURLParam(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_SETTING_MUTATION_SCHEME_BY_URL_PARAM;
        $selfProperty = &self::$enableSettingMutationSchemeByURLParam;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function enableEnablingGraphQLIntrospectionByURLParam(): bool
    {
        // Define properties
        $envVariable = Environment::ENABLE_ENABLING_GRAPHQL_INTROSPECTION_BY_URL_PARAM;
        $selfProperty = &self::$enableEnablingGraphQLIntrospectionByURLParam;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function addGraphQLIntrospectionPersistedQuery(): bool
    {
        // Define properties
        $envVariable = Environment::ADD_GRAPHQL_INTROSPECTION_PERSISTED_QUERY;
        $selfProperty = &self::$addGraphQLIntrospectionPersistedQuery;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function addConnectionFromRootToQueryRootAndMutationRoot(): bool
    {
        // Define properties
        $envVariable = Environment::ADD_CONNECTION_FROM_ROOT_TO_QUERYROOT_AND_MUTATIONROOT;
        $selfProperty = &self::$addConnectionFromRootToQueryRootAndMutationRoot;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function exposeSchemaIntrospectionFieldInSchema(): bool
    {
        // Define properties
        $envVariable = Environment::EXPOSE_SCHEMA_INTROSPECTION_FIELD_IN_SCHEMA;
        $selfProperty = &self::$exposeSchemaIntrospectionFieldInSchema;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function exposeGlobalFieldsInGraphQLSchema(): bool
    {
        if (APIComponentConfiguration::skipExposingGlobalFieldsInFullSchema()) {
            return false;
        }

        // Define properties
        $envVariable = Environment::EXPOSE_GLOBAL_FIELDS_IN_GRAPHQL_SCHEMA;
        $selfProperty = &self::$exposeGlobalFieldsInGraphQLSchema;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }
}
