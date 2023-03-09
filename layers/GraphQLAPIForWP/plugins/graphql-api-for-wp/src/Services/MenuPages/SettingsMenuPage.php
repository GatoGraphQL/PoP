<?php

declare(strict_types=1);

namespace GraphQLAPI\GraphQLAPI\Services\MenuPages;

use GraphQLAPI\GraphQLAPI\Constants\RequestParams;
use GraphQLAPI\GraphQLAPI\Constants\SettingsCategories;
use GraphQLAPI\GraphQLAPI\Facades\UserSettingsManagerFacade;
use GraphQLAPI\GraphQLAPI\ModuleResolvers\PluginGeneralSettingsFunctionalityModuleResolver;
use GraphQLAPI\GraphQLAPI\ModuleSettings\Properties;
use GraphQLAPI\GraphQLAPI\Settings\Options;
use GraphQLAPI\GraphQLAPI\Settings\SettingsNormalizerInterface;
use GraphQLAPI\GraphQLAPI\Settings\UserSettingsManagerInterface;
use PoP\Root\App;

/**
 * Settings menu page
 */
class SettingsMenuPage extends AbstractPluginMenuPage
{
    use UseTabpanelMenuPageTrait;
    use UseDocsMenuPageTrait;

    public final const FORM_ORIGIN = 'form-origin';
    public final const SETTINGS_FIELD = 'graphql-api-settings';
    public final const PLUGIN_SETTINGS_FIELD = 'graphql-api-plugin-settings';
    public final const PLUGIN_MANAGEMENT_FIELD = 'graphql-api-plugin-management';
    public final const RESET_SETTINGS_BUTTON_ID = 'submit-reset-settings';

    private ?UserSettingsManagerInterface $userSettingsManager = null;
    private ?SettingsNormalizerInterface $settingsNormalizer = null;
    private ?PluginGeneralSettingsFunctionalityModuleResolver $PluginGeneralSettingsFunctionalityModuleResolver = null;

    public function setUserSettingsManager(UserSettingsManagerInterface $userSettingsManager): void
    {
        $this->userSettingsManager = $userSettingsManager;
    }
    protected function getUserSettingsManager(): UserSettingsManagerInterface
    {
        return $this->userSettingsManager ??= UserSettingsManagerFacade::getInstance();
    }
    final public function setSettingsNormalizer(SettingsNormalizerInterface $settingsNormalizer): void
    {
        $this->settingsNormalizer = $settingsNormalizer;
    }
    final protected function getSettingsNormalizer(): SettingsNormalizerInterface
    {
        /** @var SettingsNormalizerInterface */
        return $this->settingsNormalizer ??= $this->instanceManager->getInstance(SettingsNormalizerInterface::class);
    }
    final public function setPluginGeneralSettingsFunctionalityModuleResolver(PluginGeneralSettingsFunctionalityModuleResolver $PluginGeneralSettingsFunctionalityModuleResolver): void
    {
        $this->PluginGeneralSettingsFunctionalityModuleResolver = $PluginGeneralSettingsFunctionalityModuleResolver;
    }
    final protected function getPluginGeneralSettingsFunctionalityModuleResolver(): PluginGeneralSettingsFunctionalityModuleResolver
    {
        /** @var PluginGeneralSettingsFunctionalityModuleResolver */
        return $this->PluginGeneralSettingsFunctionalityModuleResolver ??= $this->instanceManager->getInstance(PluginGeneralSettingsFunctionalityModuleResolver::class);
    }

    public function getMenuPageSlug(): string
    {
        return 'settings';
    }

