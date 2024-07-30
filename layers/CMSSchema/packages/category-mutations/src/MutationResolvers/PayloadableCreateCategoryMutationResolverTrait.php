<?php

declare(strict_types=1);

namespace PoPCMSSchema\CategoryMutations\MutationResolvers;

use PoPCMSSchema\CategoryMutations\Exception\CategoryCRUDMutationException;
use PoPSchema\SchemaCommons\MutationResolvers\PayloadableMutationResolverTrait;
use PoP\ComponentModel\Feedback\ObjectTypeFieldResolutionFeedbackStore;
use PoP\ComponentModel\QueryResolution\FieldDataAccessorInterface;
use PoP\Root\Exception\AbstractException;

trait PayloadableCreateCategoryMutationResolverTrait
{
    use PayloadableMutationResolverTrait, CreateCategoryMutationResolverTrait {
        CreateCategoryMutationResolverTrait::executeMutation as upstreamExecuteMutation;
        PayloadableMutationResolverTrait::validate insteadof CreateCategoryMutationResolverTrait;
    }
    use PayloadableCategoryMutationResolverTrait;

    /**
     * Validate the app-level errors when executing the mutation,
     * return them in the Payload.
     *
     * @throws AbstractException In case of error
     */
    public function executeMutation(
        FieldDataAccessorInterface $fieldDataAccessor,
        ObjectTypeFieldResolutionFeedbackStore $objectTypeFieldResolutionFeedbackStore,
    ): mixed {
        $separateObjectTypeFieldResolutionFeedbackStore = new ObjectTypeFieldResolutionFeedbackStore();
        $this->validateCreateErrors($fieldDataAccessor, $separateObjectTypeFieldResolutionFeedbackStore);
        if ($separateObjectTypeFieldResolutionFeedbackStore->getErrors() !== []) {
            return $this->createFailureObjectMutationPayload(
                array_map(
                    $this->createErrorPayloadFromObjectTypeFieldResolutionFeedback(...),
                    $separateObjectTypeFieldResolutionFeedbackStore->getErrors()
                )
            )->getID();
        }

        $categoryID = null;
        try {
            /** @var string|int */
            $categoryID = $this->upstreamExecuteMutation(
                $fieldDataAccessor,
                $separateObjectTypeFieldResolutionFeedbackStore,
            );
        } catch (CategoryCRUDMutationException $customPostCRUDMutationException) {
            return $this->createFailureObjectMutationPayload(
                [
                    $this->createGenericErrorPayloadFromPayloadClientException($customPostCRUDMutationException),
                ]
            )->getID();
        }

        if ($separateObjectTypeFieldResolutionFeedbackStore->getErrors() !== []) {
            return $this->createFailureObjectMutationPayload(
                array_map(
                    $this->createErrorPayloadFromObjectTypeFieldResolutionFeedback(...),
                    $separateObjectTypeFieldResolutionFeedbackStore->getErrors()
                ),
                $categoryID
            )->getID();
        }

        /** @var string|int $categoryID */
        return $this->createSuccessObjectMutationPayload($categoryID)->getID();
    }
}
