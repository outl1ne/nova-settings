let mix = require('laravel-mix')

mix.extend('nova', require('laravel-nova-devtool'))

mix.setPublicPath('dist')
  .js('resources/js/entry.js', 'dist')
  .vue({ version: 3 })
  .nova('outl1ne/nova-settings')

