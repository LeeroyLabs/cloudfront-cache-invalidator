<?php

namespace leeroy\cloudfrontcacheinvalidator;

use Craft;
use craft\elements\Asset;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\web\UrlManager;
use craft\web\View;
use leeroy\cloudftontcacheinvalidator\assetbundles\PluginAsset;
use yii\base\Event;
use yii\base\InvalidConfigException;
use craft\base\Plugin;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;
use leeroy\cloudfrontcacheinvalidator\services\CacheService;
use leeroy\cloudftontcacheinvalidator\models\Settings;
use craft\events\ModelEvent;

class CloudfrontCacheInvalidator extends Plugin
{
    public static $plugin;

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSection = true;

    /**
     * Initializes the module.
     * @throws \JsonException
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE,
                static function (TemplateEvent $event) {
                    try {
                        Craft::$app->getView()->registerAssetBundle(PluginAsset::class);
                    } catch (InvalidConfigException $e) {
                        Craft::error(
                            'Error registering AssetBundle - '.$e->getMessage(),
                            __METHOD__
                        );
                    }
                }
            );
        }

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            static function(RegisterCpNavItemsEvent $event) {
                $event->navItems[] = [
                    'url' => 'cloudfront-cache-invalidator',
                    'label' => 'Cloudfront Cache Invalidator',
                ];
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            static function (RegisterUrlRulesEvent $event) {
                $event->rules['invalid-cache'] = 'cloudfront-cache-invalidator/cache/invalid-cache';
                $event->rules['cloudfront-cache-invalidator'] = 'cloudfront-cache-invalidator/cache/cloudfront-cache-invalidator';
            }
        );

        Event::on(
            Asset::class,
            Asset::EVENT_AFTER_SAVE,
            static function (ModelEvent $event) {
                $entry = $event->sender;

                (new CacheService)->invalidCache($entry->url);
            }
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'cloudfront-cache-invalidator/settings',
            ['settings' => $this->getSettings()]
        );
    }
}