    /**
     * Initialize the class instance
     */
    public function initialize(): void
    {
        parent::initialize();

        /**
         * Before saving the settings in the DB,
         * decide what it is that must be stored.
         * There are 2 options.
         *
         * 1. If button "submit-reset-settings" is sent,
         * then the "Reset Settings" button has been pressed.
         * Then remove all settings values, except for the
         * safe/unsafe default value
         *
         * 2. Otherwise, it's the normal Settings.
         */
        $option = self::SETTINGS_FIELD;
        \add_filter(
            "pre_update_option_{$option}",
            /**
             * @param array<string,mixed> $values
             * @return array<string,mixed>
             */
            function (array $values): array {
                /**
                 * 1st case: check that pressed on the "Reset Settings" button,
                 * and an actual "safe" or "unsafe" value was selected.
                 */
                if (isset($values[self::RESET_SETTINGS_BUTTON_ID])) {
                    /**
                     * Remove all settings, except the one indicating if to use
                     * the "safe" or "unsafe" default behavior
                     */
                    $resetSettingsOptionName = $this->getPluginGeneralSettingsFunctionalityModuleResolver()->getSettingOptionName(
                        PluginGeneralSettingsFunctionalityModuleResolver::GENERAL,
                        PluginGeneralSettingsFunctionalityModuleResolver::OPTION_USE_SAFE_OR_UNSAFE_DEFAULT_BEHAVIOR
                    );
                    $values = array_intersect_key(
                        $values,
                        [
                            $resetSettingsOptionName => ''
                        ]
                    );
                } else {
                    /**
                     * Execute the callback sanitazation here,
                     * and not on entry 'sanitize_callback' from `register_setting`,
                     * because that one will be called twice: once triggered
                     * by `update_option` and once by `add_option`,
                     * with `add_option` happening after the extra logic here
                     * (i.e. added on `pre_update_option_{$option}`) has taken
                     * place, which means that it undoes this logic that sets
                     * the state for "reset the settings".
                     *
                     * This call is needed to cast the data
                     * before saving to the DB.
                     */
                    $values = $this->getSettingsNormalizer()->normalizeSettings($values, SettingsCategories::GRAPHQL_API_SETTINGS);
                }

                return $values;
            }
        );

        /**
         * After saving the settings in the DB:
         * - Flush the rewrite rules, so different URL slugs take effect
         * - Update the timestamp
         *
         * This hooks is also triggered the first time the user saves the settings
         * (i.e. there's no update) thanks to `maybeStoreEmptySettings`
         */
        \add_action(
            "update_option_{$option}",
            function (): void {
                \flush_rewrite_rules();

                // Update the timestamp
                $this->getUserSettingsManager()->storeContainerTimestamp();
            }
        );

        /**
         * Register the settings
         */
        \add_action(
            'admin_init',
            function (): void {
                $settingsItems = $this->getSettingsNormalizer()->getAllSettingsItems();
                $settingsEntries = [
                    [
                        'category' => SettingsCategories::GRAPHQL_API_SETTINGS,
                        'field' => self::SETTINGS_FIELD,
                        'option-name' => Options::SETTINGS,
                        'description' => \__('Settings for the GraphQL API', 'graphql-api'),
                    ],
                    [
                        'category' => SettingsCategories::PLUGIN_SETTINGS,
                        'field' => self::PLUGIN_SETTINGS_FIELD,
                        'option-name' => Options::PLUGIN_SETTINGS,
                        'description' => \__('Plugin Settings', 'graphql-api'),
                    ],
                    [
                        'category' => SettingsCategories::PLUGIN_MANAGEMENT,
                        'field' => self::PLUGIN_MANAGEMENT_FIELD,
                        'option-name' => Options::PLUGIN_MANAGEMENT,
                        'description' => \__('Plugin Management', 'graphql-api'),
                    ],
                ];
                foreach ($settingsEntries as $settingsEntry) {
                    $categorySettingsItems = array_filter(
                        $settingsItems,
                        /** @param array<string,mixed> $item */
                        fn (array $item) => $item['settings-category'] === $settingsEntry['category']
                    );
                    $settingsField = $settingsEntry['field'];
                    $settingsOptionName = $settingsEntry['option-name'];
                    $settingsDescription = $settingsEntry['description'];
                    foreach ($categorySettingsItems as $item) {
                        $settingsFieldForModule = $this->getSettingsFieldForModule($item['id']);
                        $module = $item['module'];
                        \add_settings_section(
                            $settingsFieldForModule,
                            // The empty string ensures the render function won't output a h2.
                            '',
                            function (): void {
                            },
                            $settingsField
                        );
                        foreach ($item['settings'] as $itemSetting) {
                            \add_settings_field(
                                $itemSetting[Properties::NAME],
                                $itemSetting[Properties::TITLE] ?? '',
                                function () use ($module, $itemSetting): void {
                                    $type = $itemSetting[Properties::TYPE] ?? null;
                                    $possibleValues = $itemSetting[Properties::POSSIBLE_VALUES] ?? [];
                                    if (!empty($possibleValues)) {
                                        $this->printSelectField($module, $itemSetting);
                                    } elseif ($type === Properties::TYPE_ARRAY) {
                                        $this->printTextareaField($module, $itemSetting);
                                    } elseif ($type === Properties::TYPE_BOOL) {
                                        $this->printCheckboxField($module, $itemSetting);
                                    } elseif ($type === Properties::TYPE_NULL) {
                                        $this->printLabelField($module, $itemSetting);
                                    } else {
                                        $this->printInputField($module, $itemSetting);
                                    }
                                },
                                $settingsField,
                                $settingsFieldForModule,
                                [
                                    'label' => $itemSetting[Properties::DESCRIPTION] ?? '',
                                    'id' => $itemSetting[Properties::NAME],
                                ]
                            );
                        }
                    }

                    /**
                     * Finally register all the settings
                     */
                    \register_setting(
                        $settingsField,
                        $settingsOptionName,
                        [
                            'type' => 'array',
                            'description' => $settingsDescription,
                            /**
                             * Don't execute the callback sanitazation here,
                             * because it will be called twice: once triggered
                             * by `update_option` and once by `add_option`,
                             * with `add_option` happening after the extra logic
                             * added by `pre_update_option_{$option}` has taken
                             * place, which means that it undoes the logic added
                             * on that hook to set the state for "reset the settings".
                             *
                             * Then, the sanitazation is also executed on that hook.
                             */
                            // // This call is needed to cast the data
                            // // before saving to the DB
                            // 'sanitize_callback' => $this->getSettingsNormalizer()->normalizeSettings(...),
                            'show_in_rest' => false,
                        ]
                    );
                }
            }
        );
    }

