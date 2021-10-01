<?php

declare(strict_types=1);

namespace PoP\ComponentModel\ModuleProcessors;

use PoP\ComponentModel\Facades\Schema\SchemaDefinitionServiceFacade;
use PoP\ComponentModel\TypeResolvers\InputTypeResolverInterface;

trait SchemaFilterInputModuleProcessorTrait
{
    public function getSchemaFilterInputTypeResolver(array $module): InputTypeResolverInterface
    {
        return $this->getDefaultSchemaFilterInputTypeResolver();
    }
    protected function getDefaultSchemaFilterInputTypeResolver(): InputTypeResolverInterface
    {
        $schemaDefinitionService = SchemaDefinitionServiceFacade::getInstance();
        return $schemaDefinitionService->getDefaultInputTypeResolver();
    }
    public function getSchemaFilterInputDescription(array $module): ?string
    {
        return null;
    }
    public function getSchemaFilterInputDeprecationDescription(array $module): ?string
    {
        return null;
    }
    public function getSchemaFilterInputDefaultValue(array $module): mixed
    {
        return null;
    }
    public function getSchemaFilterInputTypeModifiers(array $module): ?int
    {
        return null;
    }
}
