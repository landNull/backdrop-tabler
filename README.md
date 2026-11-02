# Tabler for Backdrop

Tabler is a Backdrop theme. It is the front end and the admin theme. Settings on the theme page and Andromeda CLI verbs write the same config. The last write wins.

## Tabler vs Barrio

| Feature | Tabler | Barrio |
|---|---|---|
| Design system | Complete admin/dashboard look (typography, spacing, components) | Layout + Bootstrap markup only; "no style rather than layout" |
| Vertical sidebar | Built-in `navbar-vertical`, folded/icon-only mode | Offcanvas / left-slide options, not Tabler's sidebar model |
| Color modes | Light / dark / auto via `data-bs-theme`, primary, base palette, radius | Theme settings + optional Bootswatch; no Tabler token system |
| Icons | 6,000+ Tabler Icons (matched to the UI) | Optional SVG icons; not Tabler Icons |
| Charts | ApexCharts integration, multiple chart types, theme-aware colors | None |
| Dashboard components | Cards with gradients, stats, timelines, pricing, empty states, avatars, sparklines | Basic card layouts only |
| Plugins | Charts, datepickers, selects, dropzone, sortable, calendars (load on demand) | None |
| RTL | Every stylesheet ships RTL | Not a core feature |
| Demo pages | 100+ ready pages (dashboards, auth, forms, tables) | None; expects a subtheme |
| CSS variables | Full Tabler token set (`--tblr-*`) | Bootstrap variables via SASS subtheme |
| Forms | Extended form controls, validation states, input groups, masks | Standard Bootstrap form markup |

Summary: Barrio gives clean Bootstrap 5 structure. Tabler gives the finished admin/dashboard design system on top of that structure. For a Tabler Backdrop theme, use Barrio only as a markup reference; the look, sidebar, tokens, icons, and components come from Tabler itself.

Note: `tabler.info` declares no `base theme`, so Barrio is not a runtime dependency here. The Barrio influence is copied Bootstrap 5 markup (menus, breadcrumbs), while the design system comes from the Tabler CDN plus `css/tabler.css` keyed to the `--tblr-*` tokens.

## Layout

The layout template is `layouts/tabler/layout--tabler.tpl.php`. Regions:

| Region | Holds |
|---|---|
| header | site name |
| navigation | main menu |
| top | hero, promoted cards |
| content | page content |
| sidebar | search, recent |
| footer | footer links |

The layout must live in `layouts/tabler/` at the web root so the admin theme can see it. Theme-only layouts are invisible when the admin theme is different. `andro apply theme` copies the theme's `layouts/tabler/` into the platform `web/layouts/tabler/` when it is present.

## Settings

The theme settings page writes `tabler.settings` (vertical tabs: Framework, Appearance, Layout and navigation, Breadcrumbs and dates). Keys:

| Key | Values |
|---|---|
| cdn | 1 (jsDelivr CDN), module (bundled `dist/`), empty (disabled) |
| version | Tabler CDN version, e.g. 1.4.0 (list fetched from the jsDelivr API, textfield fallback) |
| mode | auto, light, dark |
| primary | blue, azure, indigo, purple, pink, red, orange, yellow, lime, green, teal, cyan |
| base | gray, slate, zinc, neutral, stone |
| radius | 0, 0.5, 1, 1.5 |
| layout | default, fluid, boxed |
| navbar | vertical, horizontal |
| navbar-theme | dark, primary |
| navbar-user-menu | 0/1, account dropdown in the navbar |
| breadcrumb-home | 0/1, show the Home breadcrumb link |
| breadcrumb-title | 0/1, append the current page title |
| datetime | 0/1, "XX time ago" for nodes and comments |

The framework CSS/JS is injected in `tabler_css_alter()` / `tabler_js_alter()` at a weight that keeps it ahead of `css/tabler.css` and `js/tabler.js`, whether it comes from the CDN or the bundled copy in `dist/` (Tabler 1.4.0, minified).

## CLI

Andromeda verbs:

| Command | What it does |
|---|---|
| `andro apply theme @site tabler` | sets theme and layouts |
| `andro place blocks @site` | site name, menu, content, search |
| `andro theme blog @site` | same, plus recent posts |
| `andro theme set @site key value` | sets a design token |
| `andro theme list` | prints the tokens |
| `andro theme color @site #hex` | sets primary color |

## Files

Content images live in the site files directory. They are not theme-owned. Image style URLs must include the site path. A symlink from `web/files` to the site files directory is the current workaround.

## Cache

Devel and test run with cache off. Live runs with cache on. `stage_health` checks this.

## Tests

`php tests/run_tests.php` runs a standalone harness (no Backdrop install needed): it stubs the Backdrop API, loads `template.php`/`theme-settings.php`, and asserts the behavior of every theme hook plus `php -l`/`node --check` on all shipped files. Exit code 0 = green.
