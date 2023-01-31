<?php

declare(strict_types=1);

namespace PHPUnitForGraphQLAPI\GraphQLAPI\Integration;

trait QueryableCustomPostsFixtureEndpointWebserverRequestTestTrait
{
    protected function getFixtureFolder(): string
    {
        return __DIR__ . '/fixture-queryable-customposts';
    }

    /**
     * @return array<string,mixed>
     */
    protected function getIncludedCustomPostTypesNewValue(): array
    {
        $value = [
            'post',
            'attachment',
            'nav_menu_item',
            'custom_css',
            'revision',
        ];

        $dataName = $this->getDataName();
        if (str_ends_with($dataName, ':1')) {
            $value[] = 'page';
        } elseif (str_ends_with($dataName, ':2')) {
            $value[] = 'page';
            $value[] = 'dummy-cpt';
        }

        /**
         * Sort them as to store the entries in same way as via the UI,
         * then tests won't fail whether data was added via PHPUnit test or
         * via user interface
         */
        sort($value);

        return $value;
    }
}
