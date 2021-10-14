/**
 * Freddie's Cookie Consent plugin for Craft CMS
 *
 * Freddie's Cookie Consent JS
 *
 * @author    Freddie Dodo
 * @copyright Copyright (c) 2021 Freddie Dodo
 * @link      https://www.dinkydodo.com
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */

const cookieComponent = document.getElementById('freddies-consent-bar');

function buildPopup(settings, sections) {
    cookieComponent.classList.add('active');
    cookieComponent.style.backgroundColor = '#' + settings.backgroundColour;

    let html = '';
    html += '<form action="/freddies-cookie-consent/dodo/cookies" class="fcc-container" method="post" accept-charset="UTF-8">';
    html += '<input type="hidden" name="CRAFT_CSRF_TOKEN" value="' + settings.csrfToken + '">';
    html += '<input type="hidden" name="redirect" value="' + settings.redirectUrl + '">';
    html += '<input type="hidden" name="cookieDuration" value="' + settings.cookieDuration + '">';
    html += '<div class="fcc-content">';
    html += '<h3 style="color: #' + settings.headerColour + '">' + settings.cookieBannerTitle + '</h3>';
    html += '<p style="color: #' + settings.textColour + '">' + settings.cookieBannerContent + '</p>';
    html += '<div class="fcc-options">';

    for (let i = 0; i < sections.length; i++) {
        html += '<div class="fcc-checkbox">';
        html += '<label style="color: #' + settings.textColour + '">';
        let sectionOn = '';
        let sectionRequired = '';
        let sectionRequiredText = '';
        if (sections[i].section_on == 1) {
            sectionOn = ' checked';
        }
        if (sections[i].section_required == 1) {
            sectionRequired = '  onclick="return false;"';
            sectionRequiredText = '<p style="margin-bottom: 0;"><small><em>These cookies are required and cannot be turned off</em></small></p>';
        }
        html += '<input type="checkbox" name="cookieSelection[]" value="' + sections[i].section_handle + '"' + sectionOn + sectionRequired + '> ' + sections[i].section_name + '';
        html += '</label>';
        html += sectionRequiredText;
        html += '</div>';
    }

    html += '</div>';
    html += '<div class="fcc-buttons">';
    html += '<button type="submit" id="acceptCookies" class="fcc-primary-btn" style="background-color: #' + settings.allowBgColour + '">' + settings.allowBtnText + '</button>';

    let moreBtn = "";
    if (settings.privacyCookieEntry !== "" && settings.privacyCookieUrl === "") {
        moreBtn = settings.privacyCookieEntry;
    } else {
        moreBtn = settings.privacyCookieUrl;
    }

    if (moreBtn !== "") {
        html += '<a href="' + moreBtn + '" id="acceptCookies" class="fcc-secondary-btn" style="background-color: #' + settings.secondaryBgColour + '; color: #' + settings.secondaryTextColour + '">' + settings.secondaryBtnText + '</button>';
    }
    html += '</div>';
    html += '</div>';
    html += '</form>';

    return html;
}

function getCookie(cookie_name) {
    let name = cookie_name + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}

async function setBanner() {
    let settings = await fetch('/freddies-cookie-consent/dodo/ajax').then((response) => response.json());
    let sections = await fetch('/freddies-cookie-consent/dodo/ajaxsections').then((response) => response.json());

    cookieComponent.innerHTML = buildPopup(settings, sections);
}

if (getCookie('freddies-cookie-consent') === false) {
    setBanner();
}