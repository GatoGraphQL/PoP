<?php

declare(strict_types=1);

namespace PoPCMSSchema\CategoryMutationsWP\TypeAPIs;

use PoPCMSSchema\CategoryMutations\Exception\CategoryCRUDMutationException;
use PoPCMSSchema\CategoryMutations\TypeAPIs\CategoryTypeMutationAPIInterface;
use PoPCMSSchema\SchemaCommonsWP\TypeAPIs\TypeMutationAPITrait;
use PoP\ComponentModel\App;
use PoP\Root\Services\BasicServiceTrait;
use WP_Error;

use function user_can;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class CategoryTypeMutationAPI implements CategoryTypeMutationAPIInterface
{
    use BasicServiceTrait;
    use TypeMutationAPITrait;

    public const HOOK_QUERY = __CLASS__ . ':query';

    /**
     * @param array<string,mixed> $query
     * @return array<string,mixed> $query
     */
    protected function convertCategoriesMutationQuery(array $query): array
    {
        // Convert the parameters
        if (isset($query['parent-id'])) {
            $query['parent'] = $query['parent-id'];
            unset($query['parent-id']);
        }
        if (isset($query['id'])) {
            $query['ID'] = $query['id'];
            unset($query['id']);
        }
        if (isset($query['description'])) {
            // Nothing to do
            // $query['description'] = $query['description'];
            // unset($query['description']);
        }
        if (isset($query['slug'])) {
            // Nothing to do
            // $query['slug'] = $query['slug'];
            // unset($query['slug']);
        }
        if (isset($query['name'])) {
            // Nothing to do
            // $query['name'] = $query['name'];
            // unset($query['name']);
        }
        if (isset($query['taxonomy-name'])) {
            $query['taxonomy'] = $query['taxonomy-name'];
            unset($query['taxonomy-name']);
        }

        return App::applyFilters(
            self::HOOK_QUERY,
            $query
        );
    }
    /**
     * @param array<string,mixed> $data
     * @return string|int the ID of the created category
     * @throws CategoryCRUDMutationException If there was an error (eg: some Custom Post creation validation failed)
     */
    public function createCategory(array $data): string|int
    {
        // Convert the parameters
        $data = $this->convertCategoriesMutationQuery($data);
        $postIDOrError = \wp_insert_post($data, true);
        if ($postIDOrError instanceof WP_Error) {
            /** @var WP_Error */
            $wpError = $postIDOrError;
            throw $this->createCategoryCRUDMutationException($wpError);
        }
        /** @var int */
        $postID = $postIDOrError;
        return $postID;
    }

    protected function createCategoryCRUDMutationException(WP_Error $wpError): CategoryCRUDMutationException
    {
        return new CategoryCRUDMutationException(
            $wpError->get_error_message(),
            $wpError->get_error_code() ? $wpError->get_error_code() : null,
            $this->getWPErrorData($wpError),
        );
    }

    /**
     * @param array<string,mixed> $data
     * @return string|int the ID of the updated category
     * @throws CategoryCRUDMutationException If there was an error (eg: Custom Post does not exist)
     */
    public function updateCategory(array $data): string|int
    {
        // Convert the parameters
        $data = $this->convertCategoriesMutationQuery($data);
        $postIDOrError = \wp_update_post($data, true);
        if ($postIDOrError instanceof WP_Error) {
            /** @var WP_Error */
            $wpError = $postIDOrError;
            throw $this->createCategoryCRUDMutationException($wpError);
        }
        /** @var int */
        $postID = $postIDOrError;
        return $postID;
    }

    public function canUserEditCategory(string|int $userID, string|int $categoryID): bool
    {
        return user_can((int)$userID, 'edit_post', $categoryID);
    }
}
