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

mix.sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/entry/top.js', 'public/js');
mix.js('resources/js/entry/arrive.js', 'public/js');
mix.js('resources/js/entry/attendance.js', 'public/js');
mix.js('resources/js/entry/children.js', 'public/js');
mix.js('resources/js/entry/bus.js', 'public/js');
mix.js('resources/js/entry/daytime.js', 'public/js');
mix.js('resources/js/entry/evening.js', 'public/js');
mix.js('resources/js/entry/sheet/list.js', 'public/js/sheet');
mix.js('resources/js/entry/sheet/list_popup.js', 'public/js/sheet');
mix.js('resources/js/entry/sheet/list_link.js', 'public/js/sheet');
mix.js('resources/js/entry/sheet/list_child.js', 'public/js/sheet');
mix.js('resources/js/entry/sheet/list_yearmonth_classroom.js', 'public/js/sheet');
mix.js('resources/js/entry/sheet/list_year_classroom_grade.js', 'public/js/sheet');
mix.js('resources/js/entry/mst/calendar.js', 'public/js/mst');
mix.js('resources/js/entry/mst/calendar_bizyear.js', 'public/js/mst');
mix.js('resources/js/entry/mst/calendar_bizmonth.js', 'public/js/mst');
mix.js('resources/js/entry/mst/classroom.js', 'public/js/mst');
mix.js('resources/js/entry/mst/bizyear.js', 'public/js/mst');
mix.js('resources/js/entry/mst/children.js', 'public/js/mst');
mix.js('resources/js/entry/mst/children_import.js', 'public/js/mst');
mix.js('resources/js/entry/mst/roster.js', 'public/js/mst');
mix.js('resources/js/entry/mst/aim.js', 'public/js/mst');
mix.js('resources/js/entry/mst/report.js', 'public/js/mst');
mix.js('resources/js/entry/mst/print/report.js', 'public/js/mst/print');
mix.js('resources/js/entry/city/user.js', 'public/js/city');
mix.js('resources/js/entry/city/accesslog.js', 'public/js/city');
mix.js('resources/js/entry/login.js', 'public/js');
mix.js('resources/js/entry/city/information.js', 'public/js/city');
mix.js('resources/js/entry/city/absence.js', 'public/js/city');
mix.js('resources/js/entry/city/event.js', 'public/js/city');

// mac chromeなどでpopper.jsの読み込みエラー発生を抑える対処
// mix.sourceMaps().js('node_modules/popper.js/dist/popper.js', 'public/js').sourceMaps();
