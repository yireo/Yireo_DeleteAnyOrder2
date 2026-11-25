# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 25 November 2026
### Added
- Separate ACL resources for deleting orders and for fixing the database, granted automatically to admin roles that already had access to the module
- Audit log of deleted orders and database fixes in `var/log/deleteanyorder.log`
- `deleteanyorder:delete` validates the order ID and asks for confirmation (skip with `--force`); `deleteanyorder:fix` gets `--dry-run` and `--force` options
- PHP 8.5 support

### Changed
- Finding orphaned records no longer loads all order IDs into memory, and the fix reports the number of deleted rows per table

### Fixed
- Deleting an order left its MSI reservations behind, so products stayed unsalable (#3)
- Fixing the database deleted related rows of existing orders instead of only orphaned ones
- Console commands failing with Symfony 7 (Magento 2.4.9)
- Composer conflicts with Magento 2.4.8 from an outdated phpunit requirement, and missing monolog/monolog and psr/log dependencies

### Removed
- PHP 8.1 support

### Security
- Deleting orders, mass deleting orders and fixing the database now require a POST request with a valid form key and the matching ACL permission, closing a CSRF hole via GET requests
- Hardened SQL expressions by quoting identifiers and allow-listing metadata keys

## [1.0.8] - 22 October 2025
### Fixed
- Remove setup version

## [1.0.7] - 9 August 2023
### Fixed
- Long-standing issue with non-working table prefix

## [1.0.6] - 26 May 2021
### Added
- Delete in order overview asks confirmation

## [1.0.5] - 29 July 2020
### Added
- Magento 2.4 compatibility
- Remove PHP dep from composer

## [1.0.4] - May 2019
### Added
- Stricter PHPCS fixes (PSR2, Magento2)
- Add a KeepAChangelog standard
- Lowered prices of extension packages

## Fixed
- Fix invalid `di.xml` issue in 2.3.1

## [1.0.3] - November 2018
### Added
- Magento 2.3 compatibility

## [1.0.2] - June 2018
### Added
- Properly add dependencies

## [1.0.1] - May 2018
### Fixes
- Tests with Performance Toolkit (`extra_large`)
- Optimize `DELETE` queries

## [1.0.0] - March 2018
### Added
- Add Delete mass action in order grid
- CLI to delete, analyse and fix
- Composer package
- Integration tests
- Unit tests
