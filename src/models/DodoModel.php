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

use dinkydodouk\freddiescookieconsent\FreddiesCookieConsent;

use Craft;
use craft\base\Model;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class DodoModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var boolean
     */
    public $activateCookie = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['activateCookie', 'boolean'],
            ['activateCookie', 'default', 'value' => false],
        ];
    }
}
