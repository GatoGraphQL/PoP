<?php

declare(strict_types=1);

namespace PoPCMSSchema\CustomPostCategoryMutations\FeedbackItemProviders;

use PoP\Root\FeedbackItemProviders\AbstractFeedbackItemProvider;
use PoP\ComponentModel\Feedback\FeedbackCategories;

class MutationErrorFeedbackItemProvider extends AbstractFeedbackItemProvider
{
    public final const E1 = 'e1';
    public final const E2 = 'e2';
    public final const E3 = 'e3';
    public final const E4 = 'e4';

    /**
     * @return string[]
     */
    public function getCodes(): array
    {
        return [
            self::E1,
            self::E2,
            self::E3,
            self::E4,
        ];
    }

    public function getMessagePlaceholder(string $code): string
    {
        return match ($code) {
            self::E1 => $this->__('You must be logged in to set categories on custom posts', 'custompost-category-mutations'),
            self::E2 => $this->__('There are no categories with ID(s) \'%s\' for taxonomy \'%s\'', 'custompost-category-mutations'),
            self::E3 => $this->__('There are no categories with slug(s) \'%s\' for taxonomy \'%s\'', 'custompost-category-mutations'),
            self::E4 => $this->__('There are no category taxonomies registered for custom post type \'%s\'', 'custompost-category-mutations'),
            default => parent::getMessagePlaceholder($code),
        };
    }

    public function getCategory(string $code): string
    {
        return FeedbackCategories::ERROR;
    }
}
