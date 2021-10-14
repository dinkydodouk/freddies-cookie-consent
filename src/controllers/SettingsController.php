<?php
/**
 * Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\controllers;

use dinkydodouk\freddiescookieconsent\FreddiesCookieConsent;

use Craft;
use craft\web\Controller;
use dinkydodouk\freddiescookieconsent\models\CookiesModel;
use dinkydodouk\freddiescookieconsent\models\SettingsModel;
use dinkydodouk\freddiescookieconsent\records\CookiesRecord;
use dinkydodouk\freddiescookieconsent\records\DodoRecord;
use dinkydodouk\freddiescookieconsent\records\SectionsRecord;
use Illuminate\Support\Facades\Cookie;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class SettingsController extends Controller
{
    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $this->requireCpRequest();

        $variables['tabs'] = [
            ['label' => 'General', 'url' => '#general'],
            ['label' => 'Appearance', 'url' => '#appearance'],
            ['label' => 'Buttons', 'url' => '#buttons'],
            ['label' => 'Cookie Settings', 'url' => '#cookiesSections'],
            ['label' => 'Cookies', 'url' => '#cookies'],
            // ['label' => 'User Data', 'url' => '#userdata'],
        ];

        $siteId = Craft::$app->getSites()->currentSite->id;
        $settingsModel = new SettingsModel();
        $cookiesModel = new CookiesModel();

        $settings = DodoRecord::find()
            ->where(['siteId' => $siteId])
            ->one();

        if ($settings !== NULL) {
            $variables['settings'] = $settings['settings_json'];
        }
        $variables['sections'] = $settingsModel->getCookieSections($siteId);
        $variables['cookies'] = $cookiesModel->getCookies($siteId);

        return $this->renderTemplate('freddies-cookie-consent/forms/_settings', $variables);
    }

    /**
     * @return mixed
     */
    public function actionSave()
    {
        $this->requireCpRequest();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $siteId = (int)Craft::$app->getSites()->currentSite->id;

        $settingsModel = new SettingsModel();

        $jsonArray = [
            "activatePlugin" => (int)$request->post('activatePlugin'),
            "cookieBannerTitle" => $request->post('cookieBannerTitle'),
            "cookieBannerContent" => $request->post('cookieBannerContent'),
            "privacyCookieEntry" => $request->post('privacyCookieEntry'),
            "privacyCookieUrl" => $request->post('privacyCookieUrl'),
            "removeCss" => (int)$request->post('removeCss'),
            "backgroundColour" => $request->post('backgroundColour'),
            "headerColour" => $request->post('headerColour'),
            "textColour" => $request->post('textColour'),
            "allowBtnText" => $request->post('allowBtnText'),
            "allowBgColour" => $request->post('allowBgColour'),
            "allowTextColour" => $request->post('allowTextColour'),
            "secondaryBtnText" => $request->post('secondaryBtnText'),
            "secondaryBgColour" => $request->post('secondaryBgColour'),
            "secondaryTextColour" => $request->post('secondaryTextColour'),
            "cookieDuration" => $request->post('cookieDuration')
        ];

        $queryRun = false;
        if ($settingsModel->validate()) {
            // UPDATE THE SETTINGS FOR THE COOKIE BANNER
            $settingsRecord = DodoRecord::find()
                ->where([
                    'siteId' => $siteId,
                ])
                ->one();

            if ($settingsRecord == NULL) {
                $settingsRecord = new DodoRecord();
            }

            $settingsRecord->settings_json = $jsonArray;
            $settingsRecord->siteId = $siteId;

            if ($settingsRecord->save()) {
                Craft::$app->getSession()->setNotice('Cookie consent settings have been saved!');
            }

            $sectionsRecord = SectionsRecord::find()
                ->where([
                    'siteId' => $siteId
                ])
                ->all();

            if ($request->post('cookieSections') !== NULL) {
                $section = [];
                foreach($sectionsRecord as $key => $value) {
                    $section[] = $value['section_handle'];
                }

                $postedSection = [];
                foreach($request->post('cookieSections') as $key => $value) {
                    $postedSection[] = $value['section_handle'];
                }

                foreach ($request->post('cookieSections') as $row) {
                    if (!in_array($row['section_handle'], $section)) {
                        $sectionsRecord = new SectionsRecord();

                        $sectionsRecord->section_handle = $row['section_handle'];
                        $sectionsRecord->section_name = $row['section_name'];
                        $sectionsRecord->section_on = $row['section_on'];
                        $sectionsRecord->section_required = $row['section_required'];
                        $sectionsRecord->siteId = $siteId;

                        $sectionsRecord->save();
                    } elseif (in_array($row['section_handle'], $section)) {
                        $sectionsRecord = SectionsRecord::findOne(['section_handle' => $row['section_handle']]);

                        $sectionsRecord->section_handle = $row['section_handle'];
                        $sectionsRecord->section_name = $row['section_name'];
                        $sectionsRecord->section_on = $row['section_on'];
                        $sectionsRecord->section_required = $row['section_required'];
                        $sectionsRecord->siteId = $siteId;

                        $sectionsRecord->update();
                    }
                }

                foreach ($section as $key => $value) {
                    if (!in_array($value, $postedSection)) {
                        $sectionsRecord = SectionsRecord::findOne(['section_handle' => $value]);
                        $sectionsRecord->delete();
                    }
                }
            }

            $cookiesRecord = CookiesRecord::find()
                ->where([
                    'siteId' => $siteId
                ])
                ->all();

            if ($request->post('cookiesUsed') !== NULL) {
                $cookies = [];
                foreach($cookiesRecord as $key => $value) {
                    $cookies[] = $value['cookie_name'];
                }

                $postedCookies = [];
                foreach($request->post('cookiesUsed') as $key => $value) {
                    $postedCookies[] = $value['cookie_name'];
                }

                foreach ($request->post('cookiesUsed') as $row) {
                    if (!in_array($row['cookie_name'], $cookies)) {
                        $cookiesRecord = new CookiesRecord();

                        $cookiesRecord->cookie_name = $row['cookie_name'];
                        $cookiesRecord->cookie_expiry = $row['cookie_expiry'];
                        $cookiesRecord->cookie_description = $row['cookie_description'];
                        $cookiesRecord->cookie_section = $row['section_id'];
                        $cookiesRecord->siteId = $siteId;

                        $cookiesRecord->save();
                    } elseif (in_array($row['cookie_name'], $cookies)) {
                        $cookiesRecord = CookiesRecord::findOne(['cookie_name' => $row['cookie_name']]);

                        $cookiesRecord->cookie_name = $row['cookie_name'];
                        $cookiesRecord->cookie_expiry = $row['cookie_expiry'];
                        $cookiesRecord->cookie_description = $row['cookie_description'];
                        $cookiesRecord->cookie_section = $row['section_id'];
                        $cookiesRecord->siteId = $siteId;

                        $cookiesRecord->update();
                    }
                }

                foreach ($cookies as $key => $value) {
                    if (!in_array($value, $postedCookies)) {
                        $cookiesRecord = CookiesRecord::findOne(['cookie_name' => $value]);
                        $cookiesRecord->delete();
                    }
                }
            }

            $queryRun = true;
        }

        if($queryRun) {
            $this->redirectToPostedUrl();
        }
    }
}