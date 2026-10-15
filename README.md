# PDF Viewer - PDF Viewer for WordPress

Displays PDFs on pages and posts using **PDF.js** (bundled locally, no CDN), with basic protection features.

**Author:** Manish Kafle ([GitHub](https://github.com/K4fledmanish) · [Website](https://k4fledmanish.github.io/))

## Features

- Responsive canvas-based PDF viewer
- Previous / Next page navigation below the document
- Right-click disabled, Ctrl+P / Ctrl+S blocked
- Multiple viewers on one page
- Shortcode; works in Elementor (Shortcode widget), Gutenberg and Classic Editor

## Requirements

- WordPress 6.x
- PHP 7.4+

## Installation

1. Download `pdf-viewer.zip` from the [Releases](../../releases) page.
2. In WordPress go to Plugins > Add New > Upload Plugin, choose the zip and activate.

Or copy the `pdf-viewer` folder to `/wp-content/plugins/` manually.

## Usage

```
[pdf_viewer url="https://yoursite.com/wp-content/uploads/2026/10/file.pdf"]
```

| Attribute | Default | Description |
|-----------|---------|-------------|
| `url` / `src` | - | PDF file URL (required) |
| `width` | `100%` | Max width of the viewer |
| `proxy` | `no` | `yes` loads the PDF through a third-party proxy (`api.allorigins.win`). Only use it if the PDF is on a different domain without CORS headers. |

## Notes

The browser-side protections (disabled right-click and shortcuts) only deter casual copying. Anyone who can view a PDF in the browser can still capture it.

## License

[MIT](LICENSE) © 2026 Manish Kafle

Bundled third-party software: [PDF.js](https://github.com/mozilla/pdf.js) by Mozilla, licensed under Apache-2.0 (see `assets/js/PDFJS-LICENSE.txt`).
