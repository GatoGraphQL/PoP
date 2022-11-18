<?php

declare(strict_types=1);

namespace PoPSchema\SchemaCommons\ObjectModels;

use PoPSchema\SchemaCommons\ObjectModels\ErrorPayload;
use PoP\ComponentModel\ObjectModels\AbstractTransientObject;
use PoP\Root\Services\StandaloneServiceTrait;
use RuntimeException;

abstract class AbstractTransientEntityPayload extends AbstractTransientObject
{
    use StandaloneServiceTrait;

    /**
     * Either the object ID or the error must be provided.
     * If both of them are null, it's a development error.
     */
    public function __construct(
        public readonly string $status,
        public readonly string|int|null $objectID,
        public readonly ?ErrorPayload $error,
    ) {
        if ($objectID === null && $error === null) {
            throw new RuntimeException(
                $this->__('Either the object ID or the error must be provided', 'schema-commons')
            );
        }
        parent::__construct();
    }
}
