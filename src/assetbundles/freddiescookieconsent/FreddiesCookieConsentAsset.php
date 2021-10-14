<?php
/**
 * Freddie's Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\assetbundles\freddiescookieconsent;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class FreddiesCookieConsentAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@dinkydodouk/freddiescookieconsent/assetbundles/freddiescookieconsent/dist";

        $this->js = [
            'js/FreddiesCookieConsent.js',
        ];

        $this->css = [
            'css/FreddiesCookieConsent.css',
        ];

        parent::init();
    }
}
