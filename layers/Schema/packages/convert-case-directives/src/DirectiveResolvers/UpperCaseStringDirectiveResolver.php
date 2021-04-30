<?php

declare(strict_types=1);

namespace PoPSchema\ConvertCaseDirectives\DirectiveResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\GlobalDirectiveResolverTrait;
use PoPSchema\BasicDirectives\DirectiveResolvers\AbstractTransformFieldStringValueDirectiveResolver;

/**
 * Convert a string to upper case
 */
class UpperCaseStringDirectiveResolver extends AbstractTransformFieldStringValueDirectiveResolver
{
    use GlobalDirectiveResolverTrait;

    public function getDirectiveName(): string
    {
        return 'upperCase';
    }

    protected function transformValue($value, $id, string $field, string $fieldOutputKey, TypeResolverInterface $typeResolver, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$dbDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        /**
         * Validate it is a string
         */
        if (is_null($value) || !$this->validateTypeIsString($value, $id, $field, $fieldOutputKey, $dbErrors, $dbWarnings)) {
            return $value;
        }
        return strtoupper($value);
    }
    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        return $this->translationAPI->__('Convert a string to upper case', 'convert-case-directives');
    }
}
