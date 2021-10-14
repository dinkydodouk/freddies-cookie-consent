function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function removeCookies() {
    let disallow = JSON.parse(decodeURIComponent(getCookie('freddies-cookie-consent')));

    disallow.forEach((res) => {
        let name = res;
        let path = '/';

        document.cookie = `${name}=; expires=Thu, 01-Jan-1970 00:00:01 GMT;`;

        location.pathname.split('/').forEach(chunk => {
            path += path.substr(-1) === '/' ? chunk : '/' + chunk;
            document.cookie = `${name}=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/; domain=.${window.location.hostname}`; // try with path and generic subdomain
            document.cookie = `${name}=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/; domain=${window.location.hostname}`; // try with path and domain
            document.cookie = `${name}=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/;`; // try only with path
        });
    });

    let cookieDesc =
        Object.getOwnPropertyDescriptor(Document.prototype, 'cookie') ||
        Object.getOwnPropertyDescriptor(HTMLDocument.prototype, 'cookie');

    if (cookieDesc && cookieDesc.configurable) {
        Object.defineProperty(document, 'cookie', {
            get: function() {
                return cookieDesc.get.call(document);
            },
            set: function(val) {
                let c = val.split('=')[0];
                if (disallow.includes(c)) {
                    cookieDesc.set.call(document, val);
                }
            }
        });
    }
}

if (document.cookie.indexOf('freddies-cookie-consent') === -1) {
    Object.defineProperty(document, 'cookie', {
        get: function() {
            return '';
        },
        set: function() {
        }
    });
} else {
    removeCookies()
}