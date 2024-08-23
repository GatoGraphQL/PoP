<?php

declare(strict_types=1);

namespace PoPCMSSchema\CustomPostCategoryMutations\MutationResolvers;

use PoPCMSSchema\CategoryMutations\MutationResolvers\PayloadableUpdateCategoryTermMutationResolverTrait;

class PayloadableUpdateGenericCategoryTermMutationResolver extends AbstractMutateGenericCategoryTermMutationResolver
{
    use PayloadableUpdateCategoryTermMutationResolverTrait;
}
