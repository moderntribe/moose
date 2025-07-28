# Rating Stars Block

This custom block renders a visual rating using stars, supporting values from 0 to 5 (including half stars like 3.5).

## Features

- Accepts a numeric rating (0–5) with half-star support
- Adjustable width via a range control (`min: 100`, `max: 840`)
  - Developers can modify these limits in `edit.js`
- Uses `render.php` for frontend output to prevent blocks from breaking if an icon is swapped or updated
- Icons are fully customizable
- Accessible markup using `aria-label` and `aria-hidden`

## Customizing Icons

The block uses two sets of icons:

- **SVGs for frontend rendering**: stored in `/icons/svg`
- **React components for the editor**: stored in `/icons/components`

To customize:

1. Replace the relevant SVGs in `/icons/svg`:
   - `icon-star-full.svg`
   - `icon-star-half.svg`
   - `icon-star-empty.svg`

2. Replace or update the corresponding component files in `/icons/components`:
   - `FullStarIcon.js`
   - `HalfStarIcon.js`
   - `EmptyStarIcon.js`

> ⚠️ Update SVG attributes in components to use JSX syntax  
> (e.g., `strokeWidth` instead of `stroke-width`)

> ⚠️ Add `aria-hidden="true"` to all inline SVGs to improve accessibility

> ⚠️ Use `currentColor` for `fill` and/or `stroke` values so icons inherit theme color

## Styling

The icon color is controlled via CSS in `style.pcss`.

## Notes

- To change default size or rating behavior, edit the range controls or attributes in `edit.js`.
