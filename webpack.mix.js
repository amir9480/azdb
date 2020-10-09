const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.version()
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/js/sanjab.js', 'public/vendor/sanjab/js')
    .sass('resources/sass/sanjab.scss', 'public/vendor/sanjab/css');

if (! mix.inProduction()) {
    mix.sourceMaps();
}
