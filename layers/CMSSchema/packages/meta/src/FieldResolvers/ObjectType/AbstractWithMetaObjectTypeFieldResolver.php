<?php

declare(strict_types=1);

namespace PoPCMSSchema\Meta\FieldResolvers\ObjectType;

use PoP\ComponentModel\FieldResolvers\ObjectType\AbstractObjectTypeFieldResolver;
use PoP\ComponentModel\QueryResolution\FieldDataAccessorInterface;
use PoP\ComponentModel\TypeResolvers\ObjectType\ObjectTypeResolverInterface;
use PoP\GraphQLParser\Spec\Parser\Ast\FieldInterface;
use PoP\Root\Feedback\FeedbackItemResolution;
use PoPCMSSchema\Meta\FeedbackItemProviders\FeedbackItemProvider;
use PoPCMSSchema\Meta\FieldResolvers\InterfaceType\WithMetaInterfaceTypeFieldResolver;
use PoPCMSSchema\Meta\TypeAPIs\MetaTypeAPIInterface;

abstract class AbstractWithMetaObjectTypeFieldResolver extends AbstractObjectTypeFieldResolver
{
    private ?WithMetaInterfaceTypeFieldResolver $withMetaInterfaceTypeFieldResolver = null;

    final public function setWithMetaInterfaceTypeFieldResolver(WithMetaInterfaceTypeFieldResolver $withMetaInterfaceTypeFieldResolver): void
    {
        $this->withMetaInterfaceTypeFieldResolver = $withMetaInterfaceTypeFieldResolver;
    }
    final protected function getWithMetaInterfaceTypeFieldResolver(): WithMetaInterfaceTypeFieldResolver
    {
        return $this->withMetaInterfaceTypeFieldResolver ??= $this->instanceManager->getInstance(WithMetaInterfaceTypeFieldResolver::class);
    }

    public function getImplementedInterfaceTypeFieldResolvers(): array
    {
        return [
            $this->getWithMetaInterfaceTypeFieldResolver(),
        ];
    }

    public function getFieldNamesToResolve(): array
    {
        return [
            'metaValue',
            'metaValues',
        ];
    }

    abstract protected function getMetaTypeAPI(): MetaTypeAPIInterface;

    /**
     * Custom validations
     *
     * @return FeedbackItemResolution[] Errors
     */
    public function validateFieldKeyValues(
        ObjectTypeResolverInterface $objectTypeResolver,
        FieldDataAccessorInterface $fieldDataAccessor,
    ): array {
        $errors = parent::validateFieldKeyValues($objectTypeResolver, $fieldDataAccessor);
        switch ($fieldDataAccessor->getFieldName()) {
            case 'metaValue':
            case 'metaValues':
                if (!$this->getMetaTypeAPI()->validateIsMetaKeyAllowed($fieldDataAccessor->getValue('key'))) {
                    $errors[] = new FeedbackItemResolution(
                        FeedbackItemProvider::class,
                        FeedbackItemProvider::E1,
                        [
                            $fieldDataAccessor->getValue('key'),
                        ]
                    );
                }
                break;
        }

        return $errors;
    }

    public function validateResolvedFieldType(
        ObjectTypeResolverInterface $objectTypeResolver,
        FieldInterface $field,
    ): bool {
        switch ($field->getName()) {
            case 'metaValue':
            case 'metaValues':
                return true;
        }
        return parent::validateResolvedFieldType(
            $objectTypeResolver,
            $field,
        );
    }
}
