<?php

class PoP_NotificationsProcessors_MyPreferencesHooks
{
    public function __construct()
    {
        \PoP\Root\App::addFilter(
            'PoP_Module_Processor_UserMultipleComponents:emaildigests:modules',
            $this->getEmaildigestsForminputgroups(...)
        );
    }

    public function getEmaildigestsForminputgroups($components)
    {
        array_splice(
            $components, 
            array_search(
                [PoP_Module_Processor_UserCodes::class, PoP_Module_Processor_UserCodes::COMPONENT_CODE_EMAILDIGESTS_LABEL], 
                $components
            )+1, 
            0, 
            array(
                [PoP_Notifications_Module_Processor_EmailFormGroups::class, PoP_Notifications_Module_Processor_EmailFormGroups::COMPONENT_FORMINPUTGROUP_EMAILDIGESTS_DAILYNOTIFICATIONS],
            )
        );
        return $components;
    }
}


/**
 * Initialization
 */
new PoP_NotificationsProcessors_MyPreferencesHooks();
