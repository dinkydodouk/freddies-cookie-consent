<?php
/**
 * Freddie's Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\records;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class AllowRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%freddiescookieconsent_cookies_accept}}';
    }
}
