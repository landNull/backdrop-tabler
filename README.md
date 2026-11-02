# Tabler for Backdrop

A Backdrop theme that uses [Tabler](https://tabler.io). Set it as the default theme and as the admin theme. All styling is on the theme settings page.

## Install

Copy this directory to `web/themes/tabler`. Enable it.

```
andro theme enable @site tabler
andro theme default @site tabler
```

Set it as the admin theme under Appearance.

## Settings

Appearance, then the Tabler settings tab.

| Setting | What it sets |
|---|---|
| Color mode | `data-bs-theme` auto, light, dark |
| Primary color | `data-bs-theme-primary` |
| Neutral palette | `data-bs-theme-base` |
| Corner radius | `data-bs-theme-radius` |
| Page width | `data-bs-layout` |
| Navigation | sidebar or top bar |
| Navigation color | dark or primary |

Tabler CSS and JS load from the CDN. Uncheck that box to use a local copy.

## License

MIT. Tabler is MIT. See LICENSE.
