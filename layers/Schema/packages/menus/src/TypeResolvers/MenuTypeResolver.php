<?php

declare(strict_types=1);

namespace PoPSchema\Menus\TypeResolvers;

use PoP\ComponentModel\TypeResolvers\AbstractRelationalTypeResolver;
use PoPSchema\Menus\Facades\MenuTypeAPIFacade;
use PoPSchema\Menus\TypeDataLoaders\MenuTypeDataLoader;

class MenuTypeResolver extends AbstractRelationalTypeResolver
{
    public function getTypeName(): string
    {
        return 'Menu';
    }

    public function getSchemaTypeDescription(): ?string
    {
        return $this->translationAPI->__('Representation of a navigation menu', 'menus');
    }

    public function getID(object $resultItem): string | int | null
    {
        $menuTypeAPI = MenuTypeAPIFacade::getInstance();
        $menu = $resultItem;
        return $menuTypeAPI->getMenuID($menu);
    }

    public function getTypeDataLoaderClass(): string
    {
        return MenuTypeDataLoader::class;
    }
}
