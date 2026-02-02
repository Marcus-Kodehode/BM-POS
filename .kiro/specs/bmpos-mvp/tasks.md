# Implementation Plan: BMPOS MVP

## Overview

This implementation plan breaks down the BMPOS MVP into discrete, testable tasks. Each task builds on previous work and includes specific deliverables. The plan follows a logical progression: foundation → data layer → admin features → customer portal → polish → deployment.

---

## Tasks

### Task 1: Foundation & Auth Setup

**Goal:** Secure authentication with role-based access and password change enforcement.

- [x] 1.1 Disable public registration route
  - Comment out or remove `Route::get('/register')` and `Route::post('/register')` in `routes/auth.php`
  - _Requirements: 1.4_

- [x] 1.2 Add user table columns
  - Create migration to add `role` (string, default 'customer'), `password_change_required` (boolean, default false), `deleted_at` (timestamp, nullable)
  - Run migration
  - _Requirements: 1.5, 1.6, 9.1_

- [x] 1.3 Create middleware
  - Create `EnsureUserIsAdmin` middleware (checks `auth()->user()->isAdmin()`, aborts 403 if false)
  - Create `EnsurePasswordChanged` middleware (redirects to profile.edit if `password_change_required = true`)
  - Register both in `bootstrap/app.php`
  - _Requirements: 1.3, 1.6_

- [x] 1.4 Create AdminUserSeeder
  - Seed one admin user with known credentials (email: `admin@bmpos.no`, password: `password`, role: `admin`)
  - _Requirements: 2.1_

- [x] 1.5 Update User model
  - Add `SoftDeletes` trait
  - Add `$fillable`: `name`, `email`, `password`, `role`, `password_change_required`
  - Add `$casts`: `password_change_required` => `boolean`
  - Add methods: `isAdmin()`, `isCustomer()`
  - Add `orders()` relationship
  - _Requirements: 1.1, 2.1, 9.1_

