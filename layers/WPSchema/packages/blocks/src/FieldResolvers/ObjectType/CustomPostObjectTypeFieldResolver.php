<?php

declare(strict_types=1);

namespace PoPWPSchema\Blocks\FieldResolvers\ObjectType;

use PoPCMSSchema\CustomPosts\TypeResolvers\ObjectType\AbstractCustomPostObjectTypeResolver;
use PoPWPSchema\BlockContentParser\BlockContentParserInterface;
use PoPWPSchema\BlockContentParser\Exception\BlockContentParserException;
use PoPWPSchema\Blocks\ObjectModels\BlockInterface;
use PoPWPSchema\Blocks\ObjectModels\GeneralBlock;
use PoPWPSchema\Blocks\TypeResolvers\InputObjectType\BlockFilterByInputObjectTypeResolver;
use PoPWPSchema\Blocks\TypeResolvers\UnionType\BlockUnionTypeResolver;
use PoP\ComponentModel\FeedbackItemProviders\GenericFeedbackItemProvider;
use PoP\ComponentModel\Feedback\ObjectTypeFieldResolutionFeedback;
use PoP\ComponentModel\Feedback\ObjectTypeFieldResolutionFeedbackStore;
use PoP\ComponentModel\FieldResolvers\ObjectType\AbstractQueryableObjectTypeFieldResolver;
use PoP\ComponentModel\QueryResolution\FieldDataAccessorInterface;
use PoP\ComponentModel\Schema\SchemaTypeModifiers;
use PoP\ComponentModel\TypeResolvers\ConcreteTypeResolverInterface;
use PoP\ComponentModel\TypeResolvers\ObjectType\ObjectTypeResolverInterface;
use PoP\Engine\FeedbackItemProviders\ErrorFeedbackItemProvider as EngineErrorFeedbackItemProvider;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use PoP\Root\Feedback\FeedbackItemResolution;
use WP_Post;
use stdClass;

class CustomPostObjectTypeFieldResolver extends AbstractQueryableObjectTypeFieldResolver
{
    private ?BlockUnionTypeResolver $blockUnionTypeResolver = null;
    private ?BlockContentParserInterface $blockContentParser = null;
    private ?BlockFilterByInputObjectTypeResolver $blockFilterByInputObjectTypeResolver = null;

    final public function setBlockUnionTypeResolver(BlockUnionTypeResolver $blockUnionTypeResolver): void
    {
        $this->blockUnionTypeResolver = $blockUnionTypeResolver;
    }
    final protected function getBlockUnionTypeResolver(): BlockUnionTypeResolver
    {
        /** @var BlockUnionTypeResolver */
        return $this->blockUnionTypeResolver ??= $this->instanceManager->getInstance(BlockUnionTypeResolver::class);
    }
    final public function setBlockContentParser(BlockContentParserInterface $blockContentParser): void
    {
        $this->blockContentParser = $blockContentParser;
    }
    final protected function getBlockContentParser(): BlockContentParserInterface
    {
        /** @var BlockContentParserInterface */
        return $this->blockContentParser ??= $this->instanceManager->getInstance(BlockContentParserInterface::class);
    }
    final public function setBlockFilterByInputObjectTypeResolver(BlockFilterByInputObjectTypeResolver $blockFilterByInputObjectTypeResolver): void
    {
        $this->blockFilterByInputObjectTypeResolver = $blockFilterByInputObjectTypeResolver;
    }
    final protected function getBlockFilterByInputObjectTypeResolver(): BlockFilterByInputObjectTypeResolver
    {
        /** @var BlockFilterByInputObjectTypeResolver */
        return $this->blockFilterByInputObjectTypeResolver ??= $this->instanceManager->getInstance(BlockFilterByInputObjectTypeResolver::class);
    }

    /**
     * @return array<class-string<ObjectTypeResolverInterface>>
     */
    public function getObjectTypeResolverClassesToAttachTo(): array
    {
        return [
            AbstractCustomPostObjectTypeResolver::class,
        ];
    }

    /**
     * @return string[]
     */
    public function getFieldNamesToResolve(): array
    {
        return [
            'blocks',
        ];
    }

    public function getFieldDescription(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): ?string
    {
        return match ($fieldName) {
            'blocks' => $this->__('(Gutenberg) Blocks in a custom post', 'blocks'),
            default => parent::getFieldDescription($objectTypeResolver, $fieldName),
        };
    }

