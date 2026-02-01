# BMPOS — Claude Master Spec
**Version:** 1.0  
**Date:** 2025-02-01  
**Status:** Ready for Kiro / Claude coding sessions  

---

## 0. Role & Working Mode

You are a senior Laravel developer and tech lead. You are building a production-ready MVP for **BMPOS** — a private sales, inventory, and payment-tracking application.

### Behaviour rules:
- Be **conservative**. Do not invent features that are not explicitly described here.
- Be **security-first**. Every route, query, and view must enforce access control.
- Do **not** change the tech stack for any reason.
- When in doubt, ask — do not assume.
- Write **clean, readable, well-organised** code. Prioritise clarity over cleverness.
- All code comments and file headers/footers must follow the **File Comment Standard** in Section 8.
- After every completed task, produce a **Task Summary** following Section 9.

---

## 1. Tech Stack (Locked)

| Layer | Technology | Notes |
|---|---|---|
| Framework | Laravel 11 | Do not downgrade or change |
| Frontend | Blade | No React, no Vue, no Livewire unless explicitly added later |
| Auth | Laravel Breeze (Blade) | Handles login, password, profile edit |
| Database (local) | PostgreSQL via pgAdmin | Installed natively on Windows, no Docker |
| Database (prod) | Neon (PostgreSQL) | SSL/TLS required via `sslmode=require` |
| Hosting (prod) | Laravel Cloud | Connected via GitHub |
| PHP | 8.3 | Do not upgrade to 8.4 until MVP is live |
| Currency | NOK only | All amounts stored as **integer øre** |
| Docker | **Not used** | Machine cannot run it |

---

## 2. Product Goals

BMPOS is an admin-controlled application for:

1. **Inventory management** — tracking items the admin owns and may sell.
2. **Customer management** — admin creates and manages customer accounts.
3. **Orders & agreements** — linking items to customers via orders, supporting multiple items per order.
4. **Payments & installments** — recording partial payments (avdrag) against orders.
5. **Outstanding balance tracking** — calculating and displaying what each customer owes.
6. **Customer portal** — a secure, read-only view so customers can see their own status.
7. **Landing page** — a modern, professional public-facing page that serves as the entry point.

### What is explicitly NOT in scope (MVP):
- Payment gateway / online payment integration
- Product images or media uploads
- Shipping / logistics / tracking
- Multi-currency
- Email notifications or auto-sending
- Public registration / signup
- Signed/token-based shareable URLs (standard auth-gated links only)
- Email verification (`verified` middleware is **not** used)
- Multiple admins

---

## 3. Users & Access Control (Locked)

### 3.1 User Types

| Role | `users.role` value | Created by | Can self-register? |
|---|---|---|---|
| Admin | `admin` | Seeder (initial) | No |
| Customer | `customer` | Admin only | No |

There is **no public registration route**. The Breeze register route must be removed or disabled.

`Customer = user`. There is no separate `customers` table. A customer is simply a `users` row with `role = 'customer'`.

### 3.2 Permission Matrix

| Action | Admin | Customer |
|---|---|---|
| View own profile | ✅ | ✅ |
| Edit own profile (name, email, password) | ✅ | ✅ |
| Delete own account | ✅ | ✅ (own only) |
| View any customer's data | ✅ | ❌ |
| Create / edit / delete customers | ✅ | ❌ |
| Create / edit / delete items | ✅ | ❌ |
| Create orders | ✅ | ❌ |
| Add order lines | ✅ | ❌ |
| Register payments | ✅ | ❌ |
| Close / cancel orders | ✅ | ❌ |
| View own orders (read-only) | ✅ | ✅ (own only) |
| View own dashboard (read-only) | ✅ | ✅ |
| Access `/admin/*` routes | ✅ | ❌ (403) |

### 3.3 Key Security Rules
- **No data leakage**: Customer queries must always be scoped via `auth()->user()->orders()` or equivalent. Never use a free query like `Order::find($id)` without policy check.
- **Policies enforce access on show routes**: `OrderPolicy` is mandatory.
- **Admin middleware blocks all non-admin users** from `/admin/*`.
- **Customer can only delete their own account**. Confirm with a modal before executing.
- **Admin can delete any customer account** via admin routes (separate from profile deletion).

---

## 4. Data Model (Locked)

### 4.1 Tables

#### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | Laravel default |
| name | string | Required |
| email | string | Unique, required |
| password | string | Hashed |
| role | string | `admin` or `customer`. Default: `customer` |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `items` (Inventory / Lager)
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| name | string | Required |
| description | text | Nullable |
| purchase_price | integer | Nullable. Stored in **øre** |
| target_price | integer | Nullable. Stored in **øre** |
| status | string | `available`, `reserved`, `sold`, `archived` |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `orders`
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| customer_id | bigint (FK → users.id) | Required |
| status | string | `open`, `closed`, `cancelled` |
| total_amount | integer | In **øre**. See 4.3 for calculation rules |
| notes | text | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `order_lines`
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| order_id | bigint (FK → orders.id) | |
| item_id | bigint (FK → items.id) | |
| unit_price | integer | In **øre** |
| quantity | integer | Default: 1 |

#### `payments`
| Column | Type | Notes |
|---|---|---|
| id | bigint (PK) | |
| order_id | bigint (FK → orders.id) | |
| amount | integer | In **øre** |
| paid_at | date | Required |
| note | string | Nullable |

### 4.2 Eloquent Relations

```
User
  └── hasMany → Order (via customer_id)

Order
  ├── belongsTo → User (customer)
  ├── hasMany  → OrderLine
  └── hasMany  → Payment

OrderLine
  ├── belongsTo → Order
  └── belongsTo → Item

Item
  └── hasMany → OrderLine (optional, for reverse lookups)
```

### 4.3 Business Rules & Calculations

#### Outstanding balance per order:
```
paid_amount    = SUM(payments.amount) for this order
outstanding    = orders.total_amount - paid_amount
```
Implement as **accessor methods** on the `Order` model:
- `getPaidAmountAttribute()`
- `getOutstandingAmountAttribute()`

#### Order `total_amount` logic:
- **Default**: `total_amount` is set to the sum of `(unit_price × quantity)` across all `order_lines` when lines are added.
- **Admin can override**: Admin may manually change `total_amount` on the order detail page (e.g., to apply a discount or custom agreement).
- **UI must show both values** when they differ: display "Auto: X kr" and "Overstyrt: Y kr" so it is clear what happened.

#### Item status transitions:
| Transition | When it happens |
|---|---|
| `available` → `reserved` | Automatically when the item is added to an `order_line` |
| `reserved` → `available` | Automatically if the order is **cancelled** |
| `reserved` → `sold` | Automatically when the order is **closed** |
| Any → `archived` | Manually by admin |
| `sold` / `archived` | Cannot be added to a new order |

### 4.4 Currency Formatting

All amounts are stored as **integer øre** (e.g., 15000 = 150,00 kr).

A global helper function must exist:
```php
// Usage: format_nok(15000) → "150,00 kr"
// Usage: format_nok(0)     → "0,00 kr"
```
This helper must be used **everywhere** amounts are displayed. Never format money inline in a view.

---

## 5. Routes & Pages (Locked Structure)

### 5.1 Public Routes

| Method | Route | Page | Notes |
|---|---|---|---|
| GET | `/` | Landing page | Modern, professional, customer-facing |
| GET | `/login` | Login | Breeze default |

No `/register` route. It must be removed or commented out.

### 5.2 Customer Routes (auth required)

| Method | Route | Controller / Action | Notes |
|---|---|---|---|
| GET | `/dashboard` | `CustomerDashboardController@index` | Read-only overview |
| GET | `/orders` | `CustomerOrderController@index` | List own orders only |
| GET | `/orders/{order}` | `CustomerOrderController@show` | Policy-protected |
| GET | `/profile` | Breeze ProfileController | Edit own profile |
| PATCH | `/profile` | Breeze ProfileController | Update name/email/password |
| DELETE | `/profile` | `ProfileDeletionController` | Delete own account (with confirmation) |

### 5.3 Admin Routes (auth + admin middleware)

All routes below are prefixed with `/admin` and protected by the `EnsureUserIsAdmin` middleware.

#### Dashboard
| Method | Route | Notes |
|---|---|---|
| GET | `/admin` | Admin dashboard — key metrics |

#### Customers
| Method | Route | Notes |
|---|---|---|
| GET | `/admin/customers` | List all customers |
| GET | `/admin/customers/create` | Create customer form |
| POST | `/admin/customers` | Store new customer |
| GET | `/admin/customers/{user}` | Customer detail — orders, totals, outstanding |
| GET | `/admin/customers/{user}/edit` | Edit customer |
| PUT | `/admin/customers/{user}` | Update customer |
| DELETE | `/admin/customers/{user}` | Delete customer (with confirmation) |

