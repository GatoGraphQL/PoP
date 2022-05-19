<?php

class PoP_LocationPostLinksCreation_Module_Processor_CreateUpdatePostBlocks extends PoP_Module_Processor_AddEditContentBlocksBase
{
    public final const COMPONENT_BLOCK_LOCATIONPOSTLINK_UPDATE = 'block-locationpostlink-update';
    public final const COMPONENT_BLOCK_LOCATIONPOSTLINK_CREATE = 'block-locationpostlink-create';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_BLOCK_LOCATIONPOSTLINK_UPDATE],
            [self::class, self::COMPONENT_BLOCK_LOCATIONPOSTLINK_CREATE],
        );
    }

    public function getRelevantRoute(array $component, array &$props): ?string
    {
        return match($component[1]) {
            self::COMPONENT_BLOCK_LOCATIONPOSTLINK_CREATE => POP_LOCATIONPOSTLINKSCREATION_ROUTE_ADDLOCATIONPOSTLINK,
            self::COMPONENT_BLOCK_LOCATIONPOSTLINK_UPDATE => POP_LOCATIONPOSTLINKSCREATION_ROUTE_EDITLOCATIONPOSTLINK,
            default => parent::getRelevantRoute($component, $props),
        };
    }

    protected function getInnerSubcomponents(array $component): array
    {
        $ret = parent::getInnerSubcomponents($component);

        $block_inners = array(
            self::COMPONENT_BLOCK_LOCATIONPOSTLINK_UPDATE => [PoP_LocationPostLinksCreation_Module_Processor_CreateUpdatePostDataloads::class, PoP_LocationPostLinksCreation_Module_Processor_CreateUpdatePostDataloads::COMPONENT_DATALOAD_LOCATIONPOSTLINK_UPDATE],
            self::COMPONENT_BLOCK_LOCATIONPOSTLINK_CREATE => [PoP_LocationPostLinksCreation_Module_Processor_CreateUpdatePostDataloads::class, PoP_LocationPostLinksCreation_Module_Processor_CreateUpdatePostDataloads::COMPONENT_DATALOAD_LOCATIONPOSTLINK_CREATE],
        );
        if ($block_inner = $block_inners[$component[1]] ?? null) {
            $ret[] = $block_inner;
        }

        return $ret;
    }

    protected function isCreate(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_BLOCK_LOCATIONPOSTLINK_CREATE:
                return true;
        }

        return parent::isCreate($component);
    }
    protected function isUpdate(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_BLOCK_LOCATIONPOSTLINK_UPDATE:
                return true;
        }

        return parent::isUpdate($component);
    }

    public function initModelProps(array $component, array &$props): void
    {
        switch ($component[1]) {
            case self::COMPONENT_BLOCK_LOCATIONPOSTLINK_UPDATE:
            case self::COMPONENT_BLOCK_LOCATIONPOSTLINK_CREATE:
                if (PoP_Application_Utils::getAddcontentTarget() == POP_TARGET_ADDONS) {
                    $this->appendProp($component, $props, 'class', 'addons-nocontrols');
                }
                break;
        }

        parent::initModelProps($component, $props);
    }
}


