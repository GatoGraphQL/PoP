<?php

declare(strict_types=1);

namespace PoPCMSSchema\TaxonomyMutations\MutationResolvers;

use PoPCMSSchema\TaxonomyMutations\MutationResolvers\PayloadableDeleteTaxonomyTermMutationResolverTrait;

class PayloadableDeleteGenericTaxonomyTermMutationResolver extends AbstractMutateGenericTaxonomyTermMutationResolver
{
    use PayloadableDeleteTaxonomyTermMutationResolverTrait;
}