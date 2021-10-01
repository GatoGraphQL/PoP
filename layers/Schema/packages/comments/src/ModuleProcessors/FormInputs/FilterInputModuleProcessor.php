<?php

declare(strict_types=1);

namespace PoPSchema\Comments\ModuleProcessors\FormInputs;

use PoP\ComponentModel\FormInputs\FormMultipleInput;
use PoP\ComponentModel\ModuleProcessors\AbstractFormInputModuleProcessor;
use PoP\ComponentModel\ModuleProcessors\DataloadQueryArgsFilterInputModuleProcessorInterface;
use PoP\ComponentModel\ModuleProcessors\DataloadQueryArgsSchemaFilterInputModuleProcessorInterface;
use PoP\ComponentModel\ModuleProcessors\DataloadQueryArgsSchemaFilterInputModuleProcessorTrait;
use PoP\ComponentModel\Schema\SchemaTypeModifiers;
use PoP\ComponentModel\TypeResolvers\InputTypeResolverInterface;
use PoP\Engine\TypeResolvers\ScalarType\IDScalarTypeResolver;
use PoPSchema\Comments\Constants\CommentStatus;
use PoPSchema\Comments\Constants\CommentTypes;
use PoPSchema\Comments\FilterInputProcessors\FilterInputProcessor;
use PoPSchema\Comments\TypeResolvers\EnumType\CommentStatusEnumTypeResolver;
use PoPSchema\Comments\TypeResolvers\EnumType\CommentTypeEnumTypeResolver;
use Symfony\Contracts\Service\Attribute\Required;

class FilterInputModuleProcessor extends AbstractFormInputModuleProcessor implements DataloadQueryArgsFilterInputModuleProcessorInterface, DataloadQueryArgsSchemaFilterInputModuleProcessorInterface
{
    use DataloadQueryArgsSchemaFilterInputModuleProcessorTrait;

    public const MODULE_FILTERINPUT_CUSTOMPOST_IDS = 'filterinput-custompost-ids';
    public const MODULE_FILTERINPUT_CUSTOMPOST_ID = 'filterinput-custompost-id';
    public const MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS = 'filterinput-exclude-custompost-ids';
    public const MODULE_FILTERINPUT_COMMENT_TYPES = 'filterinput-comment-types';
    public const MODULE_FILTERINPUT_COMMENT_STATUS = 'filterinput-comment-status';

    protected CommentTypeEnumTypeResolver $commentTypeEnumTypeResolver;
    protected CommentStatusEnumTypeResolver $commentStatusEnumTypeResolver;
    protected IDScalarTypeResolver $idScalarTypeResolver;

    #[Required]
    public function autowireFilterInputModuleProcessor(
        CommentTypeEnumTypeResolver $commentTypeEnumTypeResolver,
        CommentStatusEnumTypeResolver $commentStatusEnumTypeResolver,
        IDScalarTypeResolver $idScalarTypeResolver,
    ): void {
        $this->commentTypeEnumTypeResolver = $commentTypeEnumTypeResolver;
        $this->commentStatusEnumTypeResolver = $commentStatusEnumTypeResolver;
        $this->idScalarTypeResolver = $idScalarTypeResolver;
    }

    public function getModulesToProcess(): array
    {
        return array(
            [self::class, self::MODULE_FILTERINPUT_CUSTOMPOST_IDS],
            [self::class, self::MODULE_FILTERINPUT_CUSTOMPOST_ID],
            [self::class, self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS],
            [self::class, self::MODULE_FILTERINPUT_COMMENT_TYPES],
            [self::class, self::MODULE_FILTERINPUT_COMMENT_STATUS],
        );
    }

