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
    'resources/assets/plugins/select2/css/select2.min.css',
    'resources/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
    'resources/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
    'resources/assets/plugins/jqvmap/jqvmap.min.css',
    'resources/assets/dist/css/adminlte.min.css',
    'resources/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
    'resources/assets/plugins/daterangepicker/daterangepicker.css',
    'resources/assets/plugins/summernote/summernote-bs4.css',
    'resources/assets/demo.css',
], 'public/assets/css/api.css');

mix.combine([
    'resources/assets/plugins/jquery/jquery.min.js',
    'resources/assets/plugins/jquery-ui/jquery-ui.min.js',
    'resources/assets/plugins/bootstrap/js/bootstrap.bundle.min.js',
    'resources/assets/plugins/select2/js/select2.full.min.js',
    'resources/assets/plugins/moment/moment.min.js',
    'resources/assets/plugins/daterangepicker/daterangepicker.js',
    'resources/assets/plugins/summernote/summernote-bs4.min.js',
    'resources/assets/dist/js/adminlte.js',
    'resources/assets/dist/js/demo.js',
], 'public/assets/js/api.js');

mix.copyDirectory('resources/assets/dist/img', 'public/assets/img');
mix.copyDirectory('resources/assets/plugins/fontawesome-free/webfonts', 'public/assets/webfonts');

mix.copy('resources/assets/dist/css/adminlte.min.css.map', 'public/assets/css/adminlte.min.css.map');
mix.copy('resources/assets/dist/js/adminlte.min.js.map', 'public/assets/js/adminlte.min.js.map');
