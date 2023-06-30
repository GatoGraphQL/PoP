<?php

declare(strict_types=1);

namespace GatoGraphQL\GatoGraphQL\Services\Blocks;

use GatoGraphQL\GatoGraphQL\Constants\ConfigurationDefaultValues;
use GatoGraphQL\GatoGraphQL\ModuleResolvers\SchemaTypeModuleResolver;
use GatoGraphQL\GatoGraphQL\WPDataModel\WPDataModelProviderInterface;

class SchemaConfigSchemaTagsBlock extends AbstractSchemaConfigCustomizableConfigurationBlock
{
    use MainPluginBlockTrait;
    use OptionsBlockTrait;

    public final const ATTRIBUTE_NAME_INCLUDED_TAG_TAXONOMIES = 'includedTagTaxonomies';

    private ?WPDataModelProviderInterface $wpDataModelProvider = null;

    final public function setWPDataModelProvider(WPDataModelProviderInterface $wpDataModelProvider): void
    {
        $this->wpDataModelProvider = $wpDataModelProvider;
    }
    final protected function getWPDataModelProvider(): WPDataModelProviderInterface
    {
        if ($this->wpDataModelProvider === null) {
            /** @var WPDataModelProviderInterface */
            $wpDataModelProvider = $this->instanceManager->getInstance(WPDataModelProviderInterface::class);
            $this->wpDataModelProvider = $wpDataModelProvider;
        }
        return $this->wpDataModelProvider;
    }

    protected function getBlockName(): string
    {
        return 'schema-config-schema-tags';
    }

    public function getBlockPriority(): int
    {
        return 9080;
    }

    public function getEnablingModule(): ?string
    {
        return SchemaTypeModuleResolver::SCHEMA_TAGS;
    }

    /**
     * Pass localized data to the block
     *
     * @return array<string,mixed>
     */
    protected function getLocalizedData(): array
    {
        return array_merge(
            parent::getLocalizedData(),
            [
                'possibleTagTaxonomies' => $this->getWPDataModelProvider()->getFilteredNonGatoGraphQLPluginTagTaxonomies(),
                'defaultTagTaxonomies' => ConfigurationDefaultValues::DEFAULT_TAG_TAXONOMIES,
            ]
        );
    }

    /**
     * @param array<string,mixed> $attributes
     */
    protected function doRenderBlock(array $attributes, string $content): string
    {
        $values = $attributes[self::ATTRIBUTE_NAME_INCLUDED_TAG_TAXONOMIES] ?? [];
        return sprintf(
            '<p><strong>%s</strong></p>%s',
            $this->__('Included tag taxonomies', 'gato-graphql'),
            $values ?
                sprintf(
                    '<ul><li><code>%s</code></li></ul>',
                    implode('</code></li><li><code>', $values)
                ) :
                sprintf(
                    '<p><em>%s</em></p>',
                    \__('(not set)', 'gato-graphql')
                )
        );
    }

    protected function getBlockTitle(): string
    {
        return \__('Tags', 'gato-graphql');
    }

    /**
     * Register style-index.css
     */
    protected function registerCommonStyleCSS(): bool
    {
        return true;
    }

    /**
     * Add the locale language to the localized data?
     */
    protected function addLocalLanguage(): bool
    {
        return true;
    }

    /**
     * Default language for the script/component's documentation
     */
    protected function getDefaultLanguage(): ?string
    {
        // English
        return 'en';
    }
}
