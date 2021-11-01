<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\RelationalTypeDataLoaders\ObjectType;

use GraphQLByPoP\GraphQLServer\ObjectModels\ListWrappingType;
use GraphQLByPoP\GraphQLServer\ObjectModels\NonNullWrappingType;
use GraphQLByPoP\GraphQLServer\ObjectModels\SchemaDefinitionReferenceObjectInterface;
use GraphQLByPoP\GraphQLServer\ObjectModels\TypeInterface;
use GraphQLByPoP\GraphQLServer\ObjectModels\WrappingTypeInterface;
use GraphQLByPoP\GraphQLServer\Registries\SchemaDefinitionReferenceRegistryInterface;
use GraphQLByPoP\GraphQLServer\Syntax\GraphQLSyntaxServiceInterface;
use PoP\ComponentModel\RelationalTypeDataLoaders\ObjectType\AbstractObjectTypeDataLoader;
use Symfony\Contracts\Service\Attribute\Required;

class WrappingTypeOrSchemaDefinitionReferenceTypeDataLoader extends AbstractObjectTypeDataLoader
{
    private ?SchemaDefinitionReferenceRegistryInterface $schemaDefinitionReferenceRegistry = null;
    private ?GraphQLSyntaxServiceInterface $graphQLSyntaxService = null;

    final public function setSchemaDefinitionReferenceRegistry(SchemaDefinitionReferenceRegistryInterface $schemaDefinitionReferenceRegistry): void
    {
        $this->schemaDefinitionReferenceRegistry = $schemaDefinitionReferenceRegistry;
    }
    final protected function getSchemaDefinitionReferenceRegistry(): SchemaDefinitionReferenceRegistryInterface
    {
        return $this->schemaDefinitionReferenceRegistry ??= $this->instanceManager->getInstance(SchemaDefinitionReferenceRegistryInterface::class);
    }
    final public function setGraphQLSyntaxService(GraphQLSyntaxServiceInterface $graphQLSyntaxService): void
    {
        $this->graphQLSyntaxService = $graphQLSyntaxService;
    }
    final protected function getGraphQLSyntaxService(): GraphQLSyntaxServiceInterface
    {
        return $this->graphQLSyntaxService ??= $this->instanceManager->getInstance(GraphQLSyntaxServiceInterface::class);
    }

    /**
     * The IDs can contain GraphQL's type wrappers, such as `[String]!`
     *
     * @return array<WrappingTypeInterface | SchemaDefinitionReferenceObjectInterface>
     */
    public function getObjects(array $ids): array
    {
        return array_map(
            fn (string $typeID) => $this->getWrappingTypeOrSchemaDefinitionReferenceObject($typeID),
            $ids
        );
    }

    protected function getWrappingTypeOrSchemaDefinitionReferenceObject(string $typeID): WrappingTypeInterface | SchemaDefinitionReferenceObjectInterface
    {
        // Check if the type is non-null
        if ($this->getGraphQLSyntaxService()->isNonNullWrappingType($typeID)) {
            /** @var TypeInterface */
            $wrappedType = $this->getWrappingTypeOrSchemaDefinitionReferenceObject(
                $this->getGraphQLSyntaxService()->extractWrappedTypeFromNonNullWrappingType($typeID)
            );
            return new NonNullWrappingType($wrappedType);
        }

        // Check if it is an array
        if ($this->getGraphQLSyntaxService()->isListWrappingType($typeID)) {
            /** @var TypeInterface */
            $wrappedType = $this->getWrappingTypeOrSchemaDefinitionReferenceObject(
                $this->getGraphQLSyntaxService()->extractWrappedTypeFromListWrappingType($typeID)
            );
            return new ListWrappingType($wrappedType);
        }

        return $this->getSchemaDefinitionReferenceRegistry()->getSchemaDefinitionReferenceObject($typeID);
    }
}
