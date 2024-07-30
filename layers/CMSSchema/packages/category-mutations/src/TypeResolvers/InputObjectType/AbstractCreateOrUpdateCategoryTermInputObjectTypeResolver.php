<?php

declare(strict_types=1);

namespace PoPCMSSchema\CategoryMutations\TypeResolvers\InputObjectType;

use PoPCMSSchema\TaxonomyMutations\Constants\MutationInputProperties;
use PoPCMSSchema\TaxonomyMutations\TypeResolvers\InputObjectType\AbstractCreateOrUpdateTaxonomyTermInputObjectTypeResolver;

abstract class AbstractCreateOrUpdateCategoryTermInputObjectTypeResolver extends AbstractCreateOrUpdateTaxonomyTermInputObjectTypeResolver implements UpdateCategoryInputObjectTypeResolverInterface, CreateCategoryTermInputObjectTypeResolverInterface
{
    protected function addParentIDInputField(): bool
    {
        return true;
    }

    public function getTypeDescription(): ?string
    {
        return $this->__('Input to update a category term', 'category-mutations');
    }

    public function getInputFieldDescription(string $inputFieldName): ?string
    {
        return match ($inputFieldName) {
            MutationInputProperties::ID => $this->__('The ID of the category to update', 'category-mutations'),
            MutationInputProperties::NAME => $this->__('The name of the category', 'category-mutations'),
            MutationInputProperties::DESCRIPTION => $this->__('The description of the category', 'category-mutations'),
            MutationInputProperties::SLUG => $this->__('The slug of the category', 'category-mutations'),
            MutationInputProperties::PARENT_ID => $this->__('The category\'s parent', 'category-mutations'),
            default => parent::getInputFieldDescription($inputFieldName),
        };
    }
}