    protected function getSettingsFieldForModule(string $moduleID): string
    {
        return self::SETTINGS_FIELD . '-' . $moduleID;
    }

    /**
     * The user can define this behavior through the Settings.
     * If `true`, print the sections using tabs
     * If `false`, print the sections one below the other
     */
    protected function printWithTabs(): bool
    {
        return $this->getUserSettingsManager()->getSetting(
            PluginGeneralSettingsFunctionalityModuleResolver::GENERAL,
            PluginGeneralSettingsFunctionalityModuleResolver::OPTION_PRINT_SETTINGS_WITH_TABS
        );
    }

    /**
     * Print the settings form
     */
    public function print(): void
    {
        $settingsItems = $this->getSettingsNormalizer()->getAllSettingsItems();
        if (!$settingsItems) {
            _e('There are no items to be configured', 'graphql-api');
            return;
        }

        $printWithTabs = $this->printWithTabs();

        $primarySettingsItems = [
            [
                'id' => 'graphql-api-settings',
                'name' => \__('GraphQL API Settings', 'graphql-api'),
                'category' => SettingsCategories::GRAPHQL_API_SETTINGS,
                'options-form-field' => self::SETTINGS_FIELD,
            ],
            [
                'id' => 'plugin-settings',
                'name' => \__('Plugin Settings', 'graphql-api'),
                'category' => SettingsCategories::PLUGIN_SETTINGS,
                'options-form-field' => self::PLUGIN_SETTINGS_FIELD,
            ],
            [
                'id' => 'plugin-management',
                'name' => \__('Plugin Management', 'graphql-api'),
                'category' => SettingsCategories::PLUGIN_MANAGEMENT,
                'options-form-field' => self::PLUGIN_MANAGEMENT_FIELD,
                'skip-submit-button' => true,
            ],
        ];
        $activePrimarySettingsID = $primarySettingsItems[0]['id'];
        $tab = App::query(RequestParams::TAB);
        $class = 'wrap';
        if ($printWithTabs) {
            $class .= ' graphql-api-tabpanel vertical-tabs';
        }

        // Specify to only toggle the outer .tab-content divs (skip the inner ones)
        ?>
            <div
                id="graphql-api-primary-settings"
                class="wrap graphql-api-tabpanel"
                data-tab-content-target="#graphql-api-primary-settings-nav-tab-content > .tab-content"
            >
                <h1><?php \_e('GraphQL API — Settings', 'graphql-api'); ?></h1>
                <?php \settings_errors(); ?>
                <div class="nav-tab-container">
                    <!-- Tabs -->
                    <h2 class="nav-tab-wrapper">
                        <?php
                        foreach ($primarySettingsItems as $item) {
                            printf(
                                '<a href="#%s" class="nav-tab %s">%s</a>',
                                $item['id'],
                                $item['id'] === $activePrimarySettingsID ? 'nav-tab-active' : '',
                                $item['name']
                            );
                        }
                        ?>
                    </h2>
                    <div id="graphql-api-primary-settings-nav-tab-content" class="nav-tab-content">
                        <?php
                        foreach ($primarySettingsItems as $item) {
                            /** @var string */
                            $optionsFormField = $item['options-form-field'];
                            /** @var bool */
                            $skipSubmitButton = $item['skip-submit-button'] ?? false;
                            $sectionStyle = sprintf(
                                'display: %s;',
                                $item['id'] === $activePrimarySettingsID ? 'block' : 'none'
                            );
                            ?>
                            <div id="<?php echo $item['id'] ?>" class="tab-content" style="<?php echo $sectionStyle ?>">
                            <?php
                                $categorySettingsItems = array_values(array_filter(
                                    $settingsItems,
                                    /** @param array<string,mixed> $item */
                                    fn (array $settingsItem) => $settingsItem['settings-category'] === $item['category']
                                ));
                                // By default, focus on the first module
                                $activeModuleID = $categorySettingsItems[0]['id'];
                                // If passing a tab, focus on that one, if the module exists
                                if ($tab !== null) {
                                    $moduleIDs = array_map(
                                        fn ($item) => $item['id'],
                                        $categorySettingsItems
                                    );
                                    if (in_array($tab, $moduleIDs)) {
                                        $activeModuleID = $tab;
                                    }
                                }
                            ?>
                                <div class="<?php echo $class ?>">
                                    <?php if ($printWithTabs) : ?>
                                        <div class="nav-tab-container">
                                            <!-- Tabs -->
                                            <h2 class="nav-tab-wrapper">
                                                <?php
                                                foreach ($categorySettingsItems as $item) {
                                                    printf(
                                                        '<a href="#%s" class="nav-tab %s">%s</a>',
                                                        $item['id'],
                                                        $item['id'] === $activeModuleID ? 'nav-tab-active' : '',
                                                        $item['name']
                                                    );
                                                }
                                                ?>
                                            </h2>
                                            <div class="nav-tab-content">
                                    <?php endif; ?>
                                                <form method="post" action="options.php">
                                                    <!-- Artificial input as flag that the form belongs to this plugin -->
                                                    <input type="hidden" name="<?php echo self::FORM_ORIGIN ?>" value="<?php echo $optionsFormField ?>" />
                                                    <!--
                                                        Artificial input to trigger the update of the form always, as to always purge the container/operational cache
                                                        (eg: to include 3rd party extensions in the service container, or new Gutenberg blocks)
                                                        This is needed because "If the new and old values are the same, no need to update."
                                                        which makes "update_option_{$option}" not be triggered when there are no changes
                                                        @see wp-includes/option.php
                                                    -->
                                                    <input type="hidden" name="<?php echo $optionsFormField?>[last_saved_timestamp]" value="<?php echo time() ?>">
                                                    <!-- Panels -->
                                                    <?php
                                                    $sectionClass = $printWithTabs ? 'tab-content' : '';
                                                    \settings_fields($optionsFormField);
                                                    foreach ($categorySettingsItems as $item) {
                                                        $sectionStyle = '';
                                                        $title = $printWithTabs
                                                            ? sprintf(
                                                                '<h2>%s</h2><hr/>',
                                                                $item['name']
                                                            ) : sprintf(
                                                                '<br/><hr/><br/><h2 id="%s">%s</h2>',
                                                                $item['id'],
                                                                $item['name']
                                                            );
                                                        if ($printWithTabs) {
                                                            $sectionStyle = sprintf(
                                                                'display: %s;',
                                                                $item['id'] === $activeModuleID ? 'block' : 'none'
                                                            );
                                                        }
                                                        ?>
                                                        <div id="<?php echo $item['id'] ?>" class="<?php echo $sectionClass ?>" style="<?php echo $sectionStyle ?>">
                                                            <?php echo $title ?>
                                                            <table class="form-table">
                                                                <?php \do_settings_fields($optionsFormField, $this->getSettingsFieldForModule($item['id'])) ?>
                                                            </table>
                                                        </div>
                                                        <?php
                                                    }
                                                    if (!$skipSubmitButton) {
                                                        \submit_button(
                                                            \__('Save Changes (All)', 'graphql-api')
                                                        );
                                                    }
                                                    ?>
                                                </form>
                                    <?php if ($printWithTabs) : ?>
                                            </div> <!-- class="nav-tab-content" -->
                                        </div> <!-- class="nav-tab-container" -->
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php
    }

    /**
     * Enqueue the required assets and initialize the localized scripts
     */
    protected function enqueueAssets(): void
    {
        parent::enqueueAssets();

        $this->enqueueDocsAssets();

        if ($this->printWithTabs()) {
            $this->enqueueTabpanelAssets();
        }
    }

    /**
     * Get the option value
     */
    protected function getOptionValue(string $module, string $option): mixed
    {
        return $this->getUserSettingsManager()->getSetting($module, $option);
    }

    /**
     * Display a checkbox field.
     *
     * @param array<string,mixed> $itemSetting
     */
    protected function printCheckboxField(string $module, array $itemSetting): void
    {
        $name = $itemSetting[Properties::NAME];
        $input = $itemSetting[Properties::INPUT];
        $value = $this->getOptionValue($module, $input);
        ?>
            <label for="<?php echo $name; ?>">
                <input type="checkbox" name="<?php echo self::SETTINGS_FIELD . '[' . $name . ']'; ?>" id="<?php echo $name; ?>" value="1" <?php checked(1, $value); ?> />
                <?php echo $itemSetting[Properties::DESCRIPTION] ?? ''; ?>
            </label>
        <?php
    }

    /**
     * Display a label
     *
     * @param array<string,mixed> $itemSetting
     */
    protected function printLabelField(string $module, array $itemSetting): void
    {
        ?>
            <p>
                <?php echo $itemSetting[Properties::DESCRIPTION] ?? ''; ?>
            </p>
        <?php
    }

    /**
     * Display an input field.
     *
     * @param array<string,mixed> $itemSetting
     */
    protected function printInputField(string $module, array $itemSetting): void
    {
        $name = $itemSetting[Properties::NAME];
        $input = $itemSetting[Properties::INPUT];
        $value = $this->getOptionValue($module, $input);
        $label = isset($itemSetting[Properties::DESCRIPTION]) ? '<br/>' . $itemSetting[Properties::DESCRIPTION] : '';
        $isNumber = isset($itemSetting[Properties::TYPE]) && $itemSetting[Properties::TYPE] === Properties::TYPE_INT;
        $minNumber = null;
        if ($isNumber) {
            $minNumber = $itemSetting[Properties::MIN_NUMBER] ?? null;
        }
        ?>
            <label for="<?php echo $name; ?>">
                <input name="<?php echo self::SETTINGS_FIELD . '[' . $name . ']'; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>" <?php echo $isNumber ? ('type="number" step="1"' . (!is_null($minNumber) ? ' min="' . $minNumber . '"' : '')) : 'type="text"' ?>/>
                <?php echo $label; ?>
            </label>
        <?php
    }

    /**
     * Display a select field.
     *
     * @param array<string,mixed> $itemSetting
     */
    protected function printSelectField(string $module, array $itemSetting): void
    {
        $name = $itemSetting[Properties::NAME];
        $input = $itemSetting[Properties::INPUT];
        $value = $this->getOptionValue($module, $input);
        // If it is multiple, $value is an array.
        // To simplify, deal always with arrays
        if (!is_array($value)) {
            $value = is_null($value) ? [] : [$value];
        }
        $label = isset($itemSetting[Properties::DESCRIPTION]) ? '<br/>' . $itemSetting[Properties::DESCRIPTION] : '';
        $isMultiple = $itemSetting[Properties::IS_MULTIPLE] ?? false;
        $possibleValues = $itemSetting[Properties::POSSIBLE_VALUES] ?? [];
        ?>
            <label for="<?php echo $name; ?>">
                <select name="<?php echo self::SETTINGS_FIELD . '[' . $name . ']' . ($isMultiple ? '[]' : ''); ?>" id="<?php echo $name; ?>" <?php echo $isMultiple ? 'multiple="multiple" size="10"' : ''; ?>>
                <?php foreach ($possibleValues as $optionValue => $optionLabel) : ?>
                    <?php $maybeSelected = in_array($optionValue, $value) ? 'selected="selected"' : ''; ?>
                    <option value="<?php echo $optionValue ?>" <?php echo $maybeSelected ?>>
                        <?php echo $optionLabel ?>
                    </option>
                <?php endforeach ?>
                </select>
                <?php echo $label; ?>
            </label>
        <?php
    }

    /**
     * Display a textarea field.
     *
     * @param array<string,mixed> $itemSetting
     */
    protected function printTextareaField(string $module, array $itemSetting): void
    {
        $name = $itemSetting[Properties::NAME];
        $input = $itemSetting[Properties::INPUT];
        // This must be an array
        $value = $this->getOptionValue($module, $input);
        $label = isset($itemSetting[Properties::DESCRIPTION]) ? '<br/>' . $itemSetting[Properties::DESCRIPTION] : '';
        ?>
            <label for="<?php echo $name; ?>">
                <textarea name="<?php echo self::SETTINGS_FIELD . '[' . $name . ']'; ?>" id="<?php echo $name; ?>" rows="10" cols="40"><?php echo implode("\n", $value) ?></textarea>
                <?php echo $label; ?>
            </label>
        <?php
    }
}
