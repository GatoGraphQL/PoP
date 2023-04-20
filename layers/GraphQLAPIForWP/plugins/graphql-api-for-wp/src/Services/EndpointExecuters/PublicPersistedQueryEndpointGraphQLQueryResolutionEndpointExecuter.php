<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI\Services\EndpointExecuters;

use GraphQLAPI\GraphQLAPI\ModuleResolvers\EndpointFunctionalityModuleResolver;
use GraphQLAPI\GraphQLAPI\Services\Blocks\PersistedQueryEndpointOptionsBlock;
use GraphQLAPI\GraphQLAPI\Services\CustomPostTypes\GraphQLEndpointCustomPostTypeInterface;
use GraphQLAPI\GraphQLAPI\Services\CustomPostTypes\GraphQLPublicPersistedQueryEndpointCustomPostType;
use GraphQLAPI\GraphQLAPI\Services\Helpers\GraphQLQueryPostTypeHelpers;
use WP_Post;

class PublicPersistedQueryEndpointGraphQLQueryResolutionEndpointExecuter extends AbstractGraphQLQueryResolutionEndpointExecuter
{
    private ?GraphQLPublicPersistedQueryEndpointCustomPostType $graphQLPublicPersistedQueryEndpointCustomPostType = null;
    private ?GraphQLQueryPostTypeHelpers $graphQLQueryPostTypeHelpers = null;

    final public function setGraphQLPublicPersistedQueryEndpointCustomPostType(GraphQLPublicPersistedQueryEndpointCustomPostType $graphQLPublicPersistedQueryEndpointCustomPostType): void
    {
        $this->graphQLPublicPersistedQueryEndpointCustomPostType = $graphQLPublicPersistedQueryEndpointCustomPostType;
    }
    final protected function getGraphQLPublicPersistedQueryEndpointCustomPostType(): GraphQLPublicPersistedQueryEndpointCustomPostType
    {
        /** @var GraphQLPublicPersistedQueryEndpointCustomPostType */
        return $this->graphQLPublicPersistedQueryEndpointCustomPostType ??= $this->instanceManager->getInstance(GraphQLPublicPersistedQueryEndpointCustomPostType::class);
    }
    final public function setGraphQLQueryPostTypeHelpers(GraphQLQueryPostTypeHelpers $graphQLQueryPostTypeHelpers): void
    {
        $this->graphQLQueryPostTypeHelpers = $graphQLQueryPostTypeHelpers;
    }
    final protected function getGraphQLQueryPostTypeHelpers(): GraphQLQueryPostTypeHelpers
    {
        /** @var GraphQLQueryPostTypeHelpers */
        return $this->graphQLQueryPostTypeHelpers ??= $this->instanceManager->getInstance(GraphQLQueryPostTypeHelpers::class);
    }

    public function getEnablingModule(): ?string
    {
        return EndpointFunctionalityModuleResolver::PUBLIC_PERSISTED_QUERIES;
    }

    protected function getCustomPostType(): GraphQLEndpointCustomPostTypeInterface
    {
        return $this->getGraphQLPublicPersistedQueryEndpointCustomPostType();
    }

    /**
     * Provide the query to execute and its variables
     *
     * @return array{0:?string,1:?array<string,mixed>} Array of 2 elements: [query, variables]
     */
    public function getGraphQLQueryAndVariables(?WP_Post $graphQLQueryPost): array
    {
        /**
         * Extract the query from the post (or from its parents), and set it in the application state
         */
        return $this->getGraphQLQueryPostTypeHelpers()->getGraphQLQueryPostAttributes($graphQLQueryPost, true);
    }

    /**
     * Indicate if the GraphQL variables must override the URL params
     */
    public function doURLParamsOverrideGraphQLVariables(?WP_Post $customPost): bool
    {
        if ($customPost === null) {
            return parent::doURLParamsOverrideGraphQLVariables($customPost);
        }
        $default = true;
        $optionsBlockDataItem = $this->getCustomPostType()->getOptionsBlockDataItem($customPost);
        if ($optionsBlockDataItem === null) {
            return $default;
        }

        // `true` is the default option in Gutenberg, so it's not saved to the DB!
        return $optionsBlockDataItem['attrs'][PersistedQueryEndpointOptionsBlock::ATTRIBUTE_NAME_ACCEPT_VARIABLES_AS_URL_PARAMS] ?? $default;
    }
}
