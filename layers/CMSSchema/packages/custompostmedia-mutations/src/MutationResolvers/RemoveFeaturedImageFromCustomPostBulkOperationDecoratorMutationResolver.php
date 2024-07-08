<?php

declare(strict_types=1);

namespace PoPCMSSchema\CustomPostMediaMutations\MutationResolvers;

use PoPCMSSchema\SchemaCommons\MutationResolvers\AbstractBulkOperationDecoratorMutationResolver;
use PoP\ComponentModel\MutationResolvers\MutationResolverInterface;

class RemoveFeaturedImageFromCustomPostBulkOperationDecoratorMutationResolver extends AbstractBulkOperationDecoratorMutationResolver
{
    private ?RemoveFeaturedImageFromCustomPostMutationResolver $removeFeaturedImageFromCustomPostMutationResolver = null;

    final public function setRemoveFeaturedImageFromCustomPostMutationResolver(RemoveFeaturedImageFromCustomPostMutationResolver $removeFeaturedImageFromCustomPostMutationResolver): void
    {
        $this->removeFeaturedImageFromCustomPostMutationResolver = $removeFeaturedImageFromCustomPostMutationResolver;
    }
    final protected function getRemoveFeaturedImageFromCustomPostMutationResolver(): RemoveFeaturedImageFromCustomPostMutationResolver
    {
        if ($this->removeFeaturedImageFromCustomPostMutationResolver === null) {
            /** @var RemoveFeaturedImageFromCustomPostMutationResolver */
            $removeFeaturedImageFromCustomPostMutationResolver = $this->instanceManager->getInstance(RemoveFeaturedImageFromCustomPostMutationResolver::class);
            $this->removeFeaturedImageFromCustomPostMutationResolver = $removeFeaturedImageFromCustomPostMutationResolver;
        }
        return $this->removeFeaturedImageFromCustomPostMutationResolver;
    }

    protected function getDecoratedOperationMutationResolver(): MutationResolverInterface
    {
        return $this->getRemoveFeaturedImageFromCustomPostMutationResolver();
    }
}
