<?php

abstract class PoP_Module_Processor_MySectionBlocksBase extends PoP_Module_Processor_SectionBlocksBase
{
    protected function showDisabledLayerIfCheckpointFailed(array $component, array &$props)
    {
        return true;
    }
}
