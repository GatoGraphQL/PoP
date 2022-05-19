<?php

class GD_URE_Module_Processor_MemberTagsLayouts extends GD_URE_Module_Processor_MemberTagsLayoutsBase
{
    public final const COMPONENT_URE_LAYOUTUSER_MEMBERTAGS = 'ure-layoutuser-membertags-nodesc';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_URE_LAYOUTUSER_MEMBERTAGS],
        );
    }
}


