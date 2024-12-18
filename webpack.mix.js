let mix = require('laravel-mix');
let path = require('path');

mix.extend('nova', new require('laravel-nova-devtool'));

mix
  .setPublicPath('dist')
  .js('resources/js/entry.js', 'js')
  .vue({ version: 3 })
  .nova('outl1ne/nova-settings')
  .alias({
    'laravel-nova': path.join(__dirname, 'vendor/laravel/nova/resources/js/mixins/packages.js'),
  });
