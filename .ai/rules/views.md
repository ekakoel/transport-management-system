---
paths:
  - 'resources/views/**'
---

# Views

## Build pages with one Blade layout
Internal application pages extend layouts.app and provide content through @section/@yield; do not introduce Blade components or @include partials.

## Use inline page scripts
Keep shared behavior in resources/js/app.js; put page-specific behavior in an inline script block at the end of the Blade view.

## Write user-facing text in Indonesian
Write UI labels, flash messages, and validation errors in Indonesian directly in Blade and controllers; there are no localization files.

## Format dates with DateFormatter
Display dates through App\Support\DateFormatter::format(); it centralizes date formatting and the Belum diatur empty-state.
