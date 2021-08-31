<?php

declare(strict_types=1);

namespace PoPSchema\Posts\TypeDataLoaders;

use PoPSchema\CustomPosts\TypeDataLoaders\AbstractCustomPostTypeDataLoader;
use PoPSchema\Posts\Facades\PostTypeAPIFacade;

class PostTypeDataLoader extends AbstractCustomPostTypeDataLoader
{
    public function getObjects(array $ids): array
    {
        $postTypeAPI = PostTypeAPIFacade::getInstance();
        $query = $this->getObjectQuery($ids);
        return $postTypeAPI->getPosts($query);
    }

    public function executeQuery($query, array $options = []): array
    {
        $postTypeAPI = PostTypeAPIFacade::getInstance();
        return $postTypeAPI->getPosts($query, $options);
    }
}
