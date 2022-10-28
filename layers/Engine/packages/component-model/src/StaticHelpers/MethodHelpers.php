<?php

declare(strict_types=1);

namespace PoP\ComponentModel\StaticHelpers;

use PoP\ComponentModel\Engine\EngineIterationFieldSet;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use SplObjectStorage;
use stdClass;

class MethodHelpers
{
    /**
     * @param array<string|int,EngineIterationFieldSet> $idFieldSet
     * @return SplObjectStorage<FieldInterface,array<string|int>>
     */
    public static function orderIDsByDirectFields(array $idFieldSet): SplObjectStorage
    {
        /** @var SplObjectStorage<FieldInterface,array<string|int>> */
        $fieldIDs = new SplObjectStorage();
        foreach ($idFieldSet as $id => $fieldSet) {
            foreach ($fieldSet->fields as $field) {
                $ids = $fieldIDs[$field] ?? [];
                $ids[] = $id;
                $fieldIDs[$field] = $ids;
            }
        }
        return $fieldIDs;
    }

    /**
     * @param array<string|int,EngineIterationFieldSet> $idFieldSet
     * @return FieldInterface[]
     */
    public static function getFieldsFromIDFieldSet(array $idFieldSet): array
    {
        /** @var FieldInterface[] */
        $fields = [];
        foreach ($idFieldSet as $id => $fieldSet) {
            $fields = array_merge(
                $fields,
                $fieldSet->fields
            );
        }
        return array_values(array_unique($fields));
    }

    /**
     * @param array<string|int,EngineIterationFieldSet> $idFieldSet
     * @param FieldInterface[] $fields
     * @return array<string|int,EngineIterationFieldSet>
     */
    public static function filterFieldsInIDFieldSet(
        array $idFieldSet,
        array $fields
    ): array {
        $restrictedIDFieldSet = [];
        foreach ($idFieldSet as $id => $fieldSet) {
            $matchingFields = array_intersect(
                $fields,
                $fieldSet->fields
            );
            if ($matchingFields === []) {
                continue;
            }
            $restrictedIDFieldSet[$id] = new EngineIterationFieldSet(
                $matchingFields,
                $fieldSet->conditionalFields
            );
        }
        return $restrictedIDFieldSet;
    }

    /**
     * Convert associative arrays to stdClass, which is the
     * data structure used for inputs in GraphQL.
     *
     * @param mixed[] $array
     *
     * @see https://stackoverflow.com/a/4790485
     */
    public static function associativeArrayToObject(array $array): stdClass
    {
        $object = new stdClass();     
        foreach ($array as $key => $value) {
            if (is_array($value) && array_is_list($value)) {
                $object->{$key} = static::associativeArrayToObject($value);
                continue;
            }
            $object->{$key} = $value;
        }        
        return $object;
     }
}
