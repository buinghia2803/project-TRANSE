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
let subDirectoryPath = '';

if(process.env.MIX_APP_ENV == 'production' && process.env.MIX_APP_DIR) subDirectoryPath = process.env.MIX_APP_DIR

mix.copyDirectory('resources/assets/common/css', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/common/css')
mix.copyDirectory('resources/assets/common/images', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/common/images');
mix.copyDirectory('resources/assets/common/js', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/common/js');
mix.copyDirectory('resources/assets/common/libs', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/common/libs');
mix.copyDirectory('resources/assets/common/files', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/common/files');
mix.copyDirectory('resources/assets/user/', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/end-user');
mix.copyDirectory('resources/assets/admin/', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/admin_assets');

mix.sass('resources/assets/admin/core/sass/admin.scss', 'public'+ (subDirectoryPath ? '/' + subDirectoryPath : '') +'/common/css/admin.css');
