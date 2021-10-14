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
class SectionsModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var boolean
     */
    public $section_handle = '';
    public $section_name = '';
    public $section_on = 1;
    public $section_required = 0;
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
}
