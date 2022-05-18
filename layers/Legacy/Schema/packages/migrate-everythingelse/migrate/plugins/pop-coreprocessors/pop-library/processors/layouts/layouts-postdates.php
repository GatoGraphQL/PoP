<?php

class PoP_Module_Processor_PostDateLayouts extends PoP_Module_Processor_PostDateLayoutsBase
{
    public final const MODULE_LAYOUTPOST_DATE = 'layoutpost-date';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_LAYOUTPOST_DATE],
        );
    }
}



