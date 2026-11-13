# Changelog

## [Unreleased]

## [1.0.0] - 2026-11-13

The first stable release. Omnistate National Archives UK: the Discovery catalogue of the UK's National Archives and 2,500 other archives - documents that name a person (wills, service records, parish papers), with their reference. A catalogue, not civil registration.

### Changed

- Requires `omnisense/contracts: ^1.0`, `omnisense/omnistate: ^1.0`: stable versions, so `composer require omnistate/national-archives-uk` installs in a project at the default minimum-stability. `extra.branch-alias` names 1.x (`1.x-dev`); the CI (`.github/workflows/ci.yml`, glitchr-studio/ci's shared workflow) and the release on a tag (`release.yml`).
