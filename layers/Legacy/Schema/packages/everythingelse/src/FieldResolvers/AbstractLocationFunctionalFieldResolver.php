<?php

declare(strict_types=1);

namespace PoPSchema\Locations\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\FieldResolvers\AbstractFunctionalFieldResolver;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\ComponentModel\Misc\GeneralUtils;
use PoP\Engine\Route\RouteUtils;

abstract class AbstractLocationFunctionalFieldResolver extends AbstractFunctionalFieldResolver
{
    protected function getDbobjectIdField()
    {
        return null;
    }

    public function getFieldNamesToResolve(): array
    {
        return [
            'locationsmapURL',
        ];
    }

    public function getSchemaFieldType(RelationalTypeResolverInterface $relationalTypeResolver, string $fieldName): string
    {
        $types = [
            'locationsmapURL' => SchemaDefinition::TYPE_URL,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($relationalTypeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(RelationalTypeResolverInterface $relationalTypeResolver, string $fieldName): ?string
    {
        $descriptions = [
            'locationsmapURL' => $this->translationAPI->__('Locations map URL', 'pop-locations'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($relationalTypeResolver, $fieldName);
    }

    /**
     * @param array<string, mixed> $fieldArgs
     * @param array<string, mixed>|null $variables
     * @param array<string, mixed>|null $expressions
     * @param array<string, mixed> $options
     */
    public function resolveValue(
        RelationalTypeResolverInterface $relationalTypeResolver,
        object $resultItem,
        string $fieldName,
        array $fieldArgs = [],
        ?array $variables = null,
        ?array $expressions = null,
        array $options = []
    ): mixed {
        switch ($fieldName) {
            case 'locationsmapURL':
                $locations = $relationalTypeResolver->resolveValue($resultItem, 'locations', $variables, $expressions, $options);
                if (GeneralUtils::isError($locations)) {
                    return null;
                }
                return
                    // Decode it, because add_query_arg sends the params encoded and it doesn't look nice
                    urldecode(
                        // Add param "lid[]=..."
                        GeneralUtils::addQueryArgs([
                            POP_INPUTNAME_LOCATIONID => $locations,
                            // In order to keep always the same layout for the same URL, we add the param of which object we are coming from
                            // (Then, in the modal map, it will show either post/user layout, and that layout will be cached for that post/user but not for other objects)
                            $this->getDbobjectIdField() => $relationalTypeResolver->getID($resultItem),
                        ], RouteUtils::getRouteURL(POP_LOCATIONS_ROUTE_LOCATIONSMAP))
                    );
        }

        return parent::resolveValue($relationalTypeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
