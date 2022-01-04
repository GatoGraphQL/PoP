<?php

declare(strict_types=1);

namespace PoP\AccessControl\RelationalTypeResolverDecorators;

use PoP\AccessControl\Component;
use PoP\AccessControl\ComponentConfiguration;
use PoP\AccessControl\Schema\SchemaModes;
use PoP\ComponentModel\RelationalTypeResolverDecorators\AbstractRelationalTypeResolverDecorator;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;

abstract class AbstractPublicSchemaRelationalTypeResolverDecorator extends AbstractRelationalTypeResolverDecorator
{
    /**
     * Enable only for public schema
     */
    public function enabled(RelationalTypeResolverInterface $relationalTypeResolver): bool
    {
        return
            ComponentConfiguration::enableIndividualControlForPublicPrivateSchemaMode() ||
            !ComponentConfiguration::usePrivateSchemaMode();
    }

    protected function getSchemaMode(): string
    {
        return SchemaModes::PUBLIC_SCHEMA_MODE;
    }
}
