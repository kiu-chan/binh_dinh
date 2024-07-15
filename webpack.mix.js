const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       //
   ])
   .copy('node_modules/leaflet/dist/leaflet.css', 'public/css')
   .copy('node_modules/leaflet/dist/images', 'public/images')
   .copy('node_modules/leaflet/dist/leaflet.js', 'public/js');
