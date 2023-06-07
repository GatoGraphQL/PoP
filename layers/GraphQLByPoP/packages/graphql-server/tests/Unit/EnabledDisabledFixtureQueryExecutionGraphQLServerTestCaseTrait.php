<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Unit;

trait EnabledDisabledFixtureQueryExecutionGraphQLServerTestCaseTrait
{
    abstract protected static function isEnabled(): bool;

    protected static function getGraphQLResponseFile(string $filePath, string $fileName): string
    {
        $state = $this->getFileState();
        return $filePath . \DIRECTORY_SEPARATOR . $fileName . '@' . $state . '.json';
    }

    // protected static function getGraphQLVariablesFile(string $filePath, string $fileName): string
    // {
    //     $state = $this->getFileState();
    //     return $filePath . \DIRECTORY_SEPARATOR . $fileName . '@' . $state . '.var.json';
    // }

    protected function getFileState(): string
    {
        return $this->isEnabled() ? 'enabled' : 'disabled';
    }
}
