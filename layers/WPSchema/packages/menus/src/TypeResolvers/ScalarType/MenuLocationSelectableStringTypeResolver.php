<?php

declare(strict_types=1);

namespace PoPWPSchema\Menus\TypeResolvers\ScalarType;

use PoPSchema\SchemaCommons\TypeResolvers\ScalarType\AbstractSelectableStringScalarTypeResolver;

class MenuLocationSelectableStringTypeResolver extends AbstractSelectableStringScalarTypeResolver
{
    public function getTypeName(): string
    {
        return 'MenuLocationSelectableString';
    }

    public function getTypeDescription(): string
    {
        return sprintf(
            $this->getTranslationAPI()->__('Menu Locations: \'%s\'', 'menus'),
            implode('\', \'', $this->getConsolidatedPossibleValues())
        );
    }

    /**
     * @return string[]
     */
    public function getPossibleValues(): array
    {
        // Make sure there's at least 1 result, to avoid GraphQL throwing
        // errors from an empty Enum
        if ($enumValues = array_keys(\get_registered_nav_menus())) {
            return $enumValues;
        }
        return ['empty'];
    }
}
