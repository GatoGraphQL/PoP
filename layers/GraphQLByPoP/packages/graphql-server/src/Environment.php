<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer;

class Environment
{
    public const EXPOSE_SELF_FIELD_FOR_ROOT_TYPE_IN_GRAPHQL_SCHEMA = 'EXPOSE_SELF_FIELD_FOR_ROOT_TYPE_IN_GRAPHQL_SCHEMA';
    public const SORT_GRAPHQL_SCHEMA_ALPHABETICALLY = 'SORT_GRAPHQL_SCHEMA_ALPHABETICALLY';
    public const ENABLE_PROACTIVE_FEEDBACK = 'ENABLE_PROACTIVE_FEEDBACK';
    public const ENABLE_PROACTIVE_FEEDBACK_DEPRECATIONS = 'ENABLE_PROACTIVE_FEEDBACK_DEPRECATIONS';
    public const ENABLE_PROACTIVE_FEEDBACK_NOTICES = 'ENABLE_PROACTIVE_FEEDBACK_NOTICES';
    public const ENABLE_PROACTIVE_FEEDBACK_TRACES = 'ENABLE_PROACTIVE_FEEDBACK_TRACES';
    public const ENABLE_PROACTIVE_FEEDBACK_LOGS = 'ENABLE_PROACTIVE_FEEDBACK_LOGS';
    public const ENABLE_NESTED_MUTATIONS = 'ENABLE_NESTED_MUTATIONS';
    public const ENABLE_GRAPHQL_INTROSPECTION = 'ENABLE_GRAPHQL_INTROSPECTION';
    public const EXPOSE_SELF_FIELD_IN_GRAPHQL_SCHEMA = 'EXPOSE_SELF_FIELD_IN_GRAPHQL_SCHEMA';
    public const ADD_FULLSCHEMA_FIELD_TO_SCHEMA = 'ADD_FULLSCHEMA_FIELD_TO_SCHEMA';
    public const ADD_VERSION_TO_SCHEMA_FIELD_DESCRIPTION = 'ADD_VERSION_TO_SCHEMA_FIELD_DESCRIPTION';
    public const ENABLE_SETTING_MUTATION_SCHEME_BY_URL_PARAM = 'ENABLE_SETTING_MUTATION_SCHEME_BY_URL_PARAM';
    public const ENABLE_ENABLING_GRAPHQL_INTROSPECTION_BY_URL_PARAM = 'ENABLE_ENABLING_GRAPHQL_INTROSPECTION_BY_URL_PARAM';
    public const ADD_GRAPHQL_INTROSPECTION_PERSISTED_QUERY = 'ADD_GRAPHQL_INTROSPECTION_PERSISTED_QUERY';
    public const ADD_CONNECTION_FROM_ROOT_TO_QUERYROOT_AND_MUTATIONROOT = 'ADD_CONNECTION_FROM_ROOT_TO_QUERYROOT_AND_MUTATIONROOT';
    public const EXPOSE_SCHEMA_INTROSPECTION_FIELD_IN_SCHEMA = 'EXPOSE_SCHEMA_INTROSPECTION_FIELD_IN_SCHEMA';
    public const EXPOSE_GLOBAL_FIELDS_IN_GRAPHQL_SCHEMA = 'EXPOSE_GLOBAL_FIELDS_IN_GRAPHQL_SCHEMA';
}
