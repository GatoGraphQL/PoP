<?php

class PoP_ContentPostLinksCreation_Module_Processor_SidebarInners extends PoP_Module_Processor_MultiplesBase
{
    public final const COMPONENT_MULTIPLE_SECTIONINNER_MYCONTENTPOSTLINKS_SIDEBAR = 'multiple-sectioninner-mycontentpostlinks-sidebar';

    public function getComponentNamesToProcess(): array
    {
        return array(
            self::COMPONENT_MULTIPLE_SECTIONINNER_MYCONTENTPOSTLINKS_SIDEBAR,
        );
    }

    public function getSubcomponents(\PoP\ComponentModel\Component\Component $component): array
    {
        $ret = parent::getSubcomponents($component);

        switch ($component->name) {
            case self::COMPONENT_MULTIPLE_SECTIONINNER_MYCONTENTPOSTLINKS_SIDEBAR:
                $ret[] = [GD_Custom_Module_Processor_ButtonGroups::class, GD_Custom_Module_Processor_ButtonGroups::COMPONENT_BUTTONGROUP_MYCONTENT];
                $ret[] = [PoP_ContentPostLinksCreation_Module_Processor_CustomDelegatorFilters::class, PoP_ContentPostLinksCreation_Module_Processor_CustomDelegatorFilters::COMPONENT_DELEGATORFILTER_MYCONTENTPOSTLINKS];
                break;
        }
        
        return $ret;
    }
}