    public function getFilterInput(array $module): ?array
    {
        $filterInputs = [
            self::MODULE_FILTERINPUT_CUSTOMPOST_IDS => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_CUSTOMPOST_IDS],
            self::MODULE_FILTERINPUT_CUSTOMPOST_ID => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_CUSTOMPOST_ID],
            self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS],
            self::MODULE_FILTERINPUT_COMMENT_TYPES => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_COMMENT_TYPES],
            self::MODULE_FILTERINPUT_COMMENT_STATUS => [FilterInputProcessor::class, FilterInputProcessor::FILTERINPUT_COMMENT_STATUS],
        ];
        return $filterInputs[$module[1]] ?? null;
    }

    public function getInputClass(array $module): string
    {
        switch ($module[1]) {
            case self::MODULE_FILTERINPUT_CUSTOMPOST_IDS:
            case self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS:
            case self::MODULE_FILTERINPUT_COMMENT_TYPES:
            case self::MODULE_FILTERINPUT_COMMENT_STATUS:
                return FormMultipleInput::class;
        }

        return parent::getInputClass($module);
    }

    public function getName(array $module): string
    {
        // Add a nice name, so that the URL params when filtering make sense
        return match ($module[1]) {
            self::MODULE_FILTERINPUT_CUSTOMPOST_IDS => 'customPostIDs',
            self::MODULE_FILTERINPUT_CUSTOMPOST_ID => 'customPostID',
            self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS => 'excludeCustomPostIDs',
            self::MODULE_FILTERINPUT_COMMENT_TYPES => 'types',
            self::MODULE_FILTERINPUT_COMMENT_STATUS => 'status',
            default => parent::getName($module),
        };
    }

    public function getSchemaFilterInputTypeResolver(array $module): InputTypeResolverInterface
    {
        return match ($module[1]) {
            self::MODULE_FILTERINPUT_CUSTOMPOST_IDS => $this->idScalarTypeResolver,
            self::MODULE_FILTERINPUT_CUSTOMPOST_ID => $this->idScalarTypeResolver,
            self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS => $this->idScalarTypeResolver,
            self::MODULE_FILTERINPUT_COMMENT_TYPES => $this->commentTypeEnumTypeResolver,
            self::MODULE_FILTERINPUT_COMMENT_STATUS => $this->commentStatusEnumTypeResolver,
            default => $this->getDefaultSchemaFilterInputTypeResolver(),
        };
    }

    public function getSchemaFilterInputTypeModifiers(array $module): ?int
    {
        return match ($module[1]) {
            self::MODULE_FILTERINPUT_CUSTOMPOST_IDS,
            self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS,
            self::MODULE_FILTERINPUT_COMMENT_TYPES,
            self::MODULE_FILTERINPUT_COMMENT_STATUS
                => SchemaTypeModifiers::IS_ARRAY | SchemaTypeModifiers::IS_NON_NULLABLE_ITEMS_IN_ARRAY,
            default
                => null,
        };
    }

    public function getSchemaFilterInputDefaultValue(array $module): mixed
    {
        return match ($module[1]) {
            self::MODULE_FILTERINPUT_COMMENT_TYPES => [
                CommentTypes::COMMENT,
            ],
            self::MODULE_FILTERINPUT_COMMENT_STATUS => [
                CommentStatus::APPROVE,
            ],
            default => null,
        };
    }

    public function getSchemaFilterInputDescription(array $module): ?string
    {
        return match ($module[1]) {
            self::MODULE_FILTERINPUT_CUSTOMPOST_IDS => $this->translationAPI->__('Limit results to elements with the given custom post IDs', 'comments'),
            self::MODULE_FILTERINPUT_CUSTOMPOST_ID => $this->translationAPI->__('Limit results to elements with the given custom post ID', 'comments'),
            self::MODULE_FILTERINPUT_EXCLUDE_CUSTOMPOST_IDS => $this->translationAPI->__('Exclude elements with the given custom post IDs', 'comments'),
            self::MODULE_FILTERINPUT_COMMENT_TYPES => $this->translationAPI->__('Types of comment', 'comments'),
            self::MODULE_FILTERINPUT_COMMENT_STATUS => $this->translationAPI->__('Status of the comment', 'comments'),
            default => null,
        };
    }
}
