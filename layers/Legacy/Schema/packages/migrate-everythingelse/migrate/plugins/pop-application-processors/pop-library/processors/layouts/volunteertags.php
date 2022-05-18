<?php

class PoP_Module_Processor_VolunteerTagLayouts extends PoP_Module_Processor_VolunteerTagLayoutsBase
{
    public final const MODULE_LAYOUT_POSTADDITIONAL_VOLUNTEER = 'layout-postadditional-volunteer';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_LAYOUT_POSTADDITIONAL_VOLUNTEER],
        );
    }

    public function initModelProps(array $component, array &$props): void
    {
        switch ($component[1]) {
            case self::COMPONENT_LAYOUT_POSTADDITIONAL_VOLUNTEER:
                $this->appendProp($component, $props, 'class', 'label label-warning');
                break;
        }

        parent::initModelProps($component, $props);
    }
}



