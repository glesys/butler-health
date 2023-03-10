# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.5.1] - 2023-03-10
### Added
- Uniqueness to custom "about" information.
- Method for clearing custom "about" information.

## [0.5.0] - 2023-03-07
### Changed
- **BREAKING**: Do not use `artisan about`.

## [0.4.0] - 2023-03-01
### Changed
- **BREAKING**: Require Laravel 10.

## [0.3.0] - 2023-01-19
### Changed
- **BREAKING**: Use `artisan about` instead of `customApplicationData`.

## [0.2.2] - 2022-12-27
### Added
- Laravel pint.
- Heartbeats.

## [0.2.1] - 2022-03-02
### Added
- Reconnect to the database if a PDO connection is missing in Database check.

## [0.2.0] - 2022-02-14
### Changed
- **BREAKING**: Require Laravel 9.

## [0.1.4] - 2021-09-28
### Changed
- Use "ping" instead of "set"/"get" in Redis check.

## [0.1.3] - 2021-09-28
### Added
- 'runtimeInMilliseconds' in the check response.

## [0.1.0] - 2021-09-16
### Added
- Initial commit
