# Salient Globe Locations

Interactive globe map with clickable location pins and a scrolling card strip for WPBakery Page Builder.

## Usage

Add the **Globe Locations** element in WPBakery. Configure:

- **Map image** — upload your globe/world map graphic
- **Map alt text** — leave empty for decorative maps
- **Full Width Cards** — bleed the card strip to the viewport edge on desktop
- **Locations** — repeater with name, address, phone, and X/Y position (%)

Shortcode: `[salient_globe_locations]`

## Pin behavior

- **Default:** all pins show a glow matching `--sgl-pin-glow-color` (set same as `--sgl-pin-color` in your theme)
- **Hover preview (desktop):** active pin keeps primary glow; others dim to grey dot + grey glow
- **Selected (click/tap):** dot stays fixed; glow pulses until another location is selected or selection is cleared
- **During interaction:** non-active pins dim using unselected color and glow variables

## Card strip behavior

- **Desktop:** cards scroll continuously in a marquee when no location is selected
- **Desktop drag:** click and drag the card strip to scrub horizontally; the strip stays where you release it
- **Selected:** marquee pauses while a location is selected; resumes when selection is cleared (Escape, outside click, or new selection)
- **Hover/focus:** marquee also pauses while the pointer or keyboard focus is within the card strip
- **Mobile:** cards stack vertically; no drag/swipe (scroll naturally)

## Theme customization

Pin colors use CSS custom properties with **defaults built into the plugin styles**, not re-declared on `.sgl-globe-locations`. Set overrides on `.sgl-globe-locations` (or a specific instance ID like `#sgl-1`) in your child theme `style.css` or Salient **Custom CSS**.

**Tips:**

- Use hex for `--sgl-pin-glow-color` when possible (e.g. `#E51937`). `rgb()` also works.
- Override `--sgl-pin-glow-color` alone to change the selected pin glow color.
- Override `--sgl-pin-selected-shadow` and `--sgl-pin-selected-shadow-pulse` for full control of the inset highlight and outer glow.
- In a child theme, enqueue `style.css` after the plugin stylesheet (see example in Salient child `functions.php`) so overrides always win when both load in the head.

### Quick override (copy-paste)

```css
/* Salient child theme — style.css or Custom CSS */
.sgl-globe-locations {
	--sgl-pin-color: #E51937;
	--sgl-pin-selected-color: #E51937;
	--sgl-pin-glow-color: #E51937;
	--sgl-pin-unselected-color: #666;
	--sgl-pin-unselected-glow-color: #666;
	--sgl-pin-unselected-opacity: 0.4;
	--sgl-pin-pulse-duration: 1.6s;
}
```

Override the full selected glow shadow (inset highlight + outer glow):

```css
.sgl-globe-locations {
	--sgl-pin-glow-color: #E51937;
	--sgl-pin-selected-shadow: 0 1.5px 1.5px 0 rgba(255, 255, 255, 0.25) inset, 0 0 21px 1.5px var(--sgl-pin-glow-color);
	--sgl-pin-selected-shadow-pulse: 0 1.5px 1.5px 0 rgba(255, 255, 255, 0.45) inset, 0 0 42px 8px var(--sgl-pin-glow-color);
}
```

### Per-instance override

Each section gets a unique ID (`sgl-1`, `sgl-2`, …). Target a single map on the page:

```css
#sgl-1 {
	--sgl-pin-glow-color: #0078d7; /* blue glow on first instance only */
}
```

### CSS variables reference

| Variable | Default | Controls |
|----------|---------|----------|
| `--sgl-pin-color` | `#000` | Default dot fill (idle and hover preview) |
| `--sgl-pin-selected-color` | matches `--sgl-pin-color` | Dot fill when a location is selected |
| `--sgl-pin-glow-color` | `#E51937` | Glow color for idle and selected pins (set same as `--sgl-pin-color` for matching glow) |
| `--sgl-pin-shadow` | inset + `0 0 21px 1.5px` glow | Idle box-shadow on all pins |
| `--sgl-pin-selected-shadow` | same as idle glow | Selected pin box-shadow at rest (pulse baseline) |
| `--sgl-pin-selected-shadow-pulse` | `0 0 42px 8px` glow | Selected pin box-shadow at pulse peak |
| `--sgl-pin-unselected-color` | `#888` | Dot fill for dimmed pins during hover/selection |
| `--sgl-pin-unselected-glow-color` | matches `--sgl-pin-unselected-color` | Glow color for dimmed pins |
| `--sgl-pin-unselected-shadow` | inset + `0 0 14px 1px` glow | Full box-shadow for dimmed pins |
| `--sgl-pin-unselected-opacity` | `0.45` | Opacity of dimmed pin buttons |
| `--sgl-pin-pulse-duration` | `1.6s` | Selected glow pulse cycle length |

### Example: brand accent glow, matching dot and glow

```css
.sgl-globe-locations {
	--sgl-pin-color: #E51937;
	--sgl-pin-glow-color: #E51937;
	--sgl-pin-unselected-color: #666;
	--sgl-pin-unselected-glow-color: #666;
	--sgl-pin-unselected-opacity: 0.35;
}
```

### Example: stronger pulse

```css
.sgl-globe-locations {
	--sgl-pin-selected-shadow-pulse: 0 1.5px 1.5px 0 rgba(255, 255, 255, 0.5) inset, 0 0 50px 10px var(--sgl-pin-glow-color);
	--sgl-pin-pulse-duration: 2s;
}
```

### Reduced motion

Users with `prefers-reduced-motion: reduce` still see the selected pin glow; the pulse animation is disabled automatically.

## Accessibility

- Keyboard navigation for pins and cards
- `aria-pressed` on selected pin/card
- Live region announcements on selection
- 44px minimum pin touch targets
- Escape clears selection