#### Items
| Method | Route | Notes |
|---|---|---|
| GET | `/admin/items` | List all items |
| GET | `/admin/items/create` | Create item form |
| POST | `/admin/items` | Store new item |
| GET | `/admin/items/{item}/edit` | Edit item |
| PUT | `/admin/items/{item}` | Update item |

#### Orders
| Method | Route | Notes |
|---|---|---|
| GET | `/admin/orders` | List all orders (filterable by status) |
| GET | `/admin/orders/create` | Create order form (select customer) |
| POST | `/admin/orders` | Store new order |
| GET | `/admin/orders/{order}` | Order detail — lines, payments, outstanding |
| POST | `/admin/orders/{order}/lines` | Add an order line (select item) |
| DELETE | `/admin/orders/{order}/lines/{line}` | Remove an order line |
| POST | `/admin/orders/{order}/payments` | Register a payment |
| DELETE | `/admin/orders/{order}/payments/{payment}` | Remove a payment |
| POST | `/admin/orders/{order}/close` | Close the order |

---

## 6. UI / UX / DX Requirements

### 6.1 Landing Page (`/`)
- **Modern, professional, and inviting.** This is the first thing a customer sees.
- Use a clean hero section with a clear headline explaining what the portal is for (from the customer's perspective).
- Two clear CTAs: **"Logg inn"** (primary) and a secondary element (e.g., "Kontakt oss" or placeholder contact info).
- Brief explanation of what the customer gets: oversikt over kjøp, utestående beløp, betalingshistorikk.
- **No admin-specific information** is visible on this page.
- Design tone: clean, trustworthy, slightly warm. Not corporate-cold.
- Must be fully responsive.

### 6.2 Customer Dashboard (`/dashboard`)
Three summary cards at the top:
| Card | Value |
|---|---|
| Total kjøpt | Sum of `total_amount` across all orders |
| Total betalt | Sum of all payments across all orders |
| Utestående | Total kjøpt − Total betalt |

Below the cards: a list of **open orders** with a link to each order detail.

### 6.3 Customer Order Detail (`/orders/{order}`)
- List of items (order lines) with name, price, quantity.
- Payment history table: date, amount, note.
- Summary row: Total / Betalt / Utestående (using `format_nok`).

### 6.4 Admin Dashboard (`/admin`)
Key metrics displayed prominently:
| Metric | Description |
|---|---|
| Total utestående (alle kunder) | Sum of outstanding across all open orders |
| Åpne ordrer | Count of orders with status `open` |
| Antall kunder | Count of users with role `customer` |
| Items i lager | Count by status: available / reserved / sold |

Below: a table of **top customers by outstanding balance** (e.g., top 5–10). Each row links to the customer detail page.

### 6.5 Admin Customer Detail (`/admin/customers/{user}`)
- Customer info (name, email).
- **"Kopier link"** button that copies the customer's dashboard URL (`/dashboard`) to clipboard. Note: this link requires the customer to be logged in. It is not a public/shareable token.
- Summary cards: Total kjøpt / Total betalt / Utestående (same as customer dashboard but shown from admin's view).
- List of all orders for this customer, with status and outstanding per order.

### 6.6 General UI Rules
- Use **Breeze's default layout** as the base. Keep it consistent.
- Navigation:
  - **Admin**: sidebar or top nav with links to Dashboard, Customers, Items, Orders.
  - **Customer**: top nav with Dashboard and Mine Ordrer.
- **Empty states**: Always show a friendly message when a list is empty. Never show a blank table.
- **Flash messages**: Show success/error messages after create, update, delete actions.
- **Confirmation modals**: Required before any delete action (customer account, order line, payment, customer via admin).
- **Status badges**: Use coloured badges for item status (`available` = green, `reserved` = yellow, `sold` = grey, `archived` = muted) and order status (`open` = blue, `closed` = green, `cancelled` = red).
- **All amounts** must be formatted using the `format_nok()` helper. Never format inline.
- UI text can be in **Norwegian**. Code identifiers, class names, file names must be in **English** (Laravel convention).

---

## 7. Security Checklist (Claude must verify before marking any task done)

- [ ] Are all customer-facing queries scoped to `auth()->user()`?
- [ ] Is `OrderPolicy` applied on all `show` routes for orders?
- [ ] Are all `/admin/*` routes behind the `EnsureUserIsAdmin` middleware?
- [ ] Are all form inputs validated via **Form Request** classes?
- [ ] Is `$fillable` defined on every model (no mass assignment vulnerability)?
- [ ] Are all amounts stored as **integer øre** (no floats anywhere)?
- [ ] Is public signup/registration **disabled**?
- [ ] Is `verified` middleware **not used** (no email verification)?
- [ ] Do delete actions require a confirmation step?
- [ ] Is CSRF protection active on all forms (standard Blade `@csrf`)?
- [ ] Are N+1 queries prevented with `with()` / eager loading where relevant?

---

## 8. File Comment Standard

Every new or modified file must include a **header** and a **footer**.

### Header (top of file):
```
/**
 * File: [relative path from project root, e.g. app/Models/Order.php]
 * Purpose: [1–2 sentences — what this file does]
 * Dependencies: [optional — key classes/packages this file relies on]
 */
```

### Footer (bottom of file):
```
/**
 * Summary: [1–3 sentences describing what this file contains and its role in the system]
 */
```

Keep both short. Do not over-explain. If the purpose is obvious from the filename and class name, the header can be minimal — but it must still be present.

---

## 9. Task Process & Summaries

### Rules:
- Work in **numbered tasks** as defined in the Task Roadmap (Section 10).
- Sub-tasks (e.g., 1.1, 1.2) are part of the parent task.
- After **each parent task** is fully complete, create a file:

```
TASK_<N>_SUMMARY.md
```

### Required content in each summary:

```markdown
# Task <N> Summary — <Short Title>

## What was done
- Brief list of what was created or changed.

## Files created / modified
- List of file paths.

## How to test locally
- Commands to run.
- What to expect (e.g., "navigate to /admin, log in as admin, see dashboard").

## Security notes
- What protects the data in this task (middleware, policies, scoping, validation).

## TODOs / Notes for next task
- Anything the next task depends on or should be aware of.
```

---

## 10. Task Roadmap

### Task 1 — Foundation & Auth Lock-down
| Sub-task | Description |
|---|---|
| 1.1 | Install and configure Laravel Breeze (Blade). **Disable / remove the public registration route.** |
| 1.2 | Add `role` column to `users` table. Create `EnsureUserIsAdmin` middleware. |
| 1.3 | Create `AdminUserSeeder` — seeds one admin user with known credentials. |
| 1.4 | Build the **landing page** (`/`). Set up post-login redirects: admin → `/admin`, customer → `/dashboard`. |
| 1.5 | Create a basic `/dashboard` stub for customers and a basic `/admin` stub for admin (content added in later tasks). |

**Deliverable:** Auth works. No signup. Admin and customer are gated to their own areas. Landing page is live.

---

### Task 2 — Data Model & Policies
| Sub-task | Description |
|---|---|
| 2.1 | Create migrations: `items`, `orders`, `order_lines`, `payments`. Run migrations. |
| 2.2 | Create Eloquent models with relations and `$fillable`. Add `format_nok()` helper. Add `getPaidAmountAttribute()` and `getOutstandingAmountAttribute()` accessors to `Order`. |
| 2.3 | Create `OrderPolicy`. Ensure customer routes use it. |
| 2.4 | Write feature tests: customer cannot see another customer's order (403), admin can see all orders, guest is redirected to login on protected routes. |

**Deliverable:** Database schema is correct. Relations work. Policies enforce access. Helper formats money. Tests pass.

---

### Task 3 — Admin: Customers & Dashboard
| Sub-task | Description |
|---|---|
| 3.1 | Build the **admin dashboard** with metrics: total outstanding, open orders count, customer count, items by status, top customers by outstanding. |
| 3.2 | Build **Customers CRUD**: list, create, edit, view detail, delete (with confirmation modal). |
| 3.3 | Build **Customer detail page**: summary cards, "Kopier link" button, order list with outstanding per order. |

**Deliverable:** Admin can manage customers and see key metrics.

---

### Task 4 — Admin: Items & Orders
| Sub-task | Description |
|---|---|
| 4.1 | Build **Items CRUD**: list (with status badges), create, edit. Handle status transitions via UI. |
| 4.2 | Build **Orders**: create (select customer), detail view, add order lines (select item, triggers `reserved` status). Handle auto-calculation of `total_amount` and the admin override field. |
| 4.3 | Build **Payments**: add payment form on order detail, display payment history. |
| 4.4 | Build **Close order** flow: button on order detail, confirms, sets order to `closed`, sets items to `sold`. Handle **cancel order** flow: sets order to `cancelled`, reverts items to `available`. |
| 4.5 | Build **Delete order line** and **delete payment** (with confirmation modals). Update item status and totals accordingly. |

**Deliverable:** Admin can run the full workflow: create item → create order → add lines → register payments → close order.

---

### Task 5 — Customer Portal (Read-Only)
| Sub-task | Description |
|---|---|
| 5.1 | Build **Customer dashboard** (`/dashboard`): three summary cards (Total kjøpt, Total betalt, Utestående) + list of open orders. |
| 5.2 | Build **Customer orders list** (`/orders`): table of all own orders with status and outstanding. |
| 5.3 | Build **Customer order detail** (`/orders/{order}`): items list, payment history, totals. Policy-protected. |

**Deliverable:** Customer can log in and see everything relevant to them — nothing more.

---

### Task 6 — Pre-Deploy Hardening
| Sub-task | Description |
|---|---|
| 6.1 | Audit all Form Requests for completeness. Add custom validation error messages (in Norwegian). Review and fix any edge cases (e.g., closing an order with no lines, paying more than outstanding). |
| 6.2 | Add or improve empty states across all list views. Polish UI: spacing, badges, button styles, table styling. |
| 6.3 | Create a `DevDataSeeder` that seeds realistic sample data: 3–4 customers, 5–6 items, several orders in different states, payments. Useful for testing and demos. |

**Deliverable:** App is solid, handles edge cases, looks good, and has seed data for demos.

---

### Task 7 — Deploy Documentation
| Sub-task | Description |
|---|---|
| 7.1 | Write `DEPLOY.md` with step-by-step instructions for Neon + Laravel Cloud. |
| 7.2 | Document SSL/TLS configuration (`sslmode=require`) and how to set env variables in Laravel Cloud. |
| 7.3 | Write a production checklist: run migrations, seed admin, verify routes, test customer login. |

**Deliverable:** A complete guide that can be followed to deploy without guessing.

---

## 11. Common AI Mistakes — Do Not Make These

| Mistake | Why it's bad | What to do instead |
|---|---|---|
| Using `float` for money | Floating point errors (e.g., 0.1 + 0.2 ≠ 0.3) | Always use `integer øre` |
| `Order::find($id)` without policy check | Any user can access any order | Use policy gate or scope query to `auth()->user()` |
| Forgetting `$fillable` on models | Mass assignment vulnerability | Define `$fillable` explicitly on every model |
| No Form Request validation | Raw input hits the database | Every store/update must go through a Form Request |
| Hardcoding role checks in Blade only | Backend is unprotected | Enforce in middleware AND policies. Blade checks are UI-only hints. |
| Forgetting `@csrf` in forms | POST/PUT/DELETE will fail with 419 | Always include `@csrf` in every Blade form |
| N+1 queries on order lists | Slow pages when there are many orders | Use `with(['orderLines', 'payments'])` when fetching orders |
| Leaving `/register` route active | Allows public signup | Remove or comment out the register route after Breeze install |
| Using `verified` middleware | Breaks auth if email verification is not set up | Do not use `verified`. Use only `auth`. |
| Formatting money in the view | Inconsistent display, easy to forget øre → kr conversion | Always use `format_nok()` helper |
| No empty states | Confusing blank pages for new users | Every list view must handle the zero-data state |
| No confirmation before delete | Accidental data loss | Always show a confirmation modal before any destructive action |

---

## 12. Environment & Configuration Reference

### Local `.env` (key values):
```env
APP_NAME=BMPOS
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bmpos
DB_USERNAME=postgres
DB_PASSWORD=YOUR_LOCAL_PASSWORD
```

### Production `.env` (Neon — key values):
```env
APP_NAME=BMPOS
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=pgsql
DB_HOST=<your-neon-host>.neon.tech
DB_PORT=5432
DB_DATABASE=<your-neon-db-name>
DB_USERNAME=<your-neon-username>
DB_PASSWORD=<your-neon-password>
DB_SSLMODE=require
```

> **Note on `DB_SSLMODE`:** Neon requires encrypted connections. Laravel's `pgsql` driver supports this via the `sslmode` option. This is handled in `config/database.php` by reading `DB_SSLMODE` from `.env` and passing it to the connection options. No extra packages needed.

---

*End of BMPOS Claude Master Spec v1.0*