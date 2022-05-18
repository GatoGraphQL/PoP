<?php

class PoP_Module_Processor_MapStaticImageLocations extends PoP_Module_Processor_MapStaticImageLocationsBase
{
    public final const MODULE_MAP_STATICIMAGE_LOCATIONS = 'em-map-staticimage-locations';

    public function getComponentsToProcess(): array
    {
        return array(
            [self::class, self::COMPONENT_MAP_STATICIMAGE_LOCATIONS],
        );
    }
}



