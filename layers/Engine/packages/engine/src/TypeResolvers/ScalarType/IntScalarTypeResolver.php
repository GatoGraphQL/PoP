<?php

declare(strict_types=1);

namespace PoP\Engine\TypeResolvers\ScalarType;

use PoP\ComponentModel\TypeResolvers\ScalarType\AbstractScalarTypeResolver;

/**
 * GraphQL Built-in Scalar
 * 
 * @see https://spec.graphql.org/draft/#sec-Scalars.Built-in-Scalars
 */
class IntScalarTypeResolver extends AbstractScalarTypeResolver
{
    public function getTypeName(): string
    {
        return 'Int';
    }

    public function coerceValue(mixed $inputValue): mixed
    {
        return $inputValue;
    }
}
