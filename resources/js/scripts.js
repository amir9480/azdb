window.trans = function (key) {
    var trans = {
        fa: require('../lang/fa.json')
    };
    if (typeof trans[document.documentElement.lang] !== 'undefined' && typeof trans[document.documentElement.lang][key] !== 'undefined') {
        return trans[document.documentElement.lang][key];
    }
    return key;
}

window.playNotificationSound = function () {
    if (window.ticketLastTimeNotificationSound === undefined || window.ticketLastTimeNotificationSound + 2 < parseInt(Date.now() / 1000)) {
        window.ticketLastTimeNotificationSound = parseInt(Date.now() / 1000);
        let audio = new Audio('/sounds/notification.mp3');
        audio.play();
    }
}

window.appLockLocalStorage = function (name, callback) {
    if (localStorage.getItem('appLock' + name) != null && parseInt(new Date().getTime() / 1000) - parseInt(localStorage.getItem('appLock' + name)) <= 5) {
        return setTimeout(() => appLockLocalStorage(name, callback), 50);
    }
    localStorage.setItem('appLock' + name, parseInt(new Date().getTime() / 1000));
    let result = callback();
    if (result instanceof Promise) {
        result.then(() => localStorage.removeItem('appLock' + name));
    } else {
        localStorage.removeItem('appLock' + name);
    }
}

window.httpErrorMessage = function (status) {
    if (status == 403) {
        return 'Not Authorized';
    } else if (status == 500) {
        return 'Server Error';
    } else {
        return 'Unknown Error';
    }
}

window.httpError = function (status) {
    return alert(httpErrorMessage(status));
}

function setActiveBrowserTabId() {
    if (document.hidden == false) {
        window.appActiveBrowserTabId = appBrowserTabId;
        localStorage.appActiveBrowserTabId = appBrowserTabId;
    }
}

function loadTabsInformation() {
    window.addEventListener('storage', function (event) {
        if (event.key == 'appBrowserTabs') {
            try {
                window.appBrowserTabs = Object.keys(JSON.parse(event.newValue));
            } catch (exception) {}
        }
        if (event.key == 'appActiveBrowserTabId') {
            window.appActiveBrowserTabId = event.newValue;
        }
    });

    window.addEventListener('beforeunload', function () {
        appLockLocalStorage('appBrowserTabs', function () {
            browserTabs = localStorage.appBrowserTabs ? JSON.parse(localStorage.appBrowserTabs) : {};
            if (typeof browserTabs[appBrowserTabId] != 'undefined') {
                delete browserTabs[appBrowserTabId];
            }
            localStorage.appBrowserTabs = JSON.stringify(browserTabs);
        });
    });

    window.appActiveBrowserTabId = localStorage.appActiveBrowserTabId ? localStorage.appActiveBrowserTabId : appBrowserTabId;
    document.addEventListener('visibilitychange', setActiveBrowserTabId);
    window.addEventListener('focus', setActiveBrowserTabId);

    appLockLocalStorage('appBrowserTabs', function () {
        let browserTabs = null;
        try {
            browserTabs = localStorage.appBrowserTabs ? JSON.parse(localStorage.appBrowserTabs) : {};
        } catch (e) {}
        browserTabs = browserTabs instanceof Object ? browserTabs : {};
        browserTabs[appBrowserTabId] = parseInt(Date.now()/1000);
        localStorage.appBrowserTabs = JSON.stringify(browserTabs);
        window.appBrowserTabs = Object.keys(browserTabs);
    });

    setInterval(function () {
        appLockLocalStorage('appBrowserTabs', function () {
            let browserTabs = null;
            try {
                browserTabs = localStorage.appBrowserTabs ? JSON.parse(localStorage.appBrowserTabs) : {};
            } catch (e) {}
            browserTabs = browserTabs instanceof Object ? browserTabs : {};
            browserTabs[appBrowserTabId] = parseInt(Date.now()/1000);

            // Remove outdated browser tabs
            if (appActiveBrowserTabId == appBrowserTabId) {
                for (let i in browserTabs) {
                    if (parseInt(Date.now()/1000) - browserTabs[i] > 15) {
                        delete browserTabs[i];
                    }
                }
            }

            localStorage.appBrowserTabs = JSON.stringify(browserTabs);
            window.appBrowserTabs = Object.keys(browserTabs);
        });
    }, 10000);
}

$(document).ready(function () {
    window.appBroadcastChannel = new BroadcastChannel('app_' + btoa(window.location.host));
    window.appBrowserTabId = sessionStorage.appBrowserTabId ? sessionStorage.appBrowserTabId : sessionStorage.appBrowserTabId = ('app_' + Math.floor(Math.random() * Number.MAX_SAFE_INTEGER).toString());
    loadTabsInformation();
});