- [x] 1.6 Set up post-login redirects
  - Modify `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
  - Admin → `/admin`, Customer → `/dashboard`
  - _Requirements: 1.2_

- [x] 1.7 Create stub dashboards
  - Create `/dashboard` route and view (customer dashboard stub)
  - Create `/admin` route and view (admin dashboard stub)
  - Apply `auth` middleware to `/dashboard`
  - Apply `auth` + `admin` middleware to `/admin`
  - _Requirements: 1.2, 1.3_

- [x] 1.8 Build landing page
  - Create modern, responsive landing page at `/`
  - Hero section with headline, subtext, "Logg inn" CTA
  - Features section (3 columns: order overview, balance tracking, payment history)
  - Personal, warm tone
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6_

**Checkpoint:** Run `php artisan migrate`, seed admin, test login as admin (redirects to `/admin`), test landing page on mobile and desktop.

---

### Task 2: Data Models & Relationships

**Goal:** Complete database schema with models, relationships, and helper functions.

- [x] 2.1 Create items migration
  - Columns: `id`, `name`, `description`, `purchase_price`, `target_price`, `status`, `created_at`, `updated_at`, `deleted_at`
  - _Requirements: 3.1, 9.1_

- [x] 2.2 Create orders migration
  - Columns: `id`, `order_number` (unique), `customer_id` (FK), `status`, `total_amount`, `notes`, `created_at`, `updated_at`, `deleted_at`
  - _Requirements: 4.1, 9.1_

- [x] 2.3 Create order_lines migration
  - Columns: `id`, `order_id` (FK), `item_id` (FK), `unit_price`, `quantity`, `deleted_at`
  - _Requirements: 4.4, 9.1_

- [x] 2.4 Create payments migration
  - Columns: `id`, `order_id` (FK), `amount`, `paid_at`, `payment_method`, `note`, `deleted_at`
  - _Requirements: 5.1, 5.2, 5.3, 9.1_

- [x] 2.5 Run migrations
  - Execute `php artisan migrate`

- [x] 2.6 Create Item model
  - Add `SoftDeletes` trait
  - Add `$fillable`: `name`, `description`, `purchase_price`, `target_price`, `status`
  - Add `$casts`: `purchase_price` => `integer`, `target_price` => `integer`
  - Add `orderLines()` relationship
  - Add `isAvailable()` method
  - _Requirements: 3.1, 3.2, 3.7_

- [x] 2.7 Create Order model
  - Add `SoftDeletes` trait
  - Add `$fillable`: `order_number`, `customer_id`, `status`, `total_amount`, `notes`
  - Add `$casts`: `total_amount` => `integer`
  - Add `boot()` method to auto-generate `order_number` on create
  - Add relationships: `customer()`, `orderLines()`, `payments()`
  - Add accessors: `getPaidAmountAttribute()`, `getOutstandingAmountAttribute()`
  - Add method: `isOverpaid()`
  - _Requirements: 4.1, 4.3, 4.7, 5.4, 5.5_

- [x] 2.8 Create OrderLine model
  - Add `SoftDeletes` trait
  - Add `$fillable`: `order_id`, `item_id`, `unit_price`, `quantity`
  - Add `$casts`: `unit_price` => `integer`, `quantity` => `integer`
  - Add relationships: `order()`, `item()`
  - Add accessor: `getTotalAttribute()` (unit_price * quantity)
  - _Requirements: 4.4_

- [x] 2.9 Create Payment model
  - Add `SoftDeletes` trait
  - Add `$fillable`: `order_id`, `amount`, `paid_at`, `payment_method`, `note`
  - Add `$casts`: `amount` => `integer`, `paid_at` => `date`
  - Add relationship: `order()`
  - _Requirements: 5.1, 5.2, 5.3_

- [x] 2.10 Create format_nok() helper
  - Create `app/helpers.php`
  - Add `format_nok(int $amount): string` function
  - Register in `composer.json` autoload.files
  - Run `composer dump-autoload`
  - _Requirements: 8.2, 8.3_

- [x] 2.11 Create OrderPolicy
  - Add `view()` method: admin or owner
  - Add `update()` method: admin only
  - Add `delete()` method: admin only
  - Register in `AuthServiceProvider`
  - _Requirements: 6.6, 10.5_

**Checkpoint:** Test model relationships in tinker, verify `format_nok(15000)` returns "150,00 kr", verify order number generation.

---

### Task 3: Admin Dashboard & Metrics

**Goal:** Admin can see key metrics and navigate to management pages.

- [x] 3.1 Create admin dashboard controller
  - Calculate total outstanding (sum across all open orders)
  - Count open orders
  - Count active customers
  - Count items by status
  - Get top 10 customers by outstanding (desc)
  - _Requirements: 2.2, 2.3, 2.4_

- [x] 3.2 Build admin dashboard view
  - 4 summary cards (total outstanding, open orders, customer count, items by status)
  - Table of top customers with outstanding balance
  - Links to customer detail pages
  - Use design system components (cards, badges, tables)
  - _Requirements: 2.2, 2.3, 2.4_

- [x] 3.3 Create admin layout with sidebar
  - Sidebar navigation: Dashboard, Kunder, Varer, Ordrer
  - Collapsible on mobile
  - Active state highlighting
  - Profile dropdown in top right
  - _Requirements: Design system_

**Checkpoint:** Navigate to `/admin`, see metrics, click through sidebar links (even if pages don't exist yet). ✅ Complete

---

### Task 4: Customer Management (Admin)

**Goal:** Admin can create, view, edit, and delete customers.

- [x] 4.1 Create customer list page
  - Display all active customers (exclude soft-deleted)
  - Table: name, email, total outstanding, actions
  - "Ny kunde" button
  - Empty state: "Ingen kunder ennå"
  - _Requirements: 2.3_

- [x] 4.2 Create customer creation form
  - Fields: name, email
  - Generate secure random temp password (`Str::random(12)`)
  - Set `role = 'customer'`, `password_change_required = true`
  - Display generated password to admin (one-time, copy to clipboard)
  - Form Request validation
  - _Requirements: 2.1, 2.2, 10.1, 10.8_

- [x] 4.3 Create customer detail page
  - Customer info (name, email, status)
  - Badge if `password_change_required = true`: "Må endre passord"
  - "Kopier link" button (copies `/dashboard` URL to clipboard)
  - 3 summary cards: Total kjøpt, Total betalt, Utestående
  - Table of all customer's orders (order number, status, outstanding)
  - _Requirements: 2.4, 2.5_

- [x] 4.4 Create customer edit form
  - Fields: name, email
  - Cannot change role or password (separate flows)
  - Form Request validation
  - _Requirements: 2.2_

- [x] 4.5 Implement customer soft delete
  - Confirmation modal: "Er du sikker?"
  - Soft delete (set `deleted_at`)
  - Flash message: "Kunde slettet"
  - _Requirements: 2.5, 9.1_

- [x] 4.6 Create deleted customers page
  - List soft-deleted customers
  - Show outstanding balance
  - Actions: Restore, Permanent Delete
  - _Requirements: 2.6, 2.7, 9.3_

- [x] 4.7 Implement customer restore
  - Clear `deleted_at`
  - Flash message: "Kunde gjenopprettet"
  - _Requirements: 2.8_

- [x] 4.8 Implement customer permanent delete
  - Confirmation modal: "Dette kan ikke angres!"
  - Hard delete (only for soft-deleted)
  - Flash message: "Kunde permanent slettet"
  - _Requirements: 2.7, 9.4_

**Checkpoint:** Create a test customer, view detail, soft delete, restore, permanent delete. ✅ Complete

---

### Task 5: Inventory Management (Admin)

**Goal:** Admin can manage items with status tracking.

- [x] 5.1 Create item list page
  - Display all items (exclude soft-deleted)
  - Table: name, purchase price, target price, status badge, actions
  - "Ny vare" button
  - Filter by status (available, reserved, sold, archived)
  - Empty state: "Ingen varer ennå"
  - _Requirements: 3.2_

- [x] 5.2 Create item creation form
  - Fields: name, description, purchase_price, target_price
  - Default status: `available`
  - Form Request validation
  - _Requirements: 3.1, 10.1_

- [x] 5.3 Create item edit form
  - Fields: name, description, purchase_price, target_price, status
  - Dropdown for status (available, reserved, sold, archived)
  - Warning if changing from `sold` or `archived` to `available`
  - Form Request validation
  - _Requirements: 3.2, 3.6_

- [x] 5.4 Implement item soft delete
  - Confirmation modal
  - Soft delete (set `deleted_at`)
  - Flash message: "Vare slettet"
  - _Requirements: 9.1_

**Checkpoint:** Create test items, edit status, verify status badges display correctly. ✅ Complete

---

### Task 6: Order Management (Admin)

**Goal:** Admin can create orders, add items, track payments, and close/cancel orders.

- [x] 6.1 Create order list page
  - Display all orders
  - Table: order number, customer name, status badge, outstanding, actions
  - Filter by status (open, closed, cancelled)
  - "Ny ordre" button
  - Empty state: "Ingen ordrer ennå"
  - _Requirements: 4.2_

- [x] 6.2 Create order creation form
  - Select customer (dropdown)
  - Optional notes field
  - Auto-generate `order_number` on save
  - Default status: `open`
  - Form Request validation
  - _Requirements: 4.1, 4.2, 10.1_

- [x] 6.3 Create order detail page
  - Display order number prominently
  - Customer info with link to customer detail
  - Order lines table: item name, unit price, quantity, total
  - "Legg til vare" button
  - Payment history table: date, amount, method, note
  - "Registrer betaling" button
  - Summary: Total, Betalt, Utestående
  - Overpayment warning badge if outstanding < 0
  - Actions: Close order, Cancel order
  - _Requirements: 4.6, 4.7, 5.4, 5.5_

- [x] 6.4 Implement add order line
  - Modal/form: select item (only available items), quantity, unit price (pre-filled from item.target_price)
  - On save: create order_line, set item status to `reserved`, recalculate order.total_amount
  - Form Request validation
  - _Requirements: 3.3, 4.3, 4.4_

- [x] 6.5 Implement delete order line
  - Confirmation modal
  - Soft delete order_line
  - Revert item status to `available`
  - Recalculate order.total_amount
  - _Requirements: 4.10_

- [x] 6.6 Implement manual total_amount override
  - Editable field on order detail page
  - Show both auto-calculated and overridden amounts if different
  - Display: "Auto: X kr" and "Overstyrt: Y kr"
  - _Requirements: 4.5_

- [x] 6.7 Implement register payment
  - Modal/form: amount, paid_at (date), payment_method (optional), note (optional)
  - On save: create payment, recalculate outstanding
  - Form Request validation
  - _Requirements: 5.1, 5.2, 5.3_

- [x] 6.8 Implement delete payment
  - Confirmation modal
  - Soft delete payment
  - Recalculate outstanding
  - _Requirements: 4.11, 5.6_

- [x] 6.9 Implement close order
  - Confirmation modal: "Lukk ordre?"
  - Set order.status to `closed`
  - Set all items in order to `sold`
  - Flash message: "Ordre lukket"
  - _Requirements: 4.8, 3.4_

- [x] 6.10 Implement cancel order
  - Confirmation modal: "Kanseller ordre? Dette vil fjerne alle linjer og betalinger."
  - Set order.status to `cancelled`
  - Soft delete all order_lines
  - Soft delete all payments
  - Revert all items to `available`
  - Flash message: "Ordre kansellert"
  - _Requirements: 4.9, 3.5_

**Checkpoint:** Create order, add items, register payments, verify outstanding calculation, close order, verify items marked as sold. ✅ Complete

---

### Task 7: Customer Portal (Read-Only)

**Goal:** Customers can view their orders and balance.

- [x] 7.1 Create customer dashboard
  - Password change alert banner if `password_change_required = true`
  - 3 summary cards: Total kjøpt, Total betalt, Utestående (exclude cancelled orders)
  - Table of open orders: order number, status badge, outstanding, link to detail
  - Empty state: "Ingen åpne ordrer"
  - _Requirements: 6.1, 6.2, 6.3_

- [x] 7.2 Create customer orders list page
  - Table of all customer's orders (exclude cancelled)
  - Columns: order number, status badge, outstanding
  - Link to order detail
  - Empty state: "Ingen ordrer ennå"
  - _Requirements: 6.4_

- [x] 7.3 Create customer order detail page
  - Display order number
  - Items table: name, unit price, quantity, total
  - Payment history table: date, amount, method, note
  - Summary: Total, Betalt, Utestående
  - No edit/delete actions (read-only)
  - Apply OrderPolicy (403 if not owner)
  - _Requirements: 6.5, 6.6, 6.7_

- [x] 7.4 Update profile page for password change
  - Show alert if `password_change_required = true`
  - On password update, set `password_change_required = false`
  - _Requirements: 1.7_

- [x] 7.5 Implement customer account deletion
  - Confirmation modal with outstanding balance warning
  - Soft delete user account
  - Flash message: "Konto slettet"
  - _Requirements: 9.1_

**Checkpoint:** Log in as customer, view dashboard, click through to order detail, verify cannot access admin routes.

---

### Task 8: Polish & Validation

**Goal:** Comprehensive validation, error handling, and UI polish.

- [ ] 8.1 Create Form Request classes
  - `StoreCustomerRequest`
  - `UpdateCustomerRequest`
  - `StoreItemRequest`
  - `UpdateItemRequest`
  - `StoreOrderRequest`
  - `StoreOrderLineRequest`
  - `StorePaymentRequest`
  - All with Norwegian error messages
  - _Requirements: 10.1, 10.2_

- [ ] 8.2 Add empty states to all list views
  - Friendly message + relevant action button
  - Consistent styling across all pages
  - _Requirements: Design system_

- [ ] 8.3 Add confirmation modals to all delete actions
  - Use Alpine.js for modal toggle
  - Consistent styling
  - _Requirements: 10.9_

- [ ] 8.4 Add flash messages
  - Success (green), Error (red), Warning (yellow)
  - Auto-dismiss after 5 seconds
  - Slide-down animation
  - _Requirements: Design system_

- [ ] 8.5 Implement eager loading
  - Use `with()` on order queries to prevent N+1
  - Load `orderLines.item`, `payments`, `customer` where needed
  - _Requirements: 10.11_

- [ ] 8.6 Add CSRF protection verification
  - Ensure all forms have `@csrf`
  - _Requirements: 10.3_

- [ ] 8.7 Test responsive design
  - Test all pages on mobile, tablet, desktop
  - Fix any layout issues
  - Ensure modals work on mobile
  - _Requirements: 7.6_

- [ ] 8.8 Create DevDataSeeder
  - Seed 3-4 test customers
  - Seed 5-6 test items
  - Seed several orders in different states (open, closed, cancelled)
  - Seed payments on some orders
  - _Requirements: Testing_

**Checkpoint:** Run all seeders, test all CRUD operations, verify validation errors display correctly, test on mobile.

---

### Task 9: Feature Tests

**Goal:** Automated tests for critical functionality.

- [ ] 9.1 Write auth tests
  - Test login redirects (admin → `/admin`, customer → `/dashboard`)
  - Test customer cannot access `/admin/*` (403)
  - Test guest redirected to login on protected routes
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 9.2 Write policy tests
  - Test customer cannot view another customer's order (403)
  - Test admin can view all orders
  - _Requirements: 6.6, 10.4_

- [ ] 9.3 Write order calculation tests
  - Test outstanding balance calculation
  - Test overpayment detection
  - Test total_amount recalculation on order line add/delete
  - _Requirements: 4.3, 4.7, 5.4, 5.5_

- [ ] 9.4 Write soft delete tests
  - Test soft-deleted records excluded from queries
  - Test restore functionality
  - Test permanent delete
  - _Requirements: 9.1, 9.2, 9.3, 9.4_

**Checkpoint:** Run `php artisan test`, verify all tests pass.

---

### Task 10: Deployment Preparation

**Goal:** Documentation and configuration for production deployment.

- [ ] 10.1 Create DEPLOY.md
  - Step-by-step guide for Neon + Laravel Cloud
  - Environment variable setup
  - SSL/TLS configuration (`DB_SSLMODE=require`)
  - Migration commands
  - Seeding admin user in production
  - _Requirements: Deployment_

- [ ] 10.2 Update .env.example
  - Add all required variables
  - Add comments for production values
  - _Requirements: Deployment_

- [ ] 10.3 Create production checklist
  - Verify all routes protected
  - Verify all forms validated
  - Verify CSRF protection
  - Verify soft deletes working
  - Test admin and customer flows
  - _Requirements: Security checklist_

- [ ] 10.4 Update README.md
  - Project description
  - Local setup instructions
  - Seeder instructions
  - Test credentials
  - _Requirements: Documentation_

**Checkpoint:** Follow DEPLOY.md locally to verify instructions are complete and accurate.

---

## Notes

**Task Dependencies:**
- Task 2 must be complete before Task 3-7
- Task 3-7 can be worked on in parallel (mostly independent)
- Task 8 should be done after Task 3-7
- Task 9 can be done alongside Task 3-7
- Task 10 is final

**Testing Strategy:**
- Manual testing after each task checkpoint
- Feature tests for critical paths (Task 9)
- No unit tests for MVP (overkill)

**Estimated Timeline:**
- Task 1: 2-3 hours
- Task 2: 3-4 hours
- Task 3: 1-2 hours
- Task 4: 3-4 hours
- Task 5: 2-3 hours
- Task 6: 4-5 hours
- Task 7: 2-3 hours
- Task 8: 3-4 hours
- Task 9: 2-3 hours
- Task 10: 1-2 hours
- **Total: ~25-35 hours**

---

*End of Implementation Plan*
