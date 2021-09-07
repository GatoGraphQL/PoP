<?php

declare(strict_types=1);

namespace PoP\ComponentModel\HelperServices;

use PoP\ComponentModel\Misc\GeneralUtils;
use PoP\ComponentModel\ModuleProcessors\ModuleProcessorManagerInterface;
use PoP\ComponentModel\Schema\FeedbackMessageStoreInterface;
use PoP\ComponentModel\Schema\FieldQueryInterpreterInterface;
use PoP\ComponentModel\Schema\SchemaHelpers;
use PoP\ComponentModel\TypeResolvers\RelationalTypeResolverInterface;
use PoP\Translation\TranslationAPIInterface;

class DataloadHelperService implements DataloadHelperServiceInterface
{
    public function __construct(
        protected FeedbackMessageStoreInterface $feedbackMessageStore,
        protected FieldQueryInterpreterInterface $fieldQueryInterpreter,
        protected TranslationAPIInterface $translationAPI,
        protected ModuleProcessorManagerInterface $moduleProcessorManager,
    ) {
    }

    public function getTypeResolverClassFromSubcomponentDataField(RelationalTypeResolverInterface $relationalTypeResolver, string $subcomponent_data_field): ?string
    {
        // If this field doesn't have a typeResolver, show a schema error
        // But if there are no FieldResolvers, then skip adding an error here, since that error will have been added already
        // Otherwise, there will appear 2 error messages:
        // 1. No FieldResolver
        // 2. No FieldDefaultTypeDataLoader
        $subcomponentFieldTypeResolverClass = $relationalTypeResolver->getFieldTypeResolverClass($subcomponent_data_field);
        if (!SchemaHelpers::isRelationalFieldTypeResolverClass($subcomponentFieldTypeResolverClass) && $relationalTypeResolver->hasFieldResolversForField($subcomponent_data_field)) {
            // If there is an alias, store the results under this. Otherwise, on the fieldName+fieldArgs
            $subcomponent_data_field_outputkey = $this->fieldQueryInterpreter->getFieldOutputKey($subcomponent_data_field);
            $this->feedbackMessageStore->addSchemaError(
                $relationalTypeResolver->getTypeOutputName(),
                $subcomponent_data_field_outputkey,
                sprintf(
                    $this->translationAPI->__('Field \'%s\' is not a connection', 'pop-component-model'),
                    $subcomponent_data_field_outputkey
                )
            );
        }
        return $subcomponentFieldTypeResolverClass;
    }

    /**
     * @param array<array<string, mixed>> $moduleValues
     */
    public function addFilterParams(string $url, array $moduleValues = []): string
    {
        $args = [];
        foreach ($moduleValues as $moduleValue) {
            $module = $moduleValue['module'];
            $value = $moduleValue['value'];
            $moduleprocessor = $this->moduleProcessorManager->getProcessor($module);
            $args[$moduleprocessor->getName($module)] = $value;
        }
        return GeneralUtils::addQueryArgs($args, $url);
    }
}
