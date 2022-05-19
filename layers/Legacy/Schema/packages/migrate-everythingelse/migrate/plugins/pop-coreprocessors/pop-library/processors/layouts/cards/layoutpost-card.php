<?php

class PoP_Module_Processor_PostCardLayouts extends PoP_Module_Processor_PostCardLayoutsBase
{
    public final const COMPONENT_LAYOUTPOST_CARD = 'layoutpost-card';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_LAYOUTPOST_CARD],
        );
    }
}



