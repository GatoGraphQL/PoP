<?php

declare(strict_types=1);

namespace GatoGraphQL\PluginUtils\Services\Helpers;

use PoP\ComponentModel\AbstractTestCase;

class StringConversionTest extends AbstractTestCase
{
    public function testDashesToCamelCase(): void
    {
        /** @var StringConversion */
        $stringConversion = $this->getService(StringConversion::class);

        $this->assertSame(
            'graphqlApiSchemaConfigOptions',
            $stringConversion->dashesToCamelCase(
                'gato-graphql-schema-config-options'
            )
        );

        $this->assertSame(
            'GraphqlApiSchemaConfigOptions',
            $stringConversion->dashesToCamelCase(
                'gato-graphql-schema-config-options',
                true
            )
        );
    }
}
