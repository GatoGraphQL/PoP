<?php

declare(strict_types=1);

namespace PoPBackbone\GraphQLParser\Parser\Ast;

use PoPBackbone\GraphQLParser\Exception\Parser\InvalidRequestException;
use PoPBackbone\GraphQLParser\Parser\Ast\Fragment;
use PoPBackbone\GraphQLParser\Parser\Ast\OperationInterface;
use PoPBackbone\GraphQLParser\Parser\Location;

class Document
{
    public function __construct(
        /** @var OperationInterface[] */
        private array $operations,
        /** @var Fragment[] */
        private array $fragments = [],
    ) {
    }

    /**
     * @return OperationInterface[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function getFragment(string $name): ?Fragment
    {
        foreach ($this->fragments as $fragment) {
            if ($fragment->getName() === $name) {
                return $fragment;
            }
        }

        return null;
    }

    /**
     * @throws InvalidRequestException
     */
    public function validate(): void
    {
        $this->assertOperationsDefined();
        $this->assertOperationNamesUnique();
        $this->assertFragmentReferencesAreValid();
        $this->assertFragmentsAreUsed();
        $this->assertVariableNamesUnique();
        $this->assertAllVariablesExist();
        $this->assertAllVariablesAreUsed();
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertOperationsDefined(): void
    {
        if ($this->getOperations() === []) {
            throw new InvalidRequestException(
                $this->getNoOperationsDefinedInQueryErrorMessage(),
                $this->getNonSpecificLocation()
            );
        }
    }

    protected function getNoOperationsDefinedInQueryErrorMessage(): string
    {
        return 'No operations defined in the query';
    }

    protected function getNonSpecificLocation(): Location
    {
        return new Location(1, 1);
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertOperationNamesUnique(): void
    {
        $operationNames = [];
        foreach ($this->getOperations() as $operation) {
            $operationName = $operation->getName();
            if (in_array($operationName, $operationNames)) {
                throw new InvalidRequestException(
                    $this->getDuplicateOperationNameErrorMessage($operationName),
                    $this->getNonSpecificLocation()
                );
            }
            $operationNames[] = $operationName;
        }
    }

    protected function getDuplicateOperationNameErrorMessage(string $operationName): string
    {
        return \sprintf('Operation name \'%s\' is duplicated, it must be unique', $operationName);
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertFragmentReferencesAreValid(): void
    {
        foreach ($this->getOperations() as $operation) {
            foreach ($operation->getFragmentReferences() as $fragmentReference) {
                if ($this->getFragment($fragmentReference->getName()) !== null) {
                    continue;
                }
                throw new InvalidRequestException(
                    $this->getFragmentNotDefinedInQueryErrorMessage($fragmentReference->getName()),
                    $fragmentReference->getLocation()
                );
            }
        }
    }

    protected function getFragmentNotDefinedInQueryErrorMessage(string $fragmentName): string
    {
        return sprintf('Fragment \'%s\' not defined in query', $fragmentName);
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertFragmentsAreUsed(): void
    {
        $referencedFragmentNames = [];

        // Collect fragment references in all operations
        foreach ($this->getOperations() as $operation) {
            foreach ($operation->getFragmentReferences() as $fragmentReference) {
                $referencedFragmentNames[] = $fragmentReference->getName();
            }
        }

        // Collect fragment references in all fragments
        foreach ($this->getFragments() as $fragment) {
            foreach ($fragment->getFieldsOrFragmentBonds() as $fieldsOrFragmentBond) {
                if (!($fieldsOrFragmentBond instanceof FragmentReference)) {
                    continue;
                }
                /** @var FragmentReference */
                $fragmentReference = $fieldsOrFragmentBond;
                $referencedFragmentNames[] = $fragmentReference->getName();
            }
        }

        $referencedFragmentNames = array_values(array_unique($referencedFragmentNames));

        foreach ($this->getFragments() as $fragment) {
            if (in_array($fragment->getName(), $referencedFragmentNames)) {
                continue;
            }
            throw new InvalidRequestException(
                $this->getFragmentNotUsedErrorMessage($fragment->getName()),
                $fragment->getLocation()
            );
        }
    }

    protected function getFragmentNotUsedErrorMessage(string $fragmentName): string
    {
        return sprintf('Fragment \'%s\' not used', $fragmentName);
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertVariableNamesUnique(): void
    {
        foreach ($this->getOperations() as $operation) {
            $variableNames = [];
            foreach ($operation->getVariables() as $variable) {
                $variableName = $variable->getName();
                if (in_array($variableName, $variableNames)) {
                    throw new InvalidRequestException(
                        $this->getDuplicateVariableNameErrorMessage($variableName),
                        $this->getNonSpecificLocation()
                    );
                }
                $variableNames[] = $variableName;
            }
        }
    }

    protected function getDuplicateVariableNameErrorMessage(string $variableName): string
    {
        return \sprintf('Variable name \'%s\' is duplicated, it must be unique', $variableName);
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertAllVariablesExist(): void
    {
        foreach ($this->getOperations() as $operation) {
            foreach ($operation->getVariableReferences() as $variableReference) {
                if ($variableReference->getVariable() !== null) {
                    continue;
                }
                throw new InvalidRequestException(
                    $this->getVariableDoesNotExistErrorMessage($variableReference->getName()),
                    $variableReference->getLocation()
                );
            }
        }
    }

    protected function getVariableDoesNotExistErrorMessage(string $variableName): string
    {
        return sprintf('Variable \'%s\' has not been defined in the operation', $variableName);
    }

    /**
     * @throws InvalidRequestException
     */
    protected function assertAllVariablesAreUsed(): void
    {
        foreach ($this->getOperations() as $operation) {
            $referencedVariableNames = [];
            foreach ($operation->getVariableReferences() as $variableReference) {
                $referencedVariableNames[] = $variableReference->getName();
            }
            $referencedVariableNames = array_values(array_unique($referencedVariableNames));

            foreach ($operation->getVariables() as $variable) {
                if (in_array($variable->getName(), $referencedVariableNames)) {
                    continue;
                }
                throw new InvalidRequestException(
                    $this->getVariableNotUsedErrorMessage($variable->getName()),
                    $variable->getLocation()
                );
            }
        }
    }

    protected function getVariableNotUsedErrorMessage(string $variableName): string
    {
        return sprintf('Variable \'%s\' not used', $variableName);
    }
}
