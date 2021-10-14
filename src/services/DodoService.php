<?php
/**
 * Freddie's Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\services;

use dinkydodouk\freddiescookieconsent\FreddiesCookieConsent;

use Craft;
use craft\base\Component;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class DodoService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (FreddiesCookieConsent::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
