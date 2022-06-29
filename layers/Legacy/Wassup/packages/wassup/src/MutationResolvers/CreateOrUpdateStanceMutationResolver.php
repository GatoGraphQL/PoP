<?php

declare(strict_types=1);

namespace PoPSitesWassup\Wassup\MutationResolvers;

use PoP\Application\Environment;
class CreateOrUpdateStanceMutationResolver extends \PoPSitesWassup\StanceMutations\MutationResolvers\CreateOrUpdateStanceMutationResolver
{
    protected function getCreatepostData(WithArgumentsInterface $withArgumentsAST)
    {
        $post_data = parent::getCreatepostData($withArgumentsAST);

        // Property 'menu-order' only works for WordPress
        if (Environment::disableCustomCMSCode()) {
            return $post_data;
        }

        // Allow to order the Author Thoughts Carousel, so that it always shows the General thought first, and the then article-related ones
        // For that, General thoughts have menu_order "0" (already default one), article-related ones have menu_order "1"
        if ($withArgumentsAST->getArgumentValue('stancetarget')) {
            $post_data['menu-order'] = 1;
        }

        return $post_data;
    }
}
