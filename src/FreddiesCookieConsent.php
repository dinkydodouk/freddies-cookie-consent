<?php
/**
 * Freddie's Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent;

use craft\web\AssetBundle;
use craft\web\View;
use dinkydodouk\freddiescookieconsent\records\DodoRecord;
use dinkydodouk\freddiescookieconsent\services\DodoService;
use dinkydodouk\freddiescookieconsent\variables\FreddiesCookieConsentVariable;
use dinkydodouk\freddiescookieconsent\twigextensions\FreddiesCookieConsentTwigExtension;
use dinkydodouk\freddiescookieconsent\models\SettingsModel;

use Craft;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class FreddiesCookieConsent
 *
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 *
 * @property  DodoService $dodo
 */
class FreddiesCookieConsent extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var FreddiesCookieConsent
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // CP Routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    [
                        'freddies-cookie-consent' => 'freddies-cookie-consent/dodo',
                        'freddies-cookie-consent/settings' => 'freddies-cookie-consent/settings',
                        'freddies-cookie-consent/settings/save' => 'freddies-cookie-consent/settings/save',
                    ]
                );
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    [
                        'freddies-cookie-consent/dodo/ajax' => 'freddies-cookie-consent/dodo/ajax',
                        'freddies-cookie-consent/dodo/ajaxsections' => 'freddies-cookie-consent/dodo/ajaxsections',
                        'freddies-cookie-consent/dodo/cookies' => 'freddies-cookie-consent/dodo/cookies',
                        'freddies-cookie-consent/dodo/disallowed' => 'freddies-cookie-consent/dodo/disallowed',
                    ]
                );
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('freddiesCookieConsent', FreddiesCookieConsentVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'freddies-cookie-consent',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );

        Event::on(
            View::class,
            View::EVENT_END_BODY,
            function () {
                echo '<div id="freddies-consent-bar"></div>';
            }
        );

        $request = Craft::$app->getRequest();
        if (
            $this->isInstalled
            && !$request->isConsoleRequest
            && !$request->isCpRequest
        ) {
            $this->registerAssets();
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new SettingsModel();
    }

    public function getSettingsResponse()
    {
        $url = \craft\helpers\UrlHelper::cpUrl('freddies-cookie-consent/settings');

        return \Craft::$app->controller->redirect($url);
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'freddies-cookie-consent/forms/_settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function registerAssets()
    {
        $siteId = Craft::$app->getSites()->currentSite->id;

        $settings = DodoRecord::find()
            ->where(['siteId' => $siteId])
            ->one();

        $view = Craft::$app->getView();

        if ($settings->settings_json['removeCss'] === 0) {
            $assetCss = Craft::$app->assetManager->getPublishedUrl(
                "@dinkydodouk/freddiescookieconsent/assetbundles/freddiescookieconsent/dist/css/FreddiesCookieConsent.css",
                true
            );

            $view->registerCssFile($assetCss, ['position' => View::POS_HEAD]);
        }

        $assetFreddiesJs = Craft::$app->assetManager->getPublishedUrl(
            "@dinkydodouk/freddiescookieconsent/assetbundles/freddiescookieconsent/dist/js/FreddiesCookieConsent.js",
            true
        );

        $view->registerJsFile($assetFreddiesJs);

        $assetCookieJs = Craft::$app->assetManager->getPublishedUrl(
            "@dinkydodouk/freddiescookieconsent/assetbundles/freddiescookieconsent/dist/js/CookieBlocker.js",
            true
        );

        $view->registerJsFile($assetCookieJs);
    }
}
