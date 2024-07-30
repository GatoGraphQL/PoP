<?php

declare(strict_types=1);

namespace PoPCMSSchema\CategoryMutations\FieldResolvers\ObjectType;

use PoPCMSSchema\Categories\TypeResolvers\ObjectType\GenericCategoryObjectTypeResolver;
use PoPCMSSchema\CategoryMutations\FieldResolvers\ObjectType\AbstractCategoryObjectTypeFieldResolver;
use PoPCMSSchema\CategoryMutations\Module as CategoryMutationsModule;
use PoPCMSSchema\CategoryMutations\ModuleConfiguration as CategoryMutationsModuleConfiguration;
use PoPCMSSchema\CategoryMutations\MutationResolvers\PayloadableUpdateGenericCategoryTermMutationResolver;
use PoPCMSSchema\CategoryMutations\MutationResolvers\UpdateGenericCategoryTermMutationResolver;
use PoPCMSSchema\CategoryMutations\TypeResolvers\InputObjectType\GenericCategoryUpdateInputObjectTypeResolver;
use PoPCMSSchema\CategoryMutations\TypeResolvers\ObjectType\GenericCategoryUpdateMutationPayloadObjectTypeResolver;
use PoP\ComponentModel\App;
use PoP\ComponentModel\MutationResolvers\MutationResolverInterface;
use PoP\ComponentModel\TypeResolvers\ConcreteTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\InputTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\ObjectType\ObjectTypeResolverInterface;

class GenericCategoryObjectTypeFieldResolver extends AbstractCategoryObjectTypeFieldResolver
{
    private ?GenericCategoryObjectTypeResolver $genericCategoryObjectTypeResolver = null;
    private ?GenericCategoryUpdateMutationPayloadObjectTypeResolver $genericCategoryUpdateMutationPayloadObjectTypeResolver = null;
    private ?UpdateGenericCategoryTermMutationResolver $updateGenericCategoryTermMutationResolver = null;
    private ?PayloadableUpdateGenericCategoryTermMutationResolver $payloadableUpdateGenericCategoryTermMutationResolver = null;
    private ?GenericCategoryUpdateInputObjectTypeResolver $genericCategoryUpdateInputObjectTypeResolver = null;

