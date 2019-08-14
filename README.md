# Nova Settings

This [Laravel Nova](https://nova.laravel.com) package allows you to create custom settings in code (using Nova's native fields) and creates a UI for the users where the settings can be edited.

## Requirements

- Laravel Nova >= 2.0

## Features

- Settings fields management in code
- UI for editing settings
- Helpers for accessing settings

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
\OptimistDigital\NovaSettings\NovaSettings::setSettingsFields([
    Text::make('Some setting', 'some_setting'),
    Number::make('A number', 'a_number'),
]);
```

### Custom formatting

If you want the value of the setting to be formatted before it's returned, pass a `Closure` as the second parameter to the `setSettingsFields` function. The function receives two arguments: `key` and `value`.

```php
\OptimistDigital\NovaSettings\NovaSettings::setSettingsFields([
    // ... fields
], function ($key, $value) {
    if ($key === 'some_boolean_value') return boolval($value);
    return $value;
});
```

### Helper functions

#### nova_get_settings()

Call `nova_get_settings()` to get all the settings formated as a regular array.

#### nova_get_setting_value(\$key)

To get a single setting's value, call `nova_get_setting_value('some_setting_key')`. It will return either a value or null if the key is missing.
