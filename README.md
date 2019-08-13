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
