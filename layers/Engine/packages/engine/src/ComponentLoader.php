<?php

declare(strict_types=1);

namespace PoP\Engine;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Root\Managers\ComponentManager;
use PoP\Root\ComponentLoader as RootComponentLoader;

class ComponentLoader extends RootComponentLoader
{
    public static function bootComponents()
    {
        // Boot all the components
        ComponentManager::beforeBoot();

        $hooksAPI = HooksAPIFacade::getInstance();
        $hooksAPI->addAction(
            'popcms:boot',
            function () {
                // Boot all the components
                ComponentManager::boot();
            },
            5
        );

        $hooksAPI->addAction(
            'popcms:boot',
            function () {
                // Boot all the components
                ComponentManager::afterBoot();
            },
            15
        );
    }
}
