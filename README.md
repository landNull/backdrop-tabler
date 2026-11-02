# Tabler for Backdrop

Tabler is a Backdrop theme. It is the front end and the admin theme. Settings on the theme page and Andromeda CLI verbs write the same config. The last write wins.

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

The layout must live in `layouts/tabler/` at the web root so the admin theme can see it. Theme-only layouts are invisible when the admin theme is different.

## Settings

The theme settings page writes `tabler.settings`. Keys:

| Key | Values |
|---|---|
| mode | auto, light, dark |
| primary | blue, azure, indigo, purple, pink, red, orange, yellow, lime, green, teal, cyan |
| base | gray, slate, zinc, neutral, stone |
| radius | 0, 0.5, 1, 1.5 |
| layout | default, fluid, boxed |
| navbar | vertical, horizontal |
| navbar-theme | dark, primary |

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
