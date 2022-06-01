<?php

class PoP_Module_Processor_SettingsFeedbackMessageInners extends PoP_Module_Processor_ActionExecutionFeedbackMessageInnersBase
{
    public final const COMPONENT_FEEDBACKMESSAGEINNER_SETTINGS = 'feedbackmessageinner-settings';

    public function getComponentNamesToProcess(): array
    {
        return array(
            self::COMPONENT_FEEDBACKMESSAGEINNER_SETTINGS,
        );
    }

    public function getLayoutSubcomponents(\PoP\ComponentModel\Component\Component $component)
    {
        $ret = parent::getLayoutSubcomponents($component);

        $layouts = array(
            self::COMPONENT_FEEDBACKMESSAGEINNER_SETTINGS => [PoP_Module_Processor_SettingsFeedbackMessageAlertLayouts::class, PoP_Module_Processor_SettingsFeedbackMessageAlertLayouts::COMPONENT_LAYOUT_FEEDBACKMESSAGEALERT_SETTINGS],
        );

        if ($layout = $layouts[$component->name] ?? null) {
            $ret[] = $layout;
        }

        return $ret;
    }
}



