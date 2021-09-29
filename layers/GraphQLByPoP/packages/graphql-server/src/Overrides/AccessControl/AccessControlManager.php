<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Overrides\AccessControl;

use Symfony\Contracts\Service\Attribute\Required;
use GraphQLByPoP\GraphQLServer\IFTTT\MandatoryDirectivesForFieldsRootTypeEntryDuplicatorInterface;
use PoP\AccessControl\Services\AccessControlManager as UpstreamAccessControlManager;

class AccessControlManager extends UpstreamAccessControlManager
{
    /**
     * @var array<string, array>
     */
    protected array $overriddenFieldEntries = [];
    protected MandatoryDirectivesForFieldsRootTypeEntryDuplicatorInterface $mandatoryDirectivesForFieldsRootTypeEntryDuplicator;

    #[Required]
    public function autowireGraphQLServerAccessControlManager(MandatoryDirectivesForFieldsRootTypeEntryDuplicatorInterface $mandatoryDirectivesForFieldsRootTypeEntryDuplicator): void
    {
        $this->mandatoryDirectivesForFieldsRootTypeEntryDuplicator = $mandatoryDirectivesForFieldsRootTypeEntryDuplicator;
    }

    public function addEntriesForFields(string $group, array $fieldEntries): void
    {
        parent::addEntriesForFields($group, $fieldEntries);

        // Make sure to reset getting the entries
        unset($this->overriddenFieldEntries[$group]);
    }

    /**
     * Add additional entries: whenever Root is used,
     * duplicate it also for both QueryRoot and MutationRoot,
     * so that the user needs to set the configuration only once.
     *
     * Add this logic when retrieving the entries because by then
     * the container is compiled and we can access the RootObjectTypeResolver
     * instance. In contrast, `addEntriesForFields` can be called
     * within a CompilerPass, so the instances would not yet be available.
     */
    public function getEntriesForFields(string $group): array
    {
        if (isset($this->overriddenFieldEntries[$group])) {
            return $this->overriddenFieldEntries[$group];
        }

        $this->overriddenFieldEntries[$group] = $this->mandatoryDirectivesForFieldsRootTypeEntryDuplicator->maybeAppendAdditionalRootEntriesForFields(
            parent::getEntriesForFields($group)
        );

        return $this->overriddenFieldEntries[$group];
    }
}
