<?php

use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoPCMSSchema\CustomPosts\Types\Status;

class PoP_Module_Processor_CustomWrapperLayouts extends PoP_Module_Processor_ConditionWrapperBase
{
    public final const MODULE_LAYOUTWRAPPER_USERPOSTINTERACTION = 'layoutwrapper-userpostinteraction';
    public final const MODULE_LAYOUTWRAPPER_USERHIGHLIGHTPOSTINTERACTION = 'layoutwrapper-userhighlightpostinteraction';
    public final const MODULE_CODEWRAPPER_LAZYLOADINGSPINNER = 'codewrapper-lazyloadingspinner';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_LAYOUTWRAPPER_USERPOSTINTERACTION],
            [self::class, self::COMPONENT_LAYOUTWRAPPER_USERHIGHLIGHTPOSTINTERACTION],
            [self::class, self::COMPONENT_CODEWRAPPER_LAZYLOADINGSPINNER],
        );
    }

    public function getConditionSucceededSubmodules(array $component)
    {
        $ret = parent::getConditionSucceededSubmodules($component);

        switch ($component[1]) {
            case self::COMPONENT_LAYOUTWRAPPER_USERPOSTINTERACTION:
                $ret[] = [Wassup_Module_Processor_UserPostInteractionLayouts::class, Wassup_Module_Processor_UserPostInteractionLayouts::COMPONENT_LAYOUT_USERPOSTINTERACTION];
                break;

            case self::COMPONENT_LAYOUTWRAPPER_USERHIGHLIGHTPOSTINTERACTION:
                $ret[] = [Wassup_Module_Processor_UserPostInteractionLayouts::class, Wassup_Module_Processor_UserPostInteractionLayouts::COMPONENT_LAYOUT_USERHIGHLIGHTPOSTINTERACTION];
                break;

            case self::COMPONENT_CODEWRAPPER_LAZYLOADINGSPINNER:
                $ret[] = [PoP_Module_Processor_LazyLoadingSpinnerLayouts::class, PoP_Module_Processor_LazyLoadingSpinnerLayouts::COMPONENT_LAYOUT_LAZYLOADINGSPINNER];
                break;
        }

        return $ret;
    }

    // function getConditionFailedSubmodules(array $component) {

    //     $ret = parent::getConditionFailedSubmodules($component);

    //     switch ($component[1]) {

    //         case self::COMPONENT_CODEWRAPPER_LAZYLOADINGSPINNER:

    //             // This is needed because we need to print the id no matter what, since this module
    //             // will be referenced using previousmodules-ids in [PoP_Module_Processor_HighlightReferencedbyLayouts::class, PoP_Module_Processor_HighlightReferencedbyLayouts::COMPONENT_LAZYSUBCOMPONENT_HIGHLIGHTS_FULLVIEW], etc
    //             $ret[] = [PoP_Module_Processor_Codes::class, PoP_Module_Processor_Codes::COMPONENT_CODE_EMPTY];
    //             break;
    //     }

    //     return $ret;
    // }

    public function getConditionField(array $component): ?string
    {
        switch ($component[1]) {
            case self::COMPONENT_LAYOUTWRAPPER_USERPOSTINTERACTION:
            case self::COMPONENT_LAYOUTWRAPPER_USERHIGHLIGHTPOSTINTERACTION:
            case self::COMPONENT_CODEWRAPPER_LAZYLOADINGSPINNER:
                return FieldQueryInterpreterFacade::getInstance()->getField('isStatus', ['status' => Status::PUBLISHED], 'published');
        }

        return null;
    }

    public function initModelProps(array $component, array &$props): void
    {
        switch ($component[1]) {
            case self::COMPONENT_LAYOUTWRAPPER_USERPOSTINTERACTION:
            case self::COMPONENT_LAYOUTWRAPPER_USERHIGHLIGHTPOSTINTERACTION:
                $this->appendProp($component, $props, 'class', 'userpostinteraction clearfix');
                break;

            case self::COMPONENT_CODEWRAPPER_LAZYLOADINGSPINNER:
                $this->appendProp($component, $props, 'class', 'loadingmsg clearfix');
                break;
        }

        parent::initModelProps($component, $props);
    }
}



