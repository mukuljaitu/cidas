## Summary
- Fix table filter behavior across the site so filters consistently apply, “Clear All” truly resets UI state, and Back/Forward keeps filter controls in sync with the URL.
- Fix the navbar search UI so only the custom (grey) clear button remains (hide the browser’s built-in search cancel X).

## Current State Analysis
- Table pages use a shared layout that renders:
  - Filters toolbar (`form.filters-bar`) above the table.
  - The table + pagination inside `#tableCard`, which is replaced via AJAX on filter/pagination actions.
  - Source: [table-ui-layout.blade.php](file:///d:/CIDAS/cidas/resources/views/components/table-ui-layout.blade.php#L6-L97)
- `public/js/table-ui.js` implements:
  - AJAX updates by fetching HTML and replacing only `#tableCard`, then calling `syncFiltersBarFromUrl(url)` to update the filter UI without a full page reload.
  - Source: [updateTableFromUrl](file:///d:/CIDAS/cidas/public/js/table-ui.js#L186-L216), [syncFiltersBarFromUrl](file:///d:/CIDAS/cidas/public/js/table-ui.js#L355-L437)
- The current `syncFiltersBarFromUrl` only knows how to sync a specific set of hidden inputs (`filterNameInput`, `filterStatusInput`, etc.) plus the custom “missing” filter controls. It does **not** sync general filter controls like `<select name="product_id">` / `<select name="name">` used in Variants.
  - Example select-based filters: [variants/index.blade.php](file:///d:/CIDAS/cidas/resources/views/variants/index.blade.php#L17-L33)
- Resulting breakages (most visible on select-based filter pages):
  - Clicking “Clear All” updates the table and URL, but the filter selects/chips don’t reset because the filters DOM isn’t replaced and the JS sync doesn’t update those controls.
  - Browser Back/Forward changes the URL and table, but filter controls can remain out of sync with the URL.
- Navbar search uses `<input type="search">` which shows the browser’s built-in cancel X, and it also has a custom clear button. This produces the “two X” effect.
  - Source: [navbar.blade.php](file:///d:/CIDAS/cidas/resources/views/components/navbar.blade.php#L39-L55), [table-ui.css](file:///d:/CIDAS/cidas/public/css/table-ui.css#L334-L402)

## Proposed Changes
### 1) Make filter syncing generic and reliable
**File:** [table-ui.js](file:///d:/CIDAS/cidas/public/js/table-ui.js)
- Replace/extend `syncFiltersBarFromUrl(url)` to synchronize *all* controls inside `form.filters-bar` based on the URL query string:
  - `input[type=text|search|date|hidden]`, `select`, `textarea`: set `.value` from `searchParams.get(name)`; if missing, reset to a sensible default (prefer:
    - the existing `.defaultValue` for inputs/textarea,
    - the option with `selected` attribute for selects,
    - else first option,
    - and if an “All” option exists, prefer “All” when the param is missing).
  - `checkbox`/`radio`: set `.checked` based on `searchParams.getAll(name)` (including `name="missing_sections[]"` style arrays).
- Keep the existing chip-label + option highlighting behavior for the popover-based filters, but drive it from the newly-synced values.
- Preserve and integrate the current “missing filter” behavior:
  - ensure `missing` hidden input, `missing_min`, and `missing_sections[]` reflect the URL.
  - ensure the toggle state and disabled/enabled controls are consistent.

**Why this fixes the site-wide “broken filters” feel**
- Pages that use selects (e.g. Variants) will finally have their filter controls reset/synced after AJAX navigation actions (Clear All, pagination, Back/Forward).
- Pages that use hidden-input popovers (Employees, Products, Tours) keep working, but become more robust because syncing no longer depends on a hardcoded list alone.

### 2) Hide the browser’s built-in search cancel X, keep the custom clear button
**File:** [table-ui.css](file:///d:/CIDAS/cidas/public/css/table-ui.css)
- Add CSS to suppress the native cancel button for search inputs within the navbar search UI, so only the custom clear button remains:
  - `input[type="search"]::-webkit-search-cancel-button { -webkit-appearance: none; appearance: none; }`
  - (Optionally) include the other WebKit search decorations selectors to avoid layout artifacts.

**File:** [navbar.blade.php](file:///d:/CIDAS/cidas/resources/views/components/navbar.blade.php)
- No markup change required unless needed for cross-browser consistency; keep the existing custom clear button as requested.

## Assumptions & Decisions
- Keep the custom (grey) clear button and hide the browser’s built-in cancel X (per your selection).
- Leave browser suggestion/history dropdown behavior unchanged (per your selection).
- Scope is limited to filters rendered in `form.filters-bar` and updated through `#tableCard` AJAX swapping; not adding new filter features beyond “make existing ones work correctly”.

## Verification Steps
- Employees: select Name/Status/Role filters; verify table updates via AJAX; click Clear All; verify chips reset and URL removes params.
- Parties: set multiple filters (Firm/District/State/Type/Salesman + Missing); verify Apply works; click Clear All; verify UI resets and URL resets; use Back/Forward and confirm form state matches URL.
- Variants: change `product_id` and `name` selects; paginate; click Clear All; verify selects reset to “All” and table resets; use Back/Forward and confirm selects sync with URL.
- Tours (if applicable): verify state/month/date/name/status sync and Clear All resets.
- Navbar search: type to show the custom clear button; confirm only one clear X is visible (custom), not the browser-native one.
