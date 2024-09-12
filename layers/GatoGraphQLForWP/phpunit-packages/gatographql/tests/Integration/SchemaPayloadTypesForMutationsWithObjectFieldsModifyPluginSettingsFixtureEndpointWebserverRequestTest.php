<?php

declare(strict_types=1);

namespace PHPUnitForGatoGraphQL\GatoGraphQL\Integration;

use GatoGraphQL\GatoGraphQL\ModuleResolvers\SchemaConfigurationFunctionalityModuleResolver;
use GraphQLByPoP\GraphQLServer\Configuration\MutationPayloadTypeOptions;
use PHPUnitForGatoGraphQL\GatoGraphQL\Integration\AbstractModifyPluginSettingsFixtureEndpointWebserverRequestTestCase;

class SchemaPayloadTypesForMutationsWithObjectFieldsModifyPluginSettingsFixtureEndpointWebserverRequestTest extends AbstractModifyPluginSettingsFixtureEndpointWebserverRequestTestCase
{
    use SchemaPayloadTypesForMutationsWithObjectFieldsFixtureEndpointWebserverRequestTestTrait;

    protected static function getEndpoint(): string
    {
        return 'graphql/mobile-app/';
    }

    protected function getSettingsKey(): string
    {
        return SchemaConfigurationFunctionalityModuleResolver::OPTION_USE_PAYLOADABLE_MUTATIONS_DEFAULT_VALUE;
    }

    protected function getModuleID(string $dataName): string
    {
        return 'gatographql_gatographql_mutations';
    }

    protected function getPluginSettingsNewValue(): mixed
    {
        return MutationPayloadTypeOptions::USE_AND_QUERY_PAYLOAD_TYPES_FOR_MUTATIONS;
    }
}
