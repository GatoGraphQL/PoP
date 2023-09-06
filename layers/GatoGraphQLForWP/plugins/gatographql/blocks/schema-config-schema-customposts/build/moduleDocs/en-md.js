(window.webpackJsonpGatographqlSchemaConfigSchemaCustomposts=window.webpackJsonpGatographqlSchemaConfigSchemaCustomposts||[]).push([[1],{50:function(s,t){s.exports='<h1 id="custom-posts">Custom Posts</h1> <p>Query Custom Post Types</p> <h2 id="description">Description</h2> <p>This module provides the basic schema functionality for custom posts, so it must also be enabled whenever any custom post entity (including posts, pages, or any Custom Post Type) is to be added to the schema.</p> <p>It also provides type <code>CustomPostUnion</code>, which is used whenever an entity can return custom posts.</p> <p><img src="https://raw.githubusercontent.com/GatoGraphQL/GatoGraphQL/1.0.4/layers/GatoGraphQLForWP/plugins/gatographql/docs/modules/schema-customposts/../../images/interactive-schema-custompost-union.png" alt="`CustomPostUnion` type" title="`CustomPostUnion` type"></p> <p>For instance, a comment can be added to a post, but also to a page and to a CPT, hence type <code>Comment</code> has field <code>customPost: CustomPostUnion!</code> (instead of field <code>post: Post!</code>) to retrieve the entity where the comment was added.</p> <p><img src="https://raw.githubusercontent.com/GatoGraphQL/GatoGraphQL/1.0.4/layers/GatoGraphQLForWP/plugins/gatographql/docs/modules/schema-customposts/../../images/interactive-schema-comment.png" alt="`Comment` type" title="`Comment` type"></p> <p>Because all Custom Posts implement interface <code>CustomPost</code>, we can retrieve data from <code>CustomPostUnion</code> using a fragment reference or an inline fragment:</p> <pre><code class="hljs language-graphql"><span class="hljs"><span class="hljs-punctuation">{</span>\n  comments <span class="hljs-punctuation">{</span>\n    id\n    date\n    content\n    customPost <span class="hljs-punctuation">{</span>\n      __typename\n      <span class="hljs-punctuation">...</span><span class="hljs-keyword">on</span> CustomPost <span class="hljs-punctuation">{</span>\n        id\n        title\n        url\n      <span class="hljs-punctuation">}</span>\n    <span class="hljs-punctuation">}</span>\n  <span class="hljs-punctuation">}</span>\n<span class="hljs-punctuation">}</span></span></code></pre> <p>If we know that the comment was added to a post, we can also query fields specific to the <code>Post</code>:</p> <pre><code class="hljs language-graphql"><span class="hljs"><span class="hljs-punctuation">{</span>\n  comments <span class="hljs-punctuation">{</span>\n    id\n    date\n    content\n    customPost <span class="hljs-punctuation">{</span>\n      __typename\n      <span class="hljs-punctuation">...</span><span class="hljs-keyword">on</span> CustomPost <span class="hljs-punctuation">{</span>\n        id\n        title\n        url\n      <span class="hljs-punctuation">}</span>\n      <span class="hljs-punctuation">...</span><span class="hljs-keyword">on</span> Post <span class="hljs-punctuation">{</span>\n        categoryNames\n      <span class="hljs-punctuation">}</span>\n    <span class="hljs-punctuation">}</span>\n  <span class="hljs-punctuation">}</span>\n<span class="hljs-punctuation">}</span></span></code></pre> <h2 id="mapped-and-unmapped-cpts">Mapped and Unmapped CPTs</h2> <p>There are CPTs (such as <code>&quot;post&quot;</code> and <code>&quot;page&quot;</code>) which already have a corresponding GraphQL type in the schema (<code>Post</code> and <code>Page</code>), and these types are incorporated directly into <code>CustomPostUnion</code>.</p> <p>For any CPT that has not been modeled in the schema (such as <code>&quot;attachment&quot;</code>, <code>&quot;revision&quot;</code> or <code>&quot;nav_menu_item&quot;</code>, or any CPT installed by any plugin), their data will be accessed via the <code>GenericCustomPost</code> type.</p> <p>For instance, this query retrieves entries from multiple CPTS:</p> <pre><code class="hljs language-graphql"><span class="hljs"><span class="hljs-punctuation">{</span>\n  customPosts<span class="hljs-punctuation">(</span>\n    <span class="hljs-symbol">filter</span><span class="hljs-punctuation">:</span> <span class="hljs-punctuation">{</span>\n      <span class="hljs-symbol">customPostTypes</span><span class="hljs-punctuation">:</span> <span class="hljs-punctuation">[</span>\n        <span class="hljs-string">&quot;post&quot;</span>,\n        <span class="hljs-string">&quot;page&quot;</span>,\n        <span class="hljs-string">&quot;attachment&quot;</span>,\n        <span class="hljs-string">&quot;nav_menu_item&quot;</span>,\n        <span class="hljs-string">&quot;custom_css&quot;</span>,\n        <span class="hljs-string">&quot;revision&quot;</span>\n      <span class="hljs-punctuation">]</span>,\n      <span class="hljs-symbol">status</span><span class="hljs-punctuation">:</span> <span class="hljs-punctuation">[</span>\n        publish,\n        inherit\n      <span class="hljs-punctuation">]</span>\n    <span class="hljs-punctuation">}</span>\n  <span class="hljs-punctuation">)</span> <span class="hljs-punctuation">{</span>\n    id\n    title\n    content\n    status\n    customPostType\n    __typename\n  <span class="hljs-punctuation">}</span>\n<span class="hljs-punctuation">}</span></span></code></pre> <h2 id="defining-the-allowed-custom-post-types">Defining the allowed Custom Post Types</h2> <p>The custom post types that can be queried must be explicitly configured. This can be done in 2 places.</p> <p>In the Schema Configuration applied to the endpoint, by selecting option <code>&quot;Use custom configuration&quot;</code> under &quot;Customize configuration? (Or use default from Settings?)&quot; and then selecting the desired items:</p> <p><img src="https://raw.githubusercontent.com/GatoGraphQL/GatoGraphQL/1.0.4/layers/GatoGraphQLForWP/plugins/gatographql/docs/modules/schema-customposts/../../images/customposts-schema-configuration-queryable-cpts.png" alt="Selecting the allowed Custom Post Types in the Schema Configuration" title="Selecting the allowed Custom Post Types in the Schema Configuration"></p> <p>Otherwise, the value defined under section &quot;Included custom post types&quot; in the Settings page for <code>Schema Custom Posts</code> is used:</p> <div class="img-width-1024" markdown="1"> <p><img src="https://raw.githubusercontent.com/GatoGraphQL/GatoGraphQL/1.0.4/layers/GatoGraphQLForWP/plugins/gatographql/docs/modules/schema-customposts/../../images/customposts-settings-queryable-cpts.png" alt="Selecting the allowed Custom Post Types in the Settings" title="Selecting the allowed Custom Post Types in the Settings"></p> </div> <h2 id="additional-configuration">Additional configuration</h2> <p>Through the Settings page, we can also define:</p> <ul> <li>The default number of elements to retrieve (i.e. when field argument <code>limit</code> is not set) when querying for a list of any custom post type</li> <li>The maximum number of elements that can be retrieved in a single query execution</li> </ul> <div class="img-width-1024" markdown="1"> <p><img src="https://raw.githubusercontent.com/GatoGraphQL/GatoGraphQL/1.0.4/layers/GatoGraphQLForWP/plugins/gatographql/docs/modules/schema-customposts/../../images/settings-customposts-limits.png" alt="Settings for Custom Post limits" title="Settings for Custom Post limits"></p> </div> <p>If there is only one type added to <code>CustomPostUnion</code> (eg: only <code>Post</code>), we can then have the fields that resolve to <code>CustomPostUnion</code> be instead resolved to that unique type instead:</p> <div class="img-width-1024" markdown="1"> <p><img src="https://raw.githubusercontent.com/GatoGraphQL/GatoGraphQL/1.0.4/layers/GatoGraphQLForWP/plugins/gatographql/docs/modules/schema-customposts/../../images/settings-customposts-single-type.png" alt="Settings for Custom Posts" title="Settings for Custom Post"></p> </div> '}}]);