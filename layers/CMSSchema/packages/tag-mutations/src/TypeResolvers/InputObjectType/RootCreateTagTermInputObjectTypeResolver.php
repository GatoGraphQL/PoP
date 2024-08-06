<?php

declare(strict_types=1);

namespace PoPCMSSchema\TagMutations\TypeResolvers\InputObjectType;

class RootCreateTagTermInputObjectTypeResolver extends AbstractCreateOrUpdateTagTermInputObjectTypeResolver implements CreateTagTermInputObjectTypeResolverInterface
{
    public function getTypeName(): string
    {
        return 'RootCreateTagInput';
    }

    public function getTypeDescription(): ?string
    {
        return $this->__('Input to create a tag term', 'tag-mutations');
    }

    protected function addTaxonomyInputField(): bool
    {
        return false;
    }

    protected function isNameInputFieldMandatory(): bool
    {
        return true;
    }
}
