<?php
/**
 * Freddie's Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\controllers;

use Craft;
use craft\web\Controller;
use dinkydodouk\freddiescookieconsent\models\CookiesModel;
use dinkydodouk\freddiescookieconsent\records\CookiesRecord;
use dinkydodouk\freddiescookieconsent\records\DodoRecord;
use dinkydodouk\freddiescookieconsent\records\SectionsRecord;
use yii\web\Cookie;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class DodoController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['ajax', 'ajaxsections', 'cookies', 'disallowed'];

    // Public Methods
    // =========================================================================

    public function actionAjax()
    {
        $siteId = Craft::$app->getSites()->currentSite->id;
        $headers = $this->request->getHeaders();

        $settings = DodoRecord::find()
            ->select(['settings_json'])
            ->where(['siteId' => $siteId])
            ->one();

        $csrfToken = Craft::$app->getRequest()->csrfToken;

        $arraySettings = [];
        foreach($settings['settings_json'] as $key => $value) {
            $arraySettings[$key] = $value;
        }
        $arraySettings['csrfToken'] = $csrfToken;
        $arraySettings['redirectUrl'] = Craft::$app->getSecurity()->hashData($headers->get('referer'));

        return $this->asJson($arraySettings);
    }

    public function actionAjaxsections()
    {
        $siteId = Craft::$app->getSites()->currentSite->id;

        $sections = SectionsRecord::find()
            ->where(['siteId' => $siteId])
            ->all();

        return $this->asJson($sections);
    }

    public function actionCookies()
    {
        $this->requirePostRequest();

        $postArray = $this->request->post();

        // THESE DON'T NEED TO BE STORED IN THE COOKIE
        unset($postArray['CRAFT_CSRF_TOKEN']);
        unset($postArray['cookieDuration']);
        unset($postArray['redirect']);

        // COOKIES STORED IN DB
        $siteId = Craft::$app->getSites()->currentSite->id;
        $cookiesModel = new CookiesModel();
        $cookies = $cookiesModel->getCookiesAndSection($siteId);

        $array = [];
        foreach($cookies as $row) {
            if (!in_array($row['section_handle'], $postArray['cookieSelection'])) {
                array_push($array, $row['cookie_name']);
            }
        }

        if (empty($array)) {
            $array = [
                'allow' => 'all'
            ];
        }

        setcookie(
            'freddies-cookie-consent',
            json_encode($array),
            time() + (86400 * $this->request->post('cookieDuration')),
            "/",
            "",
            false,
            false
        );

        return $this->redirectToPostedUrl($this->request->post('redirect'));
    }

    public function actionDisallowed()
    {
        // COOKIES STORED IN DB
        $siteId = Craft::$app->getSites()->currentSite->id;
        $cookiesModel = new CookiesModel();
        $cookies = $cookiesModel->getCookiesAndSection($siteId);

        return $this->asJson($cookies);

    }
}
