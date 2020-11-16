var plugin = {};
plugin.install = function (Vue) {
    Vue.prototype.__ = trans;
}
export default plugin;
