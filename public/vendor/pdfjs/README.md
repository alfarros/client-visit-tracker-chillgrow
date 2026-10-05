# PDF.js assets

This directory contains locally hosted assets from `pdfjs-dist` version `5.4.394`.

- `build/pdf.min.mjs` and `build/pdf.worker.min.mjs` power the one-page-at-a-time PDF preview.
- `cmaps`, `standard_fonts`, and `wasm` provide PDF character maps, standard fonts, and optional decoder assets.
- `LICENSE` contains the upstream Apache License 2.0.

The PDF.js library and its worker are served by this application. Patient files remain in Laravel private storage and are requested through authenticated routes.
