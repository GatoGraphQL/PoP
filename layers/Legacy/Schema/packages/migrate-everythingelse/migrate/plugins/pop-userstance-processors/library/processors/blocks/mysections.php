<?php

class UserStance_Module_Processor_MySectionBlocks extends PoP_Module_Processor_MySectionBlocksBase
{
    public final const COMPONENT_BLOCK_MYSTANCES_TABLE_EDIT = 'block-mystances-table-edit';
    public final const COMPONENT_BLOCK_MYSTANCES_SCROLL_FULLVIEWPREVIEW = 'block-mystances-scroll-fullviewpreview';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_BLOCK_MYSTANCES_TABLE_EDIT],
            [self::class, self::COMPONENT_BLOCK_MYSTANCES_SCROLL_FULLVIEWPREVIEW],
        );
    }

    public function getRelevantRoute(array $component, array &$props): ?string
    {
        return match($component[1]) {
            self::COMPONENT_BLOCK_MYSTANCES_SCROLL_FULLVIEWPREVIEW => POP_USERSTANCE_ROUTE_MYSTANCES,
            self::COMPONENT_BLOCK_MYSTANCES_TABLE_EDIT => POP_USERSTANCE_ROUTE_MYSTANCES,
            default => parent::getRelevantRoute($component, $props),
        };
    }

    protected function getInnerSubcomponent(array $component)
    {
        $inner_components = array(

            self::COMPONENT_BLOCK_MYSTANCES_TABLE_EDIT => [UserStance_Module_Processor_MySectionDataloads::class, UserStance_Module_Processor_MySectionDataloads::COMPONENT_DATALOAD_MYSTANCES_TABLE_EDIT],
            self::COMPONENT_BLOCK_MYSTANCES_SCROLL_FULLVIEWPREVIEW => [UserStance_Module_Processor_MySectionDataloads::class, UserStance_Module_Processor_MySectionDataloads::COMPONENT_DATALOAD_MYSTANCES_SCROLL_FULLVIEWPREVIEW],
        );

        return $inner_components[$component[1]] ?? null;
    }

    protected function getControlgroupTopSubcomponent(array $component)
    {
        switch ($component[1]) {
            case self::COMPONENT_BLOCK_MYSTANCES_TABLE_EDIT:
            case self::COMPONENT_BLOCK_MYSTANCES_SCROLL_FULLVIEWPREVIEW:
                return [UserStance_Module_Processor_CustomControlGroups::class, UserStance_Module_Processor_CustomControlGroups::COMPONENT_CONTROLGROUP_MYSTANCELIST];
        }

        return parent::getControlgroupTopSubcomponent($component);
    }
}



