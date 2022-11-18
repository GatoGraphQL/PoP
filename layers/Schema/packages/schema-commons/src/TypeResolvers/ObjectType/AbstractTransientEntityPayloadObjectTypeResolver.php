<?php

declare(strict_types=1);

namespace PoPSchema\SchemaCommons\TypeResolvers\ObjectType;

use PoPSchema\SchemaCommons\ObjectModels\AbstractTransientEntityPayload;
use PoP\ComponentModel\FieldResolvers\InterfaceType\InterfaceTypeFieldResolverInterface;
use PoP\ComponentModel\FieldResolvers\InterfaceType\NodeInterfaceTypeFieldResolver;
use PoP\ComponentModel\TypeResolvers\ObjectType\AbstractObjectTypeResolver;
use PoP\ComponentModel\TypeResolvers\ObjectType\RemoveNodeInterfaceObjectTypeResolverTrait;

abstract class AbstractTransientEntityPayloadObjectTypeResolver extends AbstractObjectTypeResolver
{
    use RemoveNodeInterfaceObjectTypeResolverTrait;

    private ?NodeInterfaceTypeFieldResolver $nodeInterfaceTypeFieldResolver = null;

    final public function setNodeInterfaceTypeFieldResolver(NodeInterfaceTypeFieldResolver $nodeInterfaceTypeFieldResolver): void
    {
        $this->nodeInterfaceTypeFieldResolver = $nodeInterfaceTypeFieldResolver;
    }
    final protected function getNodeInterfaceTypeFieldResolver(): NodeInterfaceTypeFieldResolver
    {
        /** @var NodeInterfaceTypeFieldResolver */
        return $this->nodeInterfaceTypeFieldResolver ??= $this->instanceManager->getInstance(NodeInterfaceTypeFieldResolver::class);
    }

    final public function getID(object $object): string|int|null
    {
        /** @var AbstractTransientEntityPayload */
        $entityPayloadObject = $object;
        return $entityPayloadObject->getID();
    }

    /**
     * Remove the Node interface
     *
     * @param InterfaceTypeFieldResolverInterface[] $interfaceTypeFieldResolvers
     * @return InterfaceTypeFieldResolverInterface[]
     */
    final protected function consolidateAllImplementedInterfaceTypeFieldResolvers(
        array $interfaceTypeFieldResolvers,
    ): array {
        return $this->removeNodeInterfaceTypeFieldResolver(
            parent::consolidateAllImplementedInterfaceTypeFieldResolvers($interfaceTypeFieldResolvers),
        );
    }
}
