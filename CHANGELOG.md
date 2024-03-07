# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.2.4] - 03-02-2024

### Added

- Added Spanish localization (thanks to [@dualklip](https://github.com/dualklip))

## [5.2.3] - 09-10-2023

### Fixed

- Fixed save button missing in Nova 4.28 (thanks to [@alancolant](https://github.com/alancolant))

## [5.2.2] - 09-10-2023

### Fixed

- Fixed casting of date and datetime objects when passing them into field ([see issue](https://github.com/outl1ne/nova-settings/issues/172))

## [5.2.1] - 10-08-2023

### Added

- Added Nova ->domain() support to routes (thanks to [@RonMelkhior](https://github.com/RonMelkhior))

### Changed

- Fixed null values not being persisted (thanks to [@Senexis](https://github.com/Senexis))

## [5.2.0] - 29-06-2023

### Added

- Added Nova 4.26 support (thanks to [@puzzledmonkey](https://github.com/puzzledmonkey))

## [5.1.0] - 20-03-2023

### Added

- Added Slovak language (thanks to [@wamesro](https://github.com/wamesro))
- Added resource-loaded and resource-updated Nova events

### Changed

- Allow encoding of JsonSerializable objects (thanks to [@miagg](https://github.com/miagg))
- Settings submenu is now hidden if there is only 1 menu element (thanks to [@johnpuddephatt](https://github.com/johnpuddephatt))
- Fixed image deletion when the image is inside a \Nova\Panel or \Eminiarts\Tabs (thanks to [@marttinnotta](https://github.com/marttinnotta))
- Updated packages

## [5.0.8] - 04-01-2023

### Changed

- Fixed `nova_get_settings()` not casting as expected

## [5.0.7] - 04-01-2023

### Added

- Added dusk identifier to update button (thanks to [@chrillep](https://github.com/chrillep))

### Changed

- Fixed `nova_get_settings()` not working as expected with default values
- Updated packages

## [5.0.6] - 21-10-2022

### Changed

- Added translations for French language (thanks to [@shaffe-fr](https://github.com/shaffe-fr))

## [5.0.5] - 08-09-2022

### Changed

- Fixed help text not rendering (thanks to [@mberatsanli](https://github.com/mberatsanli))

## [5.0.4] - 19-08-2022

### Changed

- Fixed nova-tabs support (thanks to [@Gertiozuni](https://github.com/Gertiozuni))
- Updated packages

## [5.0.3] - 19-07-2022

### Changed

- Fixed File and Image fields not deleting files from disk

## [5.0.2] - 24-05-2022

### Added

- Added Turkish translations (thanks to [@suleymanozev](https://github.com/suleymanozev))

### Changed

- Fixed not being redirected to login when accessing settings while unauthenticated (thanks to [@ianrobertsFF](https://github.com/ianrobertsFF))

## [5.0.1] - 14-05-2022

### Changed

- Fixed migrations (thanks to [@AndreasFurster](https://github.com/AndreasFurster))

## [5.0.0] - 13-05-2022

### Changed

- NB! Changed namespace from OptimistDigital to Outl1ne
- Allow redirections as a result of settings updates (thanks to [@ianrobertsFF](https://github.com/ianrobertsFF))
- Fixed sidebar subpages titles (thanks to [@faab007nl](https://github.com/faab007nl))
- Updated packages

## [4.0.4] - 29-04-2022

### Changed

- Removed loadViewsFrom() call from ServiceProvider
- Fixed memory cache not clearing after settings update
- Updated packages

## [4.0.3] - 25-04-2022

### Changed

- Changed `empty` check to `isset` when loading settings to allow negative but defined values

## [4.0.2] - 08-04-2022

### Changed

- Reworked routing logic

## [4.0.1] - 08-04-2022

### Changed

- Fixed page titles

## [4.0.0] - 08-04-2022

### Added

- Nova 4 support
- Fully compatible with light and dark modes

### Changed

- Dropped Laravel 7 and 8 support
- Dropped PHP 7.X support
- Dropped Nova 3 support
