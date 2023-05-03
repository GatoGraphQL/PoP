<?php

declare(strict_types=1);

namespace PHPUnitForGraphQLAPI\GraphQLAPI\Unit\Faker;

use GraphQLByPoP\GraphQLServer\Unit\EnabledFixtureQueryExecutionGraphQLServerTestCaseTrait;

class EnabledNamespacingWPFakerFixtureQueryExecutionGraphQLServerTest extends AbstractNamespacingWPFakerFixtureQueryExecutionGraphQLServerTestCase
{
    use EnabledFixtureQueryExecutionGraphQLServerTestCaseTrait;
}
