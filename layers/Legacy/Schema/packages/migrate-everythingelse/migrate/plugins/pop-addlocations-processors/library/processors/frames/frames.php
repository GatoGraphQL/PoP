<?php

class GD_EM_Module_Processor_CreateLocationFrames extends GD_EM_Module_Processor_CreateLocationFramesBase
{
    public final const COMPONENT_FRAME_CREATELOCATIONMAP = 'em-frame-createlocationmap';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_FRAME_CREATELOCATIONMAP],
        );
    }
}



