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

mix.combine([
    'resources/assets/plugins/fontawesome-free/css/all.min.css',
    'resources/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
    'resources/assets/dist/css/adminlte.min.css',
    'resources/assets/demo.css',
], 'public/assets/css/api.css');
