<?php

class PoP_Module_Processor_Menus extends PoP_Module_Processor_ContentsBase
{
    public final const MODULE_DROPDOWNBUTTONMENU_TOP = 'dropdownbuttonmenu-top';
    public final const MODULE_DROPDOWNBUTTONMENU_SIDE = 'dropdownbuttonmenu-side';
    public final const MODULE_MULTITARGETINDENTMENU = 'multitargetindentmenu';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_DROPDOWNBUTTONMENU_TOP],
            [self::class, self::COMPONENT_DROPDOWNBUTTONMENU_SIDE],
            [self::class, self::COMPONENT_MULTITARGETINDENTMENU],
        );
    }

    public function getInnerSubmodule(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_DROPDOWNBUTTONMENU_TOP:
                return [PoP_Module_Processor_MenuContentInners::class, PoP_Module_Processor_MenuContentInners::COMPONENT_CONTENTINNER_MENU_DROPDOWNBUTTON_TOP];
            
            case self::COMPONENT_DROPDOWNBUTTONMENU_SIDE:
                return [PoP_Module_Processor_MenuContentInners::class, PoP_Module_Processor_MenuContentInners::COMPONENT_CONTENTINNER_MENU_DROPDOWNBUTTON_SIDE];

            case self::COMPONENT_MULTITARGETINDENTMENU:
                return [PoP_Module_Processor_MenuContentInners::class, PoP_Module_Processor_MenuContentInners::COMPONENT_CONTENTINNER_MENU_MULTITARGETINDENT];
        }

        return getInnerSubmodule($component);
    }
}


