<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\TypeResolvers\ObjectType;

use GraphQLByPoP\GraphQLServer\TypeResolvers\ObjectType\AbstractIntrospectionObjectTypeResolver;
use GraphQLByPoP\GraphQLServer\RelationalTypeDataLoaders\ObjectType\SchemaDefinitionReferenceTypeDataLoader;

class TypeObjectTypeResolver extends AbstractIntrospectionObjectTypeResolver
{
    public function getTypeName(): string
    {
        return '__Type';
    }

    public function getSchemaTypeDescription(): ?string
    {
        return $this->translationAPI->__('Representation of each GraphQL type in the graph', 'graphql-server');
    }

    public function getID(object $resultItem): string | int | null
    {
        $type = $resultItem;
        return $type->getID();
    }

    public function getRelationalTypeDataLoaderClass(): string
    {
        return SchemaDefinitionReferenceTypeDataLoader::class;
    }
}
