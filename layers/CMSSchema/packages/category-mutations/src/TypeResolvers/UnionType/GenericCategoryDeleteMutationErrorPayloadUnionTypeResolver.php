<?php

declare(strict_types=1);

namespace PoPCMSSchema\CategoryMutations\TypeResolvers\UnionType;

use PoPCMSSchema\CategoryMutations\TypeResolvers\UnionType\AbstractCategoryDeleteMutationErrorPayloadUnionTypeResolver;
use PoPCMSSchema\CategoryMutations\RelationalTypeDataLoaders\UnionType\GenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader;
use PoP\ComponentModel\RelationalTypeDataLoaders\RelationalTypeDataLoaderInterface;

class GenericCategoryDeleteMutationErrorPayloadUnionTypeResolver extends AbstractCategoryDeleteMutationErrorPayloadUnionTypeResolver
{
    private ?GenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader $genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader = null;

    final public function setGenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader(GenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader $genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader): void
    {
        $this->genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader = $genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader;
    }
    final protected function getGenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader(): GenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader
    {
        if ($this->genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader === null) {
            /** @var GenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader */
            $genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader = $this->instanceManager->getInstance(GenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader::class);
            $this->genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader = $genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader;
        }
        return $this->genericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader;
    }

    public function getTypeName(): string
    {
        return 'CategoryDeleteMutationErrorPayloadUnion';
    }

    public function getTypeDescription(): ?string
    {
        return $this->__('Union of \'Error Payload\' types when deleting a category term (using nested mutations)', 'post-mutations');
    }

    public function getRelationalTypeDataLoader(): RelationalTypeDataLoaderInterface
    {
        return $this->getGenericCategoryDeleteMutationErrorPayloadUnionTypeDataLoader();
    }
}
