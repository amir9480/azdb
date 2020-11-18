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
};
