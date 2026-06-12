# Aras Localize

Custom WordPress plugin for LocalizeJS integration on the XPLM site.

## Features

- Branded language switcher shortcode and theme shortcode replacement
- Alternate language link list shortcode
- Hreflang tags for localized pages
- Localized sitemap support for Yoast SEO
- Feed suppression for translated URLs
- Optional prerender proxy support for translated pages and crawlers

## Configuration

Plugin settings are available under the `Aras Localize` options page in WordPress admin.

Global setting:

- `API key`

Module settings:

- `Language Switcher`
- `Link List`
- `Prerender`

## Shortcodes

- `[aras_localize_switcher]`
- `[aras_localize_link_list]`

## Repository Layout

This plugin lives inside the main WordPress repository for WPEngine deployment and is also synced to its own standalone GitHub repository with `git subtree`.