    public function getFieldTypeResolver(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): ConcreteTypeResolverInterface
    {
        return match ($fieldName) {
            'blocks' => $this->getBlockUnionTypeResolver(),
            default => parent::getFieldTypeResolver($objectTypeResolver, $fieldName),
        };
    }

    public function getFieldTypeModifiers(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): int
    {
        return match ($fieldName) {
            'blocks' => SchemaTypeModifiers::IS_ARRAY | SchemaTypeModifiers::IS_NON_NULLABLE_ITEMS_IN_ARRAY,
            default => parent::getFieldTypeModifiers($objectTypeResolver, $fieldName),
        };
    }

    /**
     * @return array<string,InputTypeResolverInterface>
     */
    public function getFieldArgNameTypeResolvers(ObjectTypeResolverInterface $objectTypeResolver, string $fieldName): array
    {
        $fieldArgNameTypeResolvers = parent::getFieldArgNameTypeResolvers($objectTypeResolver, $fieldName);
        return match ($fieldName) {
            'blocks' => array_merge(
                $fieldArgNameTypeResolvers,
                [
                    'filterBy' => $this->getBlockFilterByInputObjectTypeResolver(),
                ]
            ),
            default => $fieldArgNameTypeResolvers,
        };
    }

    public function resolveValue(
        ObjectTypeResolverInterface $objectTypeResolver,
        object $object,
        FieldDataAccessorInterface $fieldDataAccessor,
        ObjectTypeFieldResolutionFeedbackStore $objectTypeFieldResolutionFeedbackStore,
    ): mixed {
        $query = $this->convertFieldArgsToFilteringQueryArgs($objectTypeResolver, $fieldDataAccessor);
        /** @var WP_Post */
        $customPost = $object;
        switch ($fieldDataAccessor->getFieldName()) {
            case 'blocks':
                $blockContentParserPayload = null;
                try {
                    $filterOptions = [];
                    if ($query['filterBy']['include'] ?? null) {
                        $filterOptions = $query['filterBy']['include'];
                    } elseif ($query['filterBy']['exclude'] ?? null) {
                        $filterOptions = $query['filterBy']['exclude'];
                    }
                    $blockContentParserPayload = $this->getBlockContentParser()->parseCustomPostIntoBlockDataItems($customPost, $filterOptions);
                } catch (BlockContentParserException $e) {
                    $objectTypeFieldResolutionFeedbackStore->addError(
                        new ObjectTypeFieldResolutionFeedback(
                            new FeedbackItemResolution(
                                EngineErrorFeedbackItemProvider::class,
                                EngineErrorFeedbackItemProvider::E7,
                                [
                                    $e->getMessage(),
                                ]
                            ),
                            $fieldDataAccessor->getField(),
                        )
                    );
                    return null;
                }

                if ($blockContentParserPayload === null) {
                    return $blockContentParserPayload;
                }

                if ($blockContentParserPayload->warnings !== null) {
                    foreach ($blockContentParserPayload->warnings as $warning) {
                        $objectTypeFieldResolutionFeedbackStore->addWarning(
                            new ObjectTypeFieldResolutionFeedback(
                                new FeedbackItemResolution(
                                    GenericFeedbackItemProvider::class,
                                    GenericFeedbackItemProvider::W1,
                                    [
                                        $warning,
                                    ]
                                ),
                                $fieldDataAccessor->getField(),
                            )
                        );
                    }
                }

                /** @var BlockInterface[] */
                $blocks = array_map(
                    $this->createBlock(...),
                    $blockContentParserPayload->blocks
                );
                return array_map(
                    fn (BlockInterface $block) => $block->getID(),
                    $blocks
                );
        }

        return parent::resolveValue($objectTypeResolver, $object, $fieldDataAccessor, $objectTypeFieldResolutionFeedbackStore);
    }

    /**
     * Given the name, attributes, and inner block data for a block,
     * create Block object.
     */
    protected function createBlock(stdClass $blockItem): BlockInterface
    {
        $innerBlocks = null;
        if (isset($blockItem->innerBlocks)) {
            /** @var array<stdClass> */
            $blockInnerBlocks = $blockItem->innerBlocks;
            $innerBlocks = array_map(
                $this->createBlock(...),
                $blockInnerBlocks
            );
        }
        return new GeneralBlock(
            $blockItem->name,
            $blockItem->attributes ?? null,
            $innerBlocks
        );
    }

    /**
     * Since the return type is known for all the fields in this
     * FieldResolver, there's no need to validate them
     */
    public function validateResolvedFieldType(
        ObjectTypeResolverInterface $objectTypeResolver,
        FieldInterface $field,
    ): bool {
        return false;
    }
}
