# Rating Stars Block

This custom block renders a visual rating using stars, supporting values from 0 to 5 (including half stars like 3.5). It's fully accessible and uses CSS background images for star display.

## Features

- Accepts numeric ratings from 0 to 5, including half-star values (e.g. 3.5)
- Adjustable width via a range control (`min: 100px`, `max: 840px`)
  - Developers can modify these limits in `edit.js`
- Server-side rendering via `render.php` to prevent blocks from breaking if an icon is swapped or updated
- Icons are fully customizable
- Accessible markup using `role="img"`, `aria-label` and `aria-hidden`

## Customizing Icons

Star icons are stored as static SVGs in:
`/icons/icon-star-full.svg`
`/icons/icon-star-half.svg`
`/icons/icon-star-empty.svg`

To update the visual style:

1. Replace the SVG files directly.
2. Ensure the new SVGs are colored as needed (no `currentColor` or dynamic fill).
3. Keep the `viewBox` and dimensions consistent for best results.

These SVGs are used as `background-image` values in CSS — no JS or JSX changes are required.

## Styling

Star layout and appearance are controlled in `style.pcss`.

Key classes:
- `rating-stars__container`: Holds the five stars
- `rating-stars__star`: A single star element with a defined `aspect-ratio` so it scales proportionally within the `rating-stars__container`
- `rating-stars__star--full`, `--half`, `--empty`: Determines star type
