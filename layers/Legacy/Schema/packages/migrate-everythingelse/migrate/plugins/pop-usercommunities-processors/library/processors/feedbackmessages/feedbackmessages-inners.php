<?php

class GD_URE_Module_Processor_ProfileFeedbackMessageInners extends PoP_Module_Processor_ActionExecutionFeedbackMessageInnersBase
{
    public final const MODULE_FEEDBACKMESSAGEINNER_UPDATEMYCOMMUNITIES = 'feedbackmessageinner-updatemycommunities';
    public final const MODULE_FEEDBACKMESSAGEINNER_INVITENEWMEMBERS = 'feedbackmessageinner-invitemembers';
    public final const MODULE_FEEDBACKMESSAGEINNER_EDITMEMBERSHIP = 'feedbackmessageinner-editmembership';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_FEEDBACKMESSAGEINNER_UPDATEMYCOMMUNITIES],
            [self::class, self::COMPONENT_FEEDBACKMESSAGEINNER_INVITENEWMEMBERS],
            [self::class, self::COMPONENT_FEEDBACKMESSAGEINNER_EDITMEMBERSHIP],
        );
    }

    public function getLayoutSubmodules(array $component)
    {
        $ret = parent::getLayoutSubmodules($component);

        $layouts = array(
            self::COMPONENT_FEEDBACKMESSAGEINNER_UPDATEMYCOMMUNITIES => [GD_URE_Module_Processor_ProfileFeedbackMessageAlertLayouts::class, GD_URE_Module_Processor_ProfileFeedbackMessageAlertLayouts::COMPONENT_LAYOUT_FEEDBACKMESSAGEALERT_UPDATEMYCOMMUNITIES],
            self::COMPONENT_FEEDBACKMESSAGEINNER_INVITENEWMEMBERS => [GD_URE_Module_Processor_ProfileFeedbackMessageAlertLayouts::class, GD_URE_Module_Processor_ProfileFeedbackMessageAlertLayouts::COMPONENT_LAYOUT_FEEDBACKMESSAGEALERT_INVITENEWMEMBERS],
            self::COMPONENT_FEEDBACKMESSAGEINNER_EDITMEMBERSHIP => [GD_URE_Module_Processor_ProfileFeedbackMessageAlertLayouts::class, GD_URE_Module_Processor_ProfileFeedbackMessageAlertLayouts::COMPONENT_LAYOUT_FEEDBACKMESSAGEALERT_EDITMEMBERSHIP],
        );

        if ($layout = $layouts[$component[1]] ?? null) {
            $ret[] = $layout;
        }

        return $ret;
    }
}



