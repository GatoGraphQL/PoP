<?php

/**
 * Date: 16.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace PoP\GraphQLParser\Exception\Parser;

use Exception;
use PoP\GraphQLParser\Exception\Interfaces\LocationableExceptionInterface;
use PoP\GraphQLParser\Parser\Location;

abstract class AbstractParserError extends Exception implements LocationableExceptionInterface
{
    public function __construct(string $message, private Location $location)
    {
        parent::__construct($message);
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
