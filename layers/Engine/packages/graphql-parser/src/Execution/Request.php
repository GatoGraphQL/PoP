<?php

/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace PoP\GraphQLParser\Execution;

use PoP\GraphQLParser\Exception\Parser\InvalidRequestException;
use PoP\GraphQLParser\Parser\Ast\ArgumentValue\Variable;
use PoP\GraphQLParser\Parser\Ast\ArgumentValue\VariableReference;
use PoP\GraphQLParser\Parser\Ast\Fragment;
use PoP\GraphQLParser\Parser\Ast\FragmentReference;
use PoP\GraphQLParser\Parser\Ast\Mutation;
use PoP\GraphQLParser\Parser\Ast\Query;

class Request
{
    /** @var Query[] */
    private array $queries = [];

    /** @var Fragment[] */
    private array $fragments = [];

    /** @var Mutation[] */
    private array $mutations = [];

    /** @var array */
    private $variables = [];

    /** @var VariableReference[] */
    private array $variableReferences = [];

    private array $queryVariables = [];

    private array $fragmentReferences = [];

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $variables
     */
    public function __construct(array $data = [], array $variables = [])
    {
        if (array_key_exists('queries', $data)) {
            $this->addQueries($data['queries']);
        }

        if (array_key_exists('mutations', $data)) {
            $this->addMutations($data['mutations']);
        }

        if (array_key_exists('fragments', $data)) {
            $this->addFragments($data['fragments']);
        }

        if (array_key_exists('fragmentReferences', $data)) {
            $this->addFragmentReferences($data['fragmentReferences']);
        }

        if (array_key_exists('variables', $data)) {
            $this->addQueryVariables($data['variables']);
        }

        if (array_key_exists('variableReferences', $data)) {
            foreach ($data['variableReferences'] as $ref) {
                if (!array_key_exists($ref->getName(), $variables)) {
                    /** @var Variable $variable */
                    $variable = $ref->getVariable();
                    /**
                     * If $variable is null, then it was not declared in the operation arguments
                     * @see https://graphql.org/learn/queries/#variables
                     */
                    if (is_null($variable)) {
                        throw new InvalidRequestException(sprintf("Variable %s hasn't been declared", $ref->getName()), $ref->getLocation());
                    }
                    if ($variable->hasDefaultValue()) {
                        $variables[$variable->getName()] = $variable->getDefaultValue()->getValue();
                        continue;
                    }
                    throw new InvalidRequestException(sprintf("Variable %s hasn't been submitted", $ref->getName()), $ref->getLocation());
                }
            }

            $this->addVariableReferences($data['variableReferences']);
        }

        $this->setVariables($variables);
    }

    public function addQueries($queries): void
    {
        foreach ($queries as $query) {
            $this->queries[] = $query;
        }
    }

    public function addMutations($mutations): void
    {
        foreach ($mutations as $mutation) {
            $this->mutations[] = $mutation;
        }
    }

    public function addQueryVariables($queryVariables): void
    {
        foreach ($queryVariables as $queryVariable) {
            $this->queryVariables[] = $queryVariable;
        }
    }

    public function addVariableReferences($variableReferences): void
    {
        foreach ($variableReferences as $variableReference) {
            $this->variableReferences[] = $variableReference;
        }
    }

    public function addFragmentReferences($fragmentReferences): void
    {
        foreach ($fragmentReferences as $fragmentReference) {
            $this->fragmentReferences[] = $fragmentReference;
        }
    }

    public function addFragments($fragments): void
    {
        foreach ($fragments as $fragment) {
            $this->addFragment($fragment);
        }
    }

    /**
     * @return Query[]
     */
    public function getAllOperations()
    {
        return array_merge($this->mutations, $this->queries);
    }

    /**
     * @return Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments()
    {
        return $this->fragments;
    }

    public function addFragment(Fragment $fragment): void
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @param $name
     *
     * @return null|Fragment
     */
    public function getFragment($name)
    {
        foreach ($this->fragments as $fragment) {
            if ($fragment->getName() == $name) {
                return $fragment;
            }
        }

        return null;
    }

    /**
     * @return Mutation[]
     */
    public function getMutations()
    {
        return $this->mutations;
    }

    /**
     * @return bool
     */
    public function hasQueries()
    {
        return (bool)count($this->queries);
    }

    /**
     * @return bool
     */
    public function hasMutations()
    {
        return (bool)count($this->mutations);
    }

    /**
     * @return bool
     */
    public function hasFragments()
    {
        return (bool)count($this->fragments);
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @return $this
     */
    public function setVariables(array|string $variables)
    {
        if (!is_array($variables)) {
            $variables = json_decode($variables, true);
        }

        $this->variables = $variables;
        foreach ($this->variableReferences as $reference) {
            /** invalid request with no variable */
            if (!$reference->getVariable()) {
                continue;
            }
            $variableName = $reference->getVariable()->getName();

            /** no variable was set at the time */
            if (!array_key_exists($variableName, $variables)) {
                continue;
            }

            $reference->getVariable()->setValue($variables[$variableName]);
            $reference->setValue($variables[$variableName]);
        }

        return $this;
    }

    public function getVariable($name)
    {
        return $this->hasVariable($name) ? $this->variables[$name] : null;
    }

    public function hasVariable($name)
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @return array|Variable[]
     */
    public function getQueryVariables()
    {
        return $this->queryVariables;
    }

    /**
     * @param array $queryVariables
     */
    public function setQueryVariables($queryVariables): void
    {
        $this->queryVariables = $queryVariables;
    }

    /**
     * @return array|FragmentReference[]
     */
    public function getFragmentReferences()
    {
        return $this->fragmentReferences;
    }

    /**
     * @param array $fragmentReferences
     */
    public function setFragmentReferences($fragmentReferences): void
    {
        $this->fragmentReferences = $fragmentReferences;
    }

    /**
     * @return array|VariableReference[]
     */
    public function getVariableReferences()
    {
        return $this->variableReferences;
    }
}
