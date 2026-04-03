# Plan: Port `routes/orders.php` “Order Book” into Laravel Orders (properly connected)

## Summary
Goal: Replace the current `/orders` page with a Laravel-backed “Order Book” UI that matches the legacy `routes/orders.php` behavior and the provided screenshots, and properly connects Orders to **Employees (salesman)**, **Parties**, and **Transports** using database relations.

Success criteria:
- `/orders` matches the screenshots: KPI cards, category/bill-type filters, party+salesman filters, search, exports, New Order modal, Items modal, Billing & Transport drawer.
- All actions work end-to-end on Laravel: list/filter/sort/search/paginate, create order, manage items, update billing/transport/status, upload/remove receiving proofs, delete (soft delete).
- Orders are stored with `salesman_id`, `party_id`, and `transport_id` relations (while keeping current string fields as display snapshots for backward compatibility).
- Product dropdown supports Fertilizer/Pesticide toggle by adding `products.type`.

## Current State Analysis (grounded in repo)
### Legacy standalone file (NOT wired to app)
- File: [orders.php](file:///d:/CIDAS/cidas/routes/orders.php)
- Characteristics:
  - Single-file HTML/CSS/JS app (Tailwind + Chart.js + SheetJS + jsPDF via CDNs).
  - Fetches from a non-existent `api.php` (actions: `list`, `get_details`, `create`, `update`, `delete_order`, item CRUD, products_by_type, product_packings, transporters).
  - Implements the exact UI patterns shown in screenshots (New Entry modal, Items modal, Billing & Transport drawer, filters + export).

### Current Laravel Orders module (active)
- Routes: [web.php](file:///d:/CIDAS/cidas/routes/web.php#L58-L64) (resource-like endpoints + item endpoints)
- Controller: [OrderController.php](file:///d:/CIDAS/cidas/app/Http/Controllers/OrderController.php)
- UI: [orders/index.blade.php](file:///d:/CIDAS/cidas/resources/views/orders/index.blade.php)
- Gaps vs legacy/screenshots:
  - Uses simple filters (status/type/salesman via querystring), no party dropdown filter UI, no bill-type filters, no missing-files filter, no bill-no sort toggle, no export buttons.
  - New order modal uses free-text item inputs (no product/packing/size dropdown behavior like legacy).
  - Billing drawer uses free-text transport input (not connected to `transports` table; no vehicle/contact preview).
  - Data model stores `salesman`, `party`, `transport` as strings only.

### Existing domain tables we must connect
- Employees: [Employee.php](file:///d:/CIDAS/cidas/app/Models/Employee.php)
- Parties (already linked to employee): [Party.php](file:///d:/CIDAS/cidas/app/Models/Party.php) (`party.employee_id`)
- Transports: [Transport.php](file:///d:/CIDAS/cidas/app/Models/Transport.php)

### Products / variants available for dropdowns
- Inventory: [Product.php](file:///d:/CIDAS/cidas/app/Models/Product.php) + [Variant.php](file:///d:/CIDAS/cidas/app/Models/Variant.php)
- Current limitation: products have no `type` field, but legacy UI requires filtering products by `Fer/Pes`.

## Decisions Locked From Intent Chat
- UI location: **Replace `/orders`** (keep legacy file unused).
- DB links: **Add FK columns** (do not remove existing string columns now).
- Product categorization: **Add `type` to products** (`Fer` / `Pes`).
- Party dropdown: **Filter parties by selected salesman** (`Party.employee_id`).

## Proposed Changes (files + what/why/how)
### 1) Database migrations (relations + product type)
Create new migrations (do not rewrite existing historical migrations):
- Add FK columns to `orders`:
  - `salesman_id` → `employees.id` (nullable, indexed)
  - `party_id` → `parties.id` (nullable, indexed)
  - `transport_id` → `transports.id` (nullable, indexed)
  - Rationale: “proper” linking while keeping current `salesman/party/transport` strings as display snapshots and migration safety.
- Add `type` to `products`:
  - `products.type` string/enum-like (`Fer`/`Pes`), indexed
  - Rationale: power the Fertilizers/Pesticides toggle product dropdown and `get_products_by_type` behavior.

Notes:
- Existing orders remain valid even if IDs are null; UI will display snapshot string fields as fallback.
- New creates/updates will always write both:
  - IDs (`*_id`) for relational integrity
  - snapshot strings (`salesman`, `party`, `transport`) for consistent display and backward compatibility

### 2) Model updates (relations + fillables)
Update:
- [Order.php](file:///d:/CIDAS/cidas/app/Models/Order.php)
  - Add `salesman()` belongsTo Employee, `party()` belongsTo Party, `transport()` belongsTo Transport.
  - Add `salesman_id/party_id/transport_id` to `$fillable`.
- [Product.php](file:///d:/CIDAS/cidas/app/Models/Product.php)
  - Add `type` to `$fillable`.

### 3) Backend endpoints to support the SPA-like UI
Goal: replicate `routes/orders.php` behavior but backed by Laravel controllers and models.

Update/extend:
- [web.php](file:///d:/CIDAS/cidas/routes/web.php)
  - Keep existing CRUD endpoints.
  - Add JSON endpoints under `/orders/...` for the new UI:
    - `GET /orders/api/list` → paginated list + stats, accepts filters:
      - `status`, `type`, `bill_type`, `salesman_id`, `party_id`, `missing_files`, `search`, `bill_no_sort`, `page`, `pageSize`
    - `GET /orders/api/details/{order}` → order + items + computed transport details (or include via eager load)
    - `POST /orders/api/items/bulk` → `{ ids: [] }` → `{ [orderId]: items[] }` (for analytics/top products, like legacy bulk endpoint)
    - `GET /orders/api/salesmen` → salesmen list (employees with salesman role)
    - `GET /orders/api/parties?salesman_id=` → parties for selected salesman (and optionally “All”)
    - `GET /orders/api/transports` → transporter list
    - `GET /orders/api/transports/{transport}` → vehicle/contact details
    - `GET /orders/api/products?type=Fer|Pes` → products list filtered by type
    - `GET /orders/api/product-packings?product_id=` → variant-derived packing/size options

Implementation approach:
- Add these endpoints as new methods on [OrderController.php](file:///d:/CIDAS/cidas/app/Http/Controllers/OrderController.php) OR introduce a dedicated `OrderBookApiController`.
- Reuse existing store/update/item endpoints where possible; extend validation to accept IDs and populate snapshot strings.

Key business rules enforced server-side:
- `party_id` must belong to selected salesman: `Party.employee_id === salesman_id` (when both provided).
- `salesman_id` must be a salesman-eligible employee (same rule used elsewhere: role name contains “salesman”).

### 4) Replace `/orders` UI to match screenshots (port structure from legacy)
Replace the content of:
- [orders/index.blade.php](file:///d:/CIDAS/cidas/resources/views/orders/index.blade.php)

UI behavior to implement (mirrors legacy + screenshots):
- KPI row: Total, Pending Billing, Completed, Receiving, Okay, Cancelled + “New Entry” tile.
- Filter bar buttons:
  - Category: All Orders / Fertilizers / Pesticides
  - Bill type: Type A / Type B
  - Missing Files: filter orders missing receiving proofs (see rule below)
  - Bill No: toggle sort (asc/desc) by bill number
- Table toolbar:
  - Party filter dropdown with built-in search (like legacy)
  - Salesman dropdown
  - Export: CSV, Excel, PDF (use the same client-side libraries as legacy: SheetJS + jsPDF autotable)
- Search box: “Search Party, Salesman, Prod”:
  - Server-side search across party/salesman/bill_no, plus product search using bulk-items endpoint (same concept as legacy).
- New Order Entry modal:
  - Default bill type toggle A/B
  - Bill products toggle (Fer/Pes) drives product dropdown options
  - Order date, salesman select, party select (filtered by salesman)
  - Items quick-add row: product select + packing select + size select + qty + add button
  - Add multiple items before saving
- Manage Order Items modal:
  - Same quick-add row and list with +/− qty and delete (wired to existing item endpoints).
- Billing & Transport drawer:
  - Bill type toggle, bill date, bill number
  - Transport dropdown populated from `transports`
  - Auto-show vehicle type / vehicle no / contact (read-only preview)
  - Receiving proofs image grid + upload + remove existing
  - Delete + Update buttons

“Missing Files” definition (server + UI must match):
- Order is considered missing files when:
  - `status` is `Received` or `Okay` AND
  - `receiving_image_path` is null/empty array

### 5) Inventory UI adjustment (so products can be typed)
Update:
- [ProductController.php](file:///d:/CIDAS/cidas/app/Http/Controllers/ProductController.php) validation and create/update logic to accept `type`.
- [products/index.blade.php](file:///d:/CIDAS/cidas/resources/views/products/index.blade.php) drawer form:
  - Add a “Type” select (Fer/Pes)
  - Update `data-form-fields` and edit-trigger attributes so the generic drawer wiring in [table-ui.js](file:///d:/CIDAS/cidas/public/js/table-ui.js) can populate it.

### 6) Compatibility + rollout strategy
- Keep the existing `/orders` routes and URLs the same; only replace the view + enhance controller behavior.
- Keep existing columns (`salesman`, `party`, `transport`) so existing records still render even if the new FK fields are null.
- New orders will populate both IDs and snapshot strings; old orders can be progressively “fixed” by editing them once.

## Assumptions
- Authentication/middleware for `/orders` remains as currently configured in [web.php](file:///d:/CIDAS/cidas/routes/web.php#L58-L64).
- “Salesman eligibility” remains role-name contains “salesman”, consistent with existing code patterns.
- Exporting data is acceptable client-side using CDN libraries (same as legacy file) rather than adding new composer dependencies.

## Verification Steps (after implementation)
- Run migrations; confirm `orders` has `salesman_id/party_id/transport_id` and `products.type`.
- Open `/orders`:
  - KPI counts match filtered dataset.
  - Filters (status/type/bill type/missing files) combine correctly.
  - Party dropdown filters after selecting salesman.
  - Search finds by party/salesman/bill_no and also by product (via bulk items lookup).
  - Bill No sort toggle changes order.
  - Exports generate CSV/Excel/PDF for the **currently filtered set**.
- Create order:
  - Required fields enforced; party must match salesman; items saved.
  - New order shows correct snapshot strings and FK links.
- Items modal:
  - Add item, increment/decrement qty, delete item updates immediately.
- Billing drawer:
  - Select transport and see vehicle/contact preview.
  - Upload receiving images; remove existing; missing-files filter updates accordingly.
  - Delete order marks it deleted and hides it from list.

