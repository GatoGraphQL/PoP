<?php

class GD_URE_Module_Processor_CustomContents extends PoP_Module_Processor_ContentsBase
{
    public final const MODULE_URE_CONTENT_MEMBER = 'ure-content-member';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_URE_CONTENT_MEMBER],
        );
    }

    public function getInnerSubmodule(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_URE_CONTENT_MEMBER:
                return [GD_URE_Module_Processor_CustomContentInners::class, GD_URE_Module_Processor_CustomContentInners::COMPONENT_URE_CONTENTINNER_MEMBER];
        }

        return parent::getInnerSubmodule($component);
    }
}


