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

use craft\db\mysql\QueryBuilder;
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
class CookiesModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var boolean
     */
    public $cookie_name = '';
    public $cooky_expiry = '';
    public $cookie_description = '';
    public $cookie_section = SectionsModel::class;
    public $siteId = 1;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    public function getCookies(int $siteId)
    {
        return (new Query())
            ->select(['id', 'cookie_name', 'cookie_expiry', 'cookie_description', 'cookie_section AS section_id'])
            ->from('freddiescookieconsent_cookies')
            ->where(['siteId' => $siteId])
            ->all();
    }

    public function getCookiesAndSection(int $siteId)
    {
        return (new Query())
            ->select(['c.id', 'c.cookie_name', 'c.cookie_expiry', 'c.cookie_description', 'c.cookie_section', 'cs.section_handle'])
            ->from(['c' => 'freddiescookieconsent_cookies'])
            ->join('INNER JOIN', 'freddiescookieconsent_cookies_sections cs', 'c.cookie_section = cs.id')
            ->where(['c.siteId' => $siteId])
            ->all();
    }
}
