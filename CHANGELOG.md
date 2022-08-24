# Colorit Changelog
> A palette fieldtype built for [Craft 4](https://craftcms.com)

## 4.0.0 - 2022-08-24

### Changed
- Bump version numbers to mirror crafts major versions

### Added
- Craft 4 compatibility
- Optional html5 picker support for custom colors ([#30](https://github.com/presseddigital/colorit/issues/30))
- Custom color value persists when switching between palette color and custom colors

## 1.1.2.1 - 2021-07-28

### Added
- Added hashhex option example to templating in README.

## 1.1.2 - 2021-07-28

### Changed
- :warning: Auto Color Output Changed
- The Auto (Best Guest) option in settings will now output a value including the # symbol (#123456) for any color with an opacity set to 100 and an RGBa value where the opacity < 100. This is so the default twig fieldtype {{ entry.myColoritFieldName }} can handle all color options. For existing fields the previous output should remain (e.g. no #). A resave of the preset/ field maybe required.
- Update README imagery and include [Tailwind](https://tailwindcss.com/) usage example.

## 1.1.1 - 2021-07-26

### Added
- Option to display field labels ([#17](https://github.com/presseddigital/colorit/issues/17))

### Changed
- Slight UI improvements

## 1.1.0 - 2021-07-23

### Fixed
- Fixed some UI issues ([#32](https://github.com/presseddigital/colorit/issues/32) Credit to @hinderson)

## 1.0.9.3 - 2020-04-28

### Fixed
- Fixed another error related to the preset field map. ([#21](https://github.com/presseddigital/colorit/issues/21))

## 1.0.9.2 - 2020-04-27

### Fixed
- Fixed update error related to the preset field map. ([#21](https://github.com/presseddigital/colorit/issues/21))

## 1.0.9.1 - 2020-04-23

### Fixed
- Fixed update error after changed namespace, thanks @brandonkelly. ([#19](https://github.com/presseddigital/colorit/issues/19))

## 1.0.9 - 2020-04-22

### Added
- Support for setting default colors
- Table attribute support

### Fixed
- Fixed template error

## 1.0.8 - 2019-01-18

### Fixed
- Template errors on the about page

## 1.0.7 - 2018-12-03

### Fixed
- Javascript error when only custom color used
- Added an ID column to preset list

## 1.0.6.1 - 2018-11-08

### Changed
- Improved installation docs

## 1.0.6 - 2018-11-06

### Added
- Added the color label as a title attribute on the cp inputs

### Changed
- Removed `!important` from the inline styles

### Fixed
- Fixed a bug where default color options displayed when all unchecked

## 1.0.5 - 2018-10-16

### Added
- Improved validation of color palette handles (must be unique)

### Fixed
- Issue with preset value outputting empty string when set to a preset color

### Changed
- Updated plugin sidenav structure to match our other plugins

## 1.0.4 - 2018-09-20

### Added
- Wrap up ready for release to the store
- Added validation for palette colours

## 1.0.3 - 2018-09-18

### Added
- Renamed plugin

## 1.0.2 - 2018-09-17

### Added
- Added preset settings logic

## 1.0.1 - 2018-09-04

### Added
- Tidy up ready for release

## 1.0.0 - 2018-08-20

### Added
- Initial release