    final public function setGenericCategoryObjectTypeResolver(GenericCategoryObjectTypeResolver $genericCategoryObjectTypeResolver): void
    {
        $this->genericCategoryObjectTypeResolver = $genericCategoryObjectTypeResolver;
    }
    final protected function getGenericCategoryObjectTypeResolver(): GenericCategoryObjectTypeResolver
    {
        if ($this->genericCategoryObjectTypeResolver === null) {
            /** @var GenericCategoryObjectTypeResolver */
            $genericCategoryObjectTypeResolver = $this->instanceManager->getInstance(GenericCategoryObjectTypeResolver::class);
            $this->genericCategoryObjectTypeResolver = $genericCategoryObjectTypeResolver;
        }
        return $this->genericCategoryObjectTypeResolver;
    }
    final public function setGenericCategoryUpdateMutationPayloadObjectTypeResolver(GenericCategoryUpdateMutationPayloadObjectTypeResolver $genericCategoryUpdateMutationPayloadObjectTypeResolver): void
    {
        $this->genericCategoryUpdateMutationPayloadObjectTypeResolver = $genericCategoryUpdateMutationPayloadObjectTypeResolver;
    }
    final protected function getGenericCategoryUpdateMutationPayloadObjectTypeResolver(): GenericCategoryUpdateMutationPayloadObjectTypeResolver
    {
        if ($this->genericCategoryUpdateMutationPayloadObjectTypeResolver === null) {
            /** @var GenericCategoryUpdateMutationPayloadObjectTypeResolver */
            $genericCategoryUpdateMutationPayloadObjectTypeResolver = $this->instanceManager->getInstance(GenericCategoryUpdateMutationPayloadObjectTypeResolver::class);
            $this->genericCategoryUpdateMutationPayloadObjectTypeResolver = $genericCategoryUpdateMutationPayloadObjectTypeResolver;
        }
        return $this->genericCategoryUpdateMutationPayloadObjectTypeResolver;
    }
    final public function setUpdateGenericCategoryTermMutationResolver(UpdateGenericCategoryTermMutationResolver $updateGenericCategoryTermMutationResolver): void
    {
        $this->updateGenericCategoryTermMutationResolver = $updateGenericCategoryTermMutationResolver;
    }
    final protected function getUpdateGenericCategoryTermMutationResolver(): UpdateGenericCategoryTermMutationResolver
    {
        if ($this->updateGenericCategoryTermMutationResolver === null) {
            /** @var UpdateGenericCategoryTermMutationResolver */
            $updateGenericCategoryTermMutationResolver = $this->instanceManager->getInstance(UpdateGenericCategoryTermMutationResolver::class);
            $this->updateGenericCategoryTermMutationResolver = $updateGenericCategoryTermMutationResolver;
        }
        return $this->updateGenericCategoryTermMutationResolver;
    }
    final public function setPayloadableUpdateGenericCategoryTermMutationResolver(PayloadableUpdateGenericCategoryTermMutationResolver $payloadableUpdateGenericCategoryTermMutationResolver): void
    {
        $this->payloadableUpdateGenericCategoryTermMutationResolver = $payloadableUpdateGenericCategoryTermMutationResolver;
    }
    final protected function getPayloadableUpdateGenericCategoryTermMutationResolver(): PayloadableUpdateGenericCategoryTermMutationResolver
    {
        if ($this->payloadableUpdateGenericCategoryTermMutationResolver === null) {
            /** @var PayloadableUpdateGenericCategoryTermMutationResolver */
            $payloadableUpdateGenericCategoryTermMutationResolver = $this->instanceManager->getInstance(PayloadableUpdateGenericCategoryTermMutationResolver::class);
            $this->payloadableUpdateGenericCategoryTermMutationResolver = $payloadableUpdateGenericCategoryTermMutationResolver;
        }
        return $this->payloadableUpdateGenericCategoryTermMutationResolver;
    }
    final public function setGenericCategoryUpdateInputObjectTypeResolver(GenericCategoryUpdateInputObjectTypeResolver $genericCategoryUpdateInputObjectTypeResolver): void
    {
        $this->genericCategoryUpdateInputObjectTypeResolver = $genericCategoryUpdateInputObjectTypeResolver;
    }
    final protected function getGenericCategoryUpdateInputObjectTypeResolver(): GenericCategoryUpdateInputObjectTypeResolver
    {
        if ($this->genericCategoryUpdateInputObjectTypeResolver === null) {
            /** @var GenericCategoryUpdateInputObjectTypeResolver */
            $genericCategoryUpdateInputObjectTypeResolver = $this->instanceManager->getInstance(GenericCategoryUpdateInputObjectTypeResolver::class);
            $this->genericCategoryUpdateInputObjectTypeResolver = $genericCategoryUpdateInputObjectTypeResolver;
        }
        return $this->genericCategoryUpdateInputObjectTypeResolver;
    }

    /**
     * @return array<class-string<ObjectTypeResolverInterface>>
     */
    public function getObjectTypeResolverClassesToAttachTo(): array
    {
        return [
            GenericCategoryObjectTypeResolver::class,
        ];
    }

    public function getFieldDescription(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): ?string
    {
        return match ($fieldName) {
            'update' => $this->__('Update the post', 'post-mutations'),
            default => parent::getFieldDescription($objectTypeResolver, $fieldName),
        };
    }

    /**
     * @return array<string,InputTypeResolverInterface>
     */
    public function getFieldArgNameTypeResolvers(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): array
    {
        return match ($fieldName) {
            'update' => [
                'input' => $this->getGenericCategoryUpdateInputObjectTypeResolver(),
            ],
            default => parent::getFieldArgNameTypeResolvers($objectTypeResolver, $fieldName),
        };
    }

    public function getFieldMutationResolver(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): ?MutationResolverInterface
    {
        /** @var CategoryMutationsModuleConfiguration */
        $moduleConfiguration = App::getModule(CategoryMutationsModule::class)->getConfiguration();
        $usePayloadableCategoryMutations = $moduleConfiguration->usePayloadableCategoryMutations();
        return match ($fieldName) {
            'update' => $usePayloadableCategoryMutations
                ? $this->getPayloadableUpdateGenericCategoryTermMutationResolver()
                : $this->getUpdateGenericCategoryTermMutationResolver(),
            default => parent::getFieldMutationResolver($objectTypeResolver, $fieldName),
        };
    }

    public function getFieldTypeResolver(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): ConcreteTypeResolverInterface
    {
        /** @var CategoryMutationsModuleConfiguration */
        $moduleConfiguration = App::getModule(CategoryMutationsModule::class)->getConfiguration();
        $usePayloadableCategoryMutations = $moduleConfiguration->usePayloadableCategoryMutations();
        return match ($fieldName) {
            'update' => $usePayloadableCategoryMutations
                ? $this->getGenericCategoryUpdateMutationPayloadObjectTypeResolver()
                : $this->getGenericCategoryObjectTypeResolver(),
            default => parent::getFieldTypeResolver($objectTypeResolver, $fieldName),
        };
    }
}