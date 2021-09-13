<?php

declare(strict_types=1);

namespace PoP\MandatoryDirectivesByConfiguration\RelationalTypeResolverDecorators;

use PoP\ComponentModel\RelationalTypeResolverDecorators\AbstractTypeResolverDecorator;
use PoP\MandatoryDirectivesByConfiguration\RelationalTypeResolverDecorators\ConfigurableMandatoryDirectivesForFieldsTypeResolverDecoratorTrait;

abstract class AbstractMandatoryDirectivesForFieldsTypeResolverDecorator extends AbstractTypeResolverDecorator
{
    use ConfigurableMandatoryDirectivesForFieldsTypeResolverDecoratorTrait;
}
