<?php

declare(strict_types=1);

namespace PoPCMSSchema\MediaMutations\SchemaHooks;

use PoPCMSSchema\MediaMutations\ComponentProcessors\MediaFilterInputContainerComponentProcessor;
use PoPCMSSchema\CustomPostMutations\ConditionalOnModule\Users\SchemaHooks\AbstractRemoveAuthorFilterInputHookSet;

class RemoveAuthorFilterInputHookSet extends AbstractRemoveAuthorFilterInputHookSet
{
    protected function getHookNameToRemoveFilterInput(): string
    {
        return MediaFilterInputContainerComponentProcessor::HOOK_FILTER_INPUTS;
    }
}