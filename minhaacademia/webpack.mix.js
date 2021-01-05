const mix = require('laravel-mix');
const public_html = '../htdocs/site';
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

mix.styles(['resources/css/normalize.css'], public_html.concat('/css/normalize.css'))
   .styles(['resources/css/skeleton.css'], public_html.concat('/css/skeleton.css'))
   .styles(['resources/css/custom.css'], public_html.concat('/css/custom.css'));

mix.copyDirectory('resources/img', public_html.concat('/img'));

if (mix.inProduction()) {
   mix.version();
}

