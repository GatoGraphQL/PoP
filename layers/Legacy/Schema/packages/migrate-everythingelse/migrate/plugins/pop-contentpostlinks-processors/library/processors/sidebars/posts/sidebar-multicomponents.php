<?php

class PoP_ContentPostLinks_Module_Processor_CustomPostMultipleSidebarComponents extends PoP_Module_Processor_MultiplesBase
{
    public final const COMPONENT_SIDEBARMULTICOMPONENT_LINK = 'sidebarmulticomponent-link';

    public function getComponentNamesToProcess(): array
    {
        return array(
            self::COMPONENT_SIDEBARMULTICOMPONENT_LINK,
        );
    }

    public function getSubcomponents(\PoP\ComponentModel\Component\Component $component): array
    {
        $ret = parent::getSubcomponents($component);

        switch ($component->name) {
            case self::COMPONENT_SIDEBARMULTICOMPONENT_LINK:
                $ret[] = [PoP_ContentPostLinks_Module_Processor_CustomPostWidgets::class, PoP_ContentPostLinks_Module_Processor_CustomPostWidgets::COMPONENT_WIDGETCOMPACT_LINKINFO];
                $ret[] = [PoP_Module_Processor_SidebarComponentWrappers::class, PoP_Module_Processor_SidebarComponentWrappers::COMPONENT_WIDGETWRAPPER_REFERENCES];
                $ret[] = [PoP_Module_Processor_Widgets::class, PoP_Module_Processor_Widgets::COMPONENT_WIDGETCOMPACT_POST_AUTHORS];
                break;
        }

        return $ret;
    }
}



