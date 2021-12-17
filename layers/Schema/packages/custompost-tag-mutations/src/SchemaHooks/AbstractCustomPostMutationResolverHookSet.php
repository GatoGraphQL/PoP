<?php

declare(strict_types=1);

namespace PoPSchema\CustomPostTagMutations\SchemaHooks;

use PoP\ComponentModel\Schema\SchemaTypeModifiers;
use PoP\ComponentModel\TypeResolvers\InputObjectType\HookNames;
use PoP\ComponentModel\TypeResolvers\InputObjectType\InputObjectTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\InputTypeResolverInterface;
use PoP\Engine\TypeResolvers\ScalarType\StringScalarTypeResolver;
use PoP\BasicService\AbstractHookSet;
use PoPSchema\CustomPostMutations\TypeResolvers\InputObjectType\CreateCustomPostFilterInputObjectTypeResolverInterface;
use PoPSchema\CustomPostMutations\TypeResolvers\InputObjectType\UpdateCustomPostFilterInputObjectTypeResolverInterface;
use PoPSchema\CustomPostTagMutations\MutationResolvers\MutationInputProperties;

abstract class AbstractCustomPostMutationResolverHookSet extends AbstractHookSet
{
    private ?StringScalarTypeResolver $stringScalarTypeResolver = null;

    final public function setStringScalarTypeResolver(StringScalarTypeResolver $stringScalarTypeResolver): void
    {
        $this->stringScalarTypeResolver = $stringScalarTypeResolver;
    }
    final protected function getStringScalarTypeResolver(): StringScalarTypeResolver
    {
        return $this->stringScalarTypeResolver ??= $this->instanceManager->getInstance(StringScalarTypeResolver::class);
    }

    protected function init(): void
    {
        $this->getHooksAPI()->addFilter(
            HookNames::INPUT_FIELD_NAME_TYPE_RESOLVERS,
            array($this, 'maybeAddInputFieldNameTypeResolvers'),
            10,
            2
        );
        $this->getHooksAPI()->addFilter(
            HookNames::INPUT_FIELD_DESCRIPTION,
            array($this, 'maybeAddInputFieldDescription'),
            10,
            3
        );
        $this->getHooksAPI()->addFilter(
            HookNames::INPUT_FIELD_TYPE_MODIFIERS,
            array($this, 'maybeAddInputFieldTypeModifiers'),
            10,
            3
        );
    }

    /**
     * @param array<string, InputTypeResolverInterface> $inputFieldNameTypeResolvers
     * @return array<string, InputTypeResolverInterface>
     */
    public function maybeAddInputFieldNameTypeResolvers(
        array $inputFieldNameTypeResolvers,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
    ): array {
        // Only for the specific combinations of Type and fieldName
        if (!$this->isInputObjectTypeResolver($inputObjectTypeResolver)) {
            return $inputFieldNameTypeResolvers;
        }
        $inputFieldNameTypeResolvers[MutationInputProperties::TAGS] = $this->getStringScalarTypeResolver();
        return $inputFieldNameTypeResolvers;
    }

    protected function isInputObjectTypeResolver(
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
    ): bool {
        return $inputObjectTypeResolver instanceof CreateCustomPostFilterInputObjectTypeResolverInterface
            || $inputObjectTypeResolver instanceof UpdateCustomPostFilterInputObjectTypeResolverInterface;
    }

    public function maybeAddInputFieldDescription(
        ?string $inputFieldDescription,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
        string $inputFieldName,
    ): ?string {
        // Only for the newly added inputFieldName
        if ($inputFieldName !== MutationInputProperties::TAGS || !$this->isInputObjectTypeResolver($inputObjectTypeResolver)) {
            return $inputFieldDescription;
        }
        return $this->getTranslationAPI()->__('The tags to set', 'custompost-tag-mutations');
    }

    public function maybeAddInputFieldTypeModifiers(
        int $inputFieldTypeModifiers,
        InputObjectTypeResolverInterface $inputObjectTypeResolver,
        string $inputFieldName,
    ): int {
        // Only for the newly added inputFieldName
        if ($inputFieldName !== MutationInputProperties::TAGS || !$this->isInputObjectTypeResolver($inputObjectTypeResolver)) {
            return $inputFieldTypeModifiers;
        }
        return SchemaTypeModifiers::IS_ARRAY | SchemaTypeModifiers::IS_NON_NULLABLE_ITEMS_IN_ARRAY;
    }
}
