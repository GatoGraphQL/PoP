<?php
define('GD_CONSTANT_FULLVIEW_TITLEPOSITION_TOP', 'top');
define('GD_CONSTANT_FULLVIEW_TITLEPOSITION_BODY', 'body');

use PoP\ComponentModel\Facades\ComponentProcessors\ComponentProcessorManagerFacade;

abstract class PoP_Module_Processor_FullViewLayoutsBase extends PoP_Module_Processor_FullObjectLayoutsBase
{
    public function getTemplateResource(array $component, array &$props): ?array
    {
        return [PoP_CoreProcessors_TemplateResourceLoaderProcessor::class, PoP_CoreProcessors_TemplateResourceLoaderProcessor::RESOURCE_LAYOUT_FULLVIEW];
    }

    /**
     * @todo Migrate from string to LeafModuleField
     *
     * @return \PoP\ComponentModel\GraphQLEngine\Model\ComponentModelSpec\LeafModuleField[]
     */
    public function getDataFields(array $component, array &$props): array
    {
        return array_merge(
            parent::getDataFields($component, $props),
            array('catSlugs')
        );
    }

    public function titlePosition(array $component, array &$props)
    {
        return GD_CONSTANT_FULLVIEW_TITLEPOSITION_TOP;
    }

    public function getSubComponents(array $component): array
    {
        $ret = parent::getSubComponents($component);

        if ($abovecontent_components = $this->getAbovecontentSubmodules($component)) {
            $ret = array_merge(
                $ret,
                $abovecontent_components
            );
        }

        if ($content_components = $this->getContentSubmodules($component)) {
            $ret = array_merge(
                $ret,
                $content_components
            );
        }

        return $ret;
    }

    public function getAbovecontentSubmodules(array $component)
    {
        return array();
    }

    public function getContentSubmodules(array $component)
    {
        return array(
            [PoP_Module_Processor_ContentLayouts::class, PoP_Module_Processor_ContentLayouts::COMPONENT_LAYOUT_CONTENT_POSTFEED],
        );
    }

    public function getImmutableConfiguration(array $component, array &$props): array
    {
        $ret = parent::getImmutableConfiguration($component, $props);

        $componentprocessor_manager = ComponentProcessorManagerFacade::getInstance();

        $ret[GD_JS_CLASSES]['title'] = '';

        if ($this->getTitleSubmodule($component, $props)) {
            $ret['title-position'] = $this->titlePosition($component, $props);
        }

        if ($abovecontent_components = $this->getAbovecontentSubmodules($component)) {
            $ret[GD_JS_SUBMODULEOUTPUTNAMES]['abovecontent'] = array_map(
                [\PoP\ComponentModel\Facades\Modules\ModuleHelpersFacade::getInstance(), 'getModuleOutputName'],
                $abovecontent_components
            );
        }

        if ($content_components = $this->getContentSubmodules($component)) {
            $ret[GD_JS_SUBMODULEOUTPUTNAMES]['content'] = array_map(
                [\PoP\ComponentModel\Facades\Modules\ModuleHelpersFacade::getInstance(), 'getModuleOutputName'],
                $content_components
            );
        }

        return $ret;
    }

    // function getJsmethods(array $component, array &$props) {

    //     $ret = parent::getJsmethods($component, $props);
    //     $this->addJsmethod($ret, 'waypointsHistoryStateNew');
    //     return $ret;
    // }

    public function initModelProps(array $component, array &$props): void
    {

        // Make it waypoint
        $this->appendProp($component, $props, 'class', 'waypoint');

        // This one is independent of Waypoint because of the History, so here add them as separate instructions (just to make it clear)
        // $this->appendProp($component, $props, 'class', 'module-historystate newstate');
        parent::initModelProps($component, $props);
    }
}
