const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

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
mix.sass('resources/assets/sass/voyager.scss', 'resources/assets/dist/css', {
    implementation: require('sass')
})
mix.sass('resources/assets/sass/colors.scss', 'resources/assets/dist/css')
.options({
    processCssUrls: false,
    postCss: [ tailwindcss('tailwind.config.js') ],
}).purgeCss({
    folders: ['resources'],
    whitelistPatterns: [
        /mode-dark/,
        /dark/,
        /w-[0-9]+\/[0-9]+/,     // All variations of width classes we dynamically use in the view-builder
        /w-[0-9]+/,
        /h-[0-9]+/,
        /bg-[a-z]+-[0-9]+/,     // Dynamically used colors in badges, buttons and so on
        /text-[a-z]+-[0-9]+/,   // ^
        /border-[a-z]+-[0-9]+/, // ^
    ],
})
.js('resources/assets/js/voyager.js', 'resources/assets/dist/js')
.copy('node_modules/inter-ui/Inter (web)', 'resources/assets/dist/fonts/inter-ui');
