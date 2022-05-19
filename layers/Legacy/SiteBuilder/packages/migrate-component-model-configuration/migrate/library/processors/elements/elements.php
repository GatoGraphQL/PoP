<?php
use PoP\ConfigurationComponentModel\ComponentProcessors\AbstractComponentProcessor;

class PoP_ConfigurationComponentModel_Module_Processor_Elements extends AbstractComponentProcessor
{
    public final const COMPONENT_EMPTY = 'empty';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_EMPTY],
        );
    }
}
