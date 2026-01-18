# Repository Guidelines

## Project Structure & Module Organization

- `wc-italian-fiscal-fields/` contains the WordPress plugin source.
- `wc-italian-fiscal-fields/wc-italian-fiscal-fields.php` is the plugin entry point and bootstrap.
- `wc-italian-fiscal-fields/includes/` holds core classes (fields, options, validation, admin settings).
- `wc-italian-fiscal-fields/config/settings-fields.php` defines admin settings fields.
- `wc-italian-fiscal-fields/assets/` contains frontend assets (`js/checkout-fields.js`, `css/checkout-fields.css`).
- `wc-italian-fiscal-fields/languages/` stores translation templates (`wc-it-fiscal-fields.pot`).
- `wc-italian-fiscal-fields-v2.0.1.zip` is a release artifact; avoid editing it directly.

## Build, Test, and Development Commands

There is no build system or automated test runner in this repo.

- Install locally by copying `wc-italian-fiscal-fields/` into `/wp-content/plugins/` and activating it in WordPress.
- For debugging, enable WP_DEBUG in `wp-config.php` (see `TROUBLESHOOTING.md`).

## Coding Style & Naming Conventions

- Follow WordPress/WooCommerce coding standards (tabs for indentation, spaces for alignment).
- Always write in Italian: code comments, commit messages, and any responses or documentation.
- Prefix functions/handles with `wc_it_fiscal_`.
- Class naming uses `WC_IT_Fiscal_*` (e.g., `WC_IT_Fiscal_Validator`).
- Keep text strings wrapped in localization functions and use text-domain `wc-it-fiscal-fields`.

## Testing Guidelines

- Manual testing only: verify checkout field visibility rules and validation for each user type.
- Validate admin settings under WooCommerce → Impostazioni → Campi Fiscali.
- Check frontend output in Order Received, My Account, and admin order detail screens.

## Commit & Pull Request Guidelines

- Commit messages are short and imperative; optional prefixes like `Docs:` appear in history.
- Versioned fixes may use a tag like `[v2.0.1] Fix: ...`.
- PRs should include: a concise description, affected WooCommerce/WordPress versions, and reproducible steps.
- Add screenshots or short clips for checkout/admin UI changes.

## Configuration & Compatibility Notes

- The plugin requires WordPress 6.0+, WooCommerce 6.0+, and PHP 7.4+.
- Keep compatibility notes updated in the plugin header and README when versions change.
