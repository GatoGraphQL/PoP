<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Unit;

use PoP\Root\Module\ModuleInterface;

class NamespacingFixtureQueryExecutionGraphQLServerTest extends AbstractFixtureQueryExecutionGraphQLServerTestCaseCase
{
    /**
     * Directory under the fixture files are placed
     */
    protected function getFixtureFolder(): string
    {
        return __DIR__ . '/fixture-namespacing';
    }

    /**
     * @return array<class-string<ModuleInterface>,array<string,mixed>>
     */
    protected static function getGraphQLServerModuleClassConfiguration(): array
    {
        return [
            ...parent::getGraphQLServerModuleClassConfiguration(),
            ...[
                \PoP\ComponentModel\Module::class => [
                    \PoP\ComponentModel\Environment::NAMESPACE_TYPES_AND_INTERFACES => true,
                ],
            ]
        ];
    }
}
