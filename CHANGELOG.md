# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.1.1] - 2020-02-11

### Changed

- `nova_get_setting()` and `nova_get_settings()` now cache values to avoid duplicate queries

## [2.1.0] - 2020-01-27

### Added

- Allow passing `callable` that returns an array of fields as first argument of `NovaSettings::addSettingsFields()`

## [2.0.2] - 2020-01-27

### Changed

- Improve `nova-translatable` support
- Fixed `Image` field `DELETE` route not working when routes are cached
- Updated packages

## [2.0.1] - 2020-01-17

### Changed

- Support `nova-translatable` rule validation

## [2.0.0] - 2020-01-17

### Changed

- Replaced `customFormatter` logic with `casts` that works identical to Laravel model's cast
- `nova_get_setting_value($key)` is now `nova_get_setting($key)`
- `nova_get_settings($keys = null)` now accepts an array of keys that define which settings to return
- `setSettingsFields($fields, $customFormatter)` is now `addSettingsFields($fields, $casts = [])`
- Added `addCasts($casts = [])`

## [1.4.0] - 2019-12-10

### Added

- Added validation support (thanks to [@tom075](https://github.com/tom075))

## [1.3.0] - 2019-12-03

### Added

- Added support for Laravel Translatable (thanks to [@royduin](https://github.com/royduin))

## [1.2.1] - 2019-11-14

### Changed

- Fixed compatibility with Nova 2.7.0

## [1.2.0] - 2019-11-11

### Added

- Added `Image` support

## [1.1.0] - 2019-09-15

### Added

- Added `Panel` support

## [1.0.0] - 2019-08-13

Initial release.

### Added

- Manage settings fields in code
- UI for editing settings
- Helpers for accessing settings

[2.1.1]: https://github.com/optimistdigital/nova-settings/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/optimistdigital/nova-settings/compare/2.0.2...2.1.0
[2.0.2]: https://github.com/optimistdigital/nova-settings/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/optimistdigital/nova-settings/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/optimistdigital/nova-settings/compare/1.4.0...2.0.0
[1.4.0]: https://github.com/optimistdigital/nova-settings/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/optimistdigital/nova-settings/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/optimistdigital/nova-settings/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/optimistdigital/nova-settings/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/optimistdigital/nova-settings/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/optimistdigital/nova-settings/releases/tag/1.0.0
