<?php

abstract class PoP_Module_QuickviewFrameBottomOptionsPageSectionComponentRoutingProcessorBase extends \PoP\ComponentRouting\AbstractComponentRoutingProcessor
{
    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return array(POP_PAGECOMPONENTGROUP_PAGESECTION_QUICKVIEWFRAMEBOTTOMOPTIONS);
    }
}
