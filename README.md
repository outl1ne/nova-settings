# Nova Settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/optimistdigital/nova-settings.svg?style=flat-square)](https://packagist.org/packages/optimistdigital/nova-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/optimistdigital/nova-settings.svg?style=flat-square)](https://packagist.org/packages/optimistdigital/nova-settings)

This [Laravel Nova](https://nova.laravel.com) package allows you to create custom settings in code (using Nova's native fields) and creates a UI for the users where the settings can be edited.

## Requirements

- Laravel Nova >= **2.9**

## Features

- Settings fields management in code
- UI for editing settings
- Helpers for accessing settings
- Rule validation support
- Supports [nova-translatable](https://github.com/optimistdigital/nova-translatable) w/ rule validation

## Screenshots

![Settings View](docs/index.png)

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require optimistdigital/nova-settings
```

Publish the database migration(s) and run migrate:

```bash
php artisan vendor:publish --provider="OptimistDigital\NovaSettings\ToolServiceProvider" --tag="migrations"
php artisan migrate
```

Register the tool with Nova in the `tools()` method of the `NovaServiceProvider`:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \OptimistDigital\NovaSettings\NovaSettings
    ];
}
```

## Usage

### Registering fields

Define the fields in your `NovaServiceProvider`'s `boot()` function by calling `NovaSettings::setSettingsFields()`.

```php
\OptimistDigital\NovaSettings\NovaSettings::addSettingsFields([
    Text::make('Some setting', 'some_setting'),
    Number::make('A number', 'a_number'),
]);
```

### Casts

If you want the value of the setting to be formatted before it's returned, pass an array similar to `Eloquent`'s `$casts` property as the second parameter.

```php
\OptimistDigital\NovaSettings\NovaSettings::addSettingsFields([
    // ... fields
], [
  'some_boolean_value' => 'boolean',
  'some_float' => 'float',
  'some_collection' => 'collection',
  // ...
]);
```

### Helper functions

#### nova_get_settings(\$keys = null)

Call `nova_get_settings()` to get all the settings formated as a regular array. If you pass in `$keys` as an array, it will return only the keys listed.

#### nova_get_setting(\$key)

To get a single setting's value, call `nova_get_setting('some_setting_key')`. It will return either a value or null if there's no setting with such key.
