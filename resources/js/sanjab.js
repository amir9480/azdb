require('sanjab');

Vue.use(require('sanjab-ticket').default);

if (document.querySelector('#sanjab_app')) {
    window.sanjabApp = new Vue({
        el: '#sanjab_app',
    });
}
