<?php

declare(strict_types=1);

namespace PoPCMSSchema\CustomPostCategoryMutations\SchemaHooks;

use PoPCMSSchema\Categories\TypeResolvers\ObjectType\CategoryObjectTypeResolverInterface;
use PoPCMSSchema\CustomPostCategoryMutations\SchemaHooks\AbstractCustomPostMutationResolverHookSet;
use PoPCMSSchema\Categories\TypeResolvers\ObjectType\GenericCategoryObjectTypeResolver;
use PoPCMSSchema\PostMutations\SchemaHooks\PostMutationResolverHookSetTrait;

class GenericCustomPostMutationResolverHookSet extends AbstractCustomPostMutationResolverHookSet
{
    use PostMutationResolverHookSetTrait;

    private ?GenericCategoryObjectTypeResolver $genericCategoryObjectTypeResolver = null;

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

    protected function getCategoryTypeResolver(): CategoryObjectTypeResolverInterface
    {
        return $this->getGenericCategoryObjectTypeResolver();
    }
}
