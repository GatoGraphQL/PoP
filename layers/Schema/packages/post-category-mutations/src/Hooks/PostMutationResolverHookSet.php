<?php

declare(strict_types=1);

namespace PoPSchema\PostCategoryMutations\Hooks;

use PoPSchema\Categories\TypeResolvers\ObjectType\CategoryObjectTypeResolverInterface;
use PoPSchema\CustomPostCategoryMutations\Hooks\AbstractCustomPostMutationResolverHookSet;
use PoPSchema\CustomPostCategoryMutations\TypeAPIs\CustomPostCategoryTypeMutationAPIInterface;
use PoPSchema\CustomPosts\TypeResolvers\ObjectType\CustomPostObjectTypeResolverInterface;
use PoPSchema\PostCategories\TypeResolvers\ObjectType\PostCategoryObjectTypeResolver;
use PoPSchema\PostCategoryMutations\Facades\PostCategoryTypeMutationAPIFacade;
use PoPSchema\Posts\TypeAPIs\PostTypeAPIInterface;
use PoPSchema\Posts\TypeResolvers\ObjectType\PostObjectTypeResolver;
use Symfony\Contracts\Service\Attribute\Required;

class PostMutationResolverHookSet extends AbstractCustomPostMutationResolverHookSet
{
    protected PostObjectTypeResolver $postObjectTypeResolver;
    protected PostCategoryObjectTypeResolver $postCategoryObjectTypeResolver;
    protected PostTypeAPIInterface $postTypeAPI;

    #[Required]
    public function autowirePostMutationResolverHookSet(
        PostObjectTypeResolver $postObjectTypeResolver,
        PostCategoryObjectTypeResolver $postCategoryObjectTypeResolver,
        PostTypeAPIInterface $postTypeAPI,
    ): void {
        $this->postObjectTypeResolver = $postObjectTypeResolver;
        $this->postCategoryObjectTypeResolver = $postCategoryObjectTypeResolver;
        $this->postTypeAPI = $postTypeAPI;
    }

    protected function getCustomPostObjectTypeResolver(): CustomPostObjectTypeResolverInterface
    {
        return $this->postObjectTypeResolver;
    }

    protected function getCustomPostType(): string
    {
        return $this->postTypeAPI->getPostCustomPostType();
    }

    protected function getCategoryTypeResolver(): CategoryObjectTypeResolverInterface
    {
        return $this->postCategoryObjectTypeResolver;
    }

    protected function getCustomPostCategoryTypeMutationAPI(): CustomPostCategoryTypeMutationAPIInterface
    {
        return PostCategoryTypeMutationAPIFacade::getInstance();
    }
}
