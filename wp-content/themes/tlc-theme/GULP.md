# Gulp — TLC Theme

## Prerequisites

- Node.js installed
- Run `npm install` once from the theme root before using any gulp commands
- For `gulp critical`: local site must be running at `https://tlc.giantcreative.local` and **Google Chrome Canary** must be installed

---

## Commands

### `gulp build`
Full production build. Run this before committing or deploying.

- Compiles all SCSS → minified CSS (no source maps)
- Minifies JS
- Optimises images (jpg, png, gif, svg)
- Copies the CSS polyfill script

```bash
gulp build
```

---

### `gulp dev`
Development mode. Use this while actively working on styles or JS.

- Compiles SCSS with **source maps** (no minification)
- Bundles JS with source maps
- Watches `assets/scss/**/*.scss` and `assets/js/src/**/*.js` for changes and recompiles automatically

```bash
gulp dev
```

---

### `gulp critical`
Regenerates the per-page critical CSS files (`assets/css/critical-{slug}.min.css`).

Run this after `gulp build` whenever you make significant above-the-fold style changes.

- Visits all 19 pages on the local site using Chrome Canary
- Extracts above-the-fold CSS at mobile (375px), tablet (768px), and desktop (1440px)
- Outputs one `critical-{slug}.min.css` per page

```bash
gulp critical
```

> Requires: local site running + Chrome Canary installed at `/Applications/Google Chrome Canary.app`

---

### Individual tasks

Run a single pipeline step without the full build:

```bash
gulp styles    # Compile + minify all SCSS only
gulp scripts   # Bundle + minify JS only
gulp images    # Optimise images only
```

---

## Adding styles for a new page

1. Create `assets/scss/pages/{page-slug}.scss`
2. Add the entry to the `cssEntries` array in `gulpfile.js`
3. Run `gulp build`

The new `{page-slug}.min.css` file will be auto-loaded by `includes/enqueue.php` based on the page slug — no PHP changes needed.

---

## File locations

| Source | Output |
|---|---|
| `assets/scss/main.scss` | `assets/css/style.min.css` |
| `assets/scss/pages/{slug}.scss` | `assets/css/{slug}.min.css` |
| `assets/js/src/**/*.js` | `assets/js/forms.js` |
| `assets/images/**` | `assets/images/**` (optimised in place) |
