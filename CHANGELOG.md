# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).  
The version is defined only in the root file **`VERSION`**. CI injects it into the theme’s `style.css` and `package.json` when deploying. On production deploy, a git tag **vX.Y.Z** is created from `VERSION` if it doesn’t exist.

---

## [Unreleased]

_Nothing yet._

---

## [1.1.0] - 2026-02-25

### Added

- **Security headers:** mu-plugin `eea-security-headers.php` (HSTS, X-Content-Type-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy). CI deploys `wp-content/mu-plugins/` to WP Engine together with the theme.

### Changed

- **SEO:** Theme meta/OG tags are skipped when Rank Math (or similar SEO plugin) is active to avoid duplicate meta and Open Graph output.

---

## [1.0.0] - 2025-02-16

First versioned release.

### Added

- **CI/CD pipeline:** GitHub Actions workflows for deploy to Staging (on push to `develop`) and Production (on push to `main`). Each run builds the theme frontend (`npm ci` + `npm run build`) before deploying to WP Engine.
- **Versioning:** CHANGELOG and theme version in `style.css` / `package.json` (SemVer).
- **SEO meta tags:** Meta description, Open Graph, and Twitter Card tags without plugin (`init/seo-meta.php` + `eea-seo-meta.json`).

### Changed

- **Docker:** Stack migrated from Bitnami to `trafex/wordpress` (PHP-FPM + Nginx). PHP-FPM pool tuned to avoid timeouts (e.g. `request_terminate_timeout`, removal of trafex overrides).
- **README:** Environments (Staging / Production), local dev guide, and deploy summary.

---

## [Previous releases]

Older changes were not recorded in this file. This changelog starts with the trafex migration and CI/deploy updates (v1.0.0).
