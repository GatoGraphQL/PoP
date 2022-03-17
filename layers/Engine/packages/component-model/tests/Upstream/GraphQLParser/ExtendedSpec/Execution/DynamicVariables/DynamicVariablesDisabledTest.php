<?php

declare(strict_types=1);

namespace PoP\ComponentModel\Upstream\GraphQLParser\ExtendedSpec\Execution\DynamicVariables;

class DynamicVariablesDisabledTest extends AbstractDynamicVariablesTest
{
    protected static function enabled(): bool
    {
        return false;
    }
}
