<?php
use PoP\ComponentModel\Facades\ComponentProcessors\ComponentProcessorManagerFacade;

abstract class PoP_Module_Processor_MapDivsBase extends PoPEngine_QueryDataComponentProcessorBase
{
    public function getTemplateResource(array $component, array &$props): ?array
    {
        return [PoP_Locations_TemplateResourceLoaderProcessor::class, PoP_Locations_TemplateResourceLoaderProcessor::RESOURCE_MAP_DIV];
    }

    public function getSubcomponents(array $component): array
    {
        $ret = parent::getSubcomponents($component);

        if ($inners = $this->getInnerSubcomponents($component)) {
            $ret = array_merge(
                $ret,
                $inners
            );
        }

        return $ret;
    }

    public function getInnerSubcomponents(array $component): array
    {
        return array();
    }

    public function getImmutableConfiguration(array $component, array &$props): array
    {
        $componentprocessor_manager = ComponentProcessorManagerFacade::getInstance();

        $ret = parent::getImmutableConfiguration($component, $props);

        if ($inners = $this->getInnerSubcomponents($component)) {
            $ret[GD_JS_SUBCOMPONENTOUTPUTNAMES]['inners'] = array_map(
                [\PoP\ComponentModel\Facades\Modules\ComponentHelpersFacade::getInstance(), 'getComponentOutputName'],
                $inners
            );
        }

        return $ret;
    }

    public function getJsmethods(array $component, array &$props)
    {
        $ret = parent::getJsmethods($component, $props);

        $this->addJsmethod($ret, 'map');

        return $ret;
    }

    public function openOnemarkerInfowindow(array $component, array &$props)
    {
        return true;
    }

    public function initModelProps(array $component, array &$props): void
    {

        // Open the infoWindow automatically when the map has only 1 marker?
        $this->setProp($component, $props, 'open-onemarker-infowindow', $this->openOnemarkerInfowindow($component, $props));
        if ($this->getProp($component, $props, 'open-onemarker-infowindow')) {
            $this->mergeProp(
                $component,
                $props,
                'params',
                array(
                    'data-open-onemarker-infowindow' => true
                )
            );
        }

        parent::initModelProps($component, $props);
    }
}
