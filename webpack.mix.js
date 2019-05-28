let mix = require('laravel-mix');

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

mix.options({ processCssUrls: false }).sass('resources/assets/sass/app.scss', 'publishable/assets/css', { implementation: require('node-sass') })
.js('resources/assets/js/app.js', 'publishable/assets/js')
.copy('node_modules/tinymce/skins', 'publishable/assets/js/skins')
.copy('resources/assets/js/skins', 'publishable/assets/js/skins')
.copy('node_modules/tinymce/themes/modern', 'publishable/assets/js/themes/modern');
