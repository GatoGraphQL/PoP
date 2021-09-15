<?php

declare(strict_types=1);

namespace PoPSchema\Comments\Enums;

use PoPSchema\Comments\Constants\CommentTypes;
use PoP\ComponentModel\Enums\AbstractEnum;

class CommentTypeEnum extends AbstractEnum
{
    public function getTypeName(): string
    {
        return 'CommentType';
    }
    public function getValues(): array
    {
        return [
            CommentTypes::COMMENT,
            CommentTypes::TRACKBACK,
            CommentTypes::PINGBACK,
        ];
    }
}
