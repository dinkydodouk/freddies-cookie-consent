<?php
/**
 * Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\models;

use craft\db\Query;
use craft\elements\Entry;
use craft\fields\Color;
use craft\helpers\Json;
use craft\validators\ColorValidator;
use dinkydodouk\freddiescookieconsent\FreddiesCookieConsent;

use Craft;
use craft\base\Model;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class SettingsModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var boolean
     */
    public $activatePlugin = 0;
    public $cookieBannerTitle = 'Cookie Consent';
    public $cookieBannerContent = '';
    public $privacyCookieEntry = '';
    public $privacyCookieUrl = '';
    public $removeCss = 0;
    public $backgroundColour = ColorValidator::class;
    public $headerColour = ColorValidator::class;
    public $textColour = ColorValidator::class;
    public $allowBtnText = 'Allow Cookies';
    public $allowBgColour = ColorValidator::class;
    public $allowTextColour = ColorValidator::class;
    public $secondaryBtnText = 'More Info';
    public $secondaryBgColour = ColorValidator::class;
    public $secondaryTextColour = ColorValidator::class;
    public $cookieDuration = 30;
    public $cookieSections = [];
    public $cookiesUsed = [];
    public $settings_json = Json::class;
    public $siteId = 1;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['activatePlugin', 'boolean'],
            ['activatePlugin', 'default', 'value' => false],
        ];
    }

    public function getCookieSections(int $siteId)
    {
        return (new Query())
            ->select(['id', 'section_handle', 'section_name', 'section_on', 'section_required'])
            ->from('freddiescookieconsent_cookies_sections')
            ->where(['siteId' => $siteId])
            ->all();
    }
}
