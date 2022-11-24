const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


 
mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

//mix.styles('resources/css/jquery.dataTables.min.css', 'public/css/table.css').version();


mix.scripts(['public/station/js/bridge/takipsanbridge.js', 'public/station/js/bridge/bridgeconnect.js', 'public/station/js/bridge/consignment.js', 'public/station/js/bridge/read.js'], 'public/station/js/bridge.js').version();
mix.scripts(['public/station/js/bridge/takipsanbridge.js', 'public/station/js/bridge/settingconnect.js'], 'public/station/js/setting.js').version();

