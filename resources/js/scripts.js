window.trans = function (key) {
    var trans = {
        fa: require('../lang/fa.json')
    };
    if (typeof trans[document.documentElement.lang] !== 'undefined' && typeof trans[document.documentElement.lang][key] !== 'undefined') {
        return trans[document.documentElement.lang][key];
    }
    return key;
}
