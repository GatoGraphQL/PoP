<?php

class GD_Custom_EM_Module_Processor_CustomPostLayoutSidebarInners extends PoP_Module_Processor_SidebarInnersBase
{
    public final const COMPONENT_LAYOUT_POSTSIDEBARINNER_VERTICAL_LOCATIONPOST = 'layout-postsidebarinner-vertical-locationpost';
    public final const COMPONENT_LAYOUT_POSTSIDEBARINNER_HORIZONTAL_LOCATIONPOST = 'layout-postsidebarinner-horizontal-locationpost';
    public final const COMPONENT_LAYOUT_POSTSIDEBARINNER_COMPACTHORIZONTAL_LOCATIONPOST = 'layout-postsidebarinner-compacthorizontal-locationpost';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_LAYOUT_POSTSIDEBARINNER_VERTICAL_LOCATIONPOST],
            [self::class, self::COMPONENT_LAYOUT_POSTSIDEBARINNER_HORIZONTAL_LOCATIONPOST],
            [self::class, self::COMPONENT_LAYOUT_POSTSIDEBARINNER_COMPACTHORIZONTAL_LOCATIONPOST],
        );
    }

    public function getLayoutSubcomponents(array $component)
    {
        $ret = parent::getLayoutSubcomponents($component);

        switch ($component[1]) {
            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_VERTICAL_LOCATIONPOST:
            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_HORIZONTAL_LOCATIONPOST:
                $ret = array_merge(
                    $ret,
                    Custom_EM_FullViewSidebarSettings::getSidebarSubcomponents(GD_SIDEBARSECTION_LOCATIONPOST)
                );
                break;

            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_COMPACTHORIZONTAL_LOCATIONPOST:
                $ret = array_merge(
                    $ret,
                    Custom_EM_FullViewSidebarSettings::getSidebarSubcomponents(GD_COMPACTSIDEBARSECTION_LOCATIONPOST)
                );
                break;
        }
        
        return $ret;
    }

    public function getWrapperClass(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_HORIZONTAL_LOCATIONPOST:
            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_COMPACTHORIZONTAL_LOCATIONPOST:
                return 'row';
        }
    
        return parent::getWrapperClass($component);
    }
    
    public function getWidgetwrapperClass(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_HORIZONTAL_LOCATIONPOST:
                return 'col-xsm-4';
            
            case self::COMPONENT_LAYOUT_POSTSIDEBARINNER_COMPACTHORIZONTAL_LOCATIONPOST:
                return 'col-xsm-6';
        }
    
        return parent::getWidgetwrapperClass($component);
    }
}



