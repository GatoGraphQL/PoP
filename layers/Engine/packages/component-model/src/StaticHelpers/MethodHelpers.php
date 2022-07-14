<?php

declare(strict_types=1);

namespace PoP\ComponentModel\StaticHelpers;

use PoP\ComponentModel\Engine\EngineIterationFieldSet;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use SplObjectStorage;

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
}
