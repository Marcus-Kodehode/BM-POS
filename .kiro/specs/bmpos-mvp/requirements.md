# Requirements Document — BMPOS MVP

## Introduction

BMPOS (Business Management & Payment Oversight System) is a private sales, inventory, and payment-tracking application. The system enables an admin to manage inventory, create customer accounts, track orders with multiple items, record partial payments, and provide customers with a secure read-only portal to view their purchase history and outstanding balance.

## Glossary

- **System**: The BMPOS web application
- **Admin**: User with role `admin` who manages all aspects of the system
- **Customer**: User with role `customer` who can view their own orders and balance
- **Item**: A physical product in inventory (unique, typically used goods)
- **Order**: A sales agreement linking one or more items to a customer
- **Order_Line**: A single item within an order (with quantity and price)
- **Payment**: A partial or full payment (avdrag) recorded against an order
- **Outstanding**: The remaining balance on an order (total - paid)
- **Øre**: Norwegian currency subunit (100 øre = 1 kr)

---

## Requirements

### Requirement 1: User Authentication & Authorization

**User Story:** As a system user, I want secure authentication and role-based access control, so that admin and customer data remain protected.

#### Acceptance Criteria

1. WHEN a user attempts to access protected routes without authentication, THEN THE System SHALL redirect them to the login page
2. WHEN a user logs in with valid credentials, THEN THE System SHALL authenticate them and redirect based on their role (admin → `/admin`, customer → `/dashboard`)
3. WHEN a customer attempts to access admin routes, THEN THE System SHALL return a 403 Forbidden response
4. THE System SHALL NOT provide a public registration route
5. WHEN a customer is created by admin with a temporary password, THEN THE System SHALL set `password_change_required = true`
6. WHEN a customer with `password_change_required = true` logs in, THEN THE System SHALL display a prominent alert requiring password change
7. WHEN a customer updates their password, THEN THE System SHALL set `password_change_required = false`

### Requirement 2: Customer Management (Admin)

**User Story:** As an admin, I want to create and manage customer accounts, so that I can track who owes what and provide them with portal access.

#### Acceptance Criteria

1. WHEN admin creates a new customer, THEN THE System SHALL generate a secure random temporary password
2. WHEN admin creates a new customer, THEN THE System SHALL set `role = 'customer'` and `password_change_required = true`
3. WHEN admin views the customer list, THEN THE System SHALL display all active (non-deleted) customers
4. WHEN admin views a customer detail page, THEN THE System SHALL display customer info, summary cards (total purchased, total paid, outstanding), and a list of all orders
5. WHEN admin soft-deletes a customer, THEN THE System SHALL set `deleted_at` timestamp and hide the customer from the main list
6. WHEN admin views deleted customers, THEN THE System SHALL display soft-deleted customers with their outstanding balances
7. WHEN admin permanently deletes a customer, THEN THE System SHALL hard-delete the customer record (only available for soft-deleted customers)
8. WHEN admin restores a soft-deleted customer, THEN THE System SHALL clear `deleted_at` and return the customer to active status

### Requirement 3: Inventory Management (Admin)

**User Story:** As an admin, I want to manage my inventory of items, so that I can track what I own and what is available for sale.

#### Acceptance Criteria

1. WHEN admin creates an item, THEN THE System SHALL set status to `available` by default
2. WHEN admin views the item list, THEN THE System SHALL display all items with status badges (available, reserved, sold, archived)
3. WHEN an item is added to an order line, THEN THE System SHALL automatically change the item status to `reserved`
4. WHEN an order is closed, THEN THE System SHALL automatically change all reserved items in that order to `sold`
5. WHEN an order is cancelled, THEN THE System SHALL automatically revert all reserved items in that order to `available`
6. WHEN admin manually sets an item to `archived`, THEN THE System SHALL prevent that item from being added to new orders
7. WHEN admin attempts to add a `sold` or `archived` item to an order, THEN THE System SHALL reject the action with a validation error

### Requirement 4: Order Management (Admin)

**User Story:** As an admin, I want to create and manage orders with multiple items, so that I can track sales agreements with customers.

#### Acceptance Criteria

1. WHEN admin creates an order, THEN THE System SHALL auto-generate a unique `order_number` (e.g., "2025-001")
2. WHEN admin creates an order, THEN THE System SHALL set status to `open` by default
3. WHEN admin adds an order line, THEN THE System SHALL calculate `total_amount` as the sum of `(unit_price × quantity)` for all order lines
4. WHEN admin adds an order line with quantity > 1, THEN THE System SHALL accept and store the quantity
5. WHEN admin manually overrides `total_amount`, THEN THE System SHALL display both the auto-calculated and overridden amounts in the UI
6. WHEN admin views an order detail page, THEN THE System SHALL display order number, customer info, order lines, payment history, and outstanding balance
7. WHEN outstanding balance is negative (overpayment), THEN THE System SHALL display a warning badge "Overbetalt: X kr"
8. WHEN admin closes an order, THEN THE System SHALL set status to `closed` and change all item statuses to `sold`
9. WHEN admin cancels an order, THEN THE System SHALL set status to `cancelled`, soft-delete all order lines and payments, and revert item statuses to `available`
10. WHEN admin deletes an order line, THEN THE System SHALL soft-delete the line, recalculate `total_amount`, and update item status
11. WHEN admin deletes a payment, THEN THE System SHALL soft-delete the payment and recalculate outstanding balance

### Requirement 5: Payment Tracking (Admin)

**User Story:** As an admin, I want to record partial payments against orders, so that I can track what customers have paid and what they still owe.

#### Acceptance Criteria

1. WHEN admin registers a payment, THEN THE System SHALL store the amount in øre (integer)
2. WHEN admin registers a payment, THEN THE System SHALL optionally store a payment method (e.g., "kontant", "bank", "vipps")
3. WHEN admin registers a payment, THEN THE System SHALL require a `paid_at` date
4. WHEN admin views an order, THEN THE System SHALL calculate and display `paid_amount` as the sum of all payments
5. WHEN admin views an order, THEN THE System SHALL calculate and display `outstanding` as `total_amount - paid_amount`
6. WHEN admin deletes a payment, THEN THE System SHALL soft-delete the payment and recalculate outstanding balance

### Requirement 6: Customer Portal (Read-Only)

**User Story:** As a customer, I want to view my orders and outstanding balance, so that I can track my purchases and payments.

#### Acceptance Criteria

1. WHEN a customer logs in, THEN THE System SHALL redirect them to `/dashboard`
2. WHEN a customer views their dashboard, THEN THE System SHALL display three summary cards: Total kjøpt, Total betalt, Utestående (excluding cancelled orders)
3. WHEN a customer views their dashboard, THEN THE System SHALL display a list of open orders with order numbers
4. WHEN a customer views the orders list, THEN THE System SHALL display all their orders excluding cancelled orders
5. WHEN a customer views an order detail, THEN THE System SHALL display order number, items with quantity, payment history with method, and totals
6. WHEN a customer attempts to view another customer's order, THEN THE System SHALL return a 403 Forbidden response
7. WHEN a customer attempts to modify any data, THEN THE System SHALL prevent the action (read-only access)

### Requirement 7: Landing Page (Public)

**User Story:** As a visitor, I want to see a professional landing page, so that I understand what the portal offers and can log in.

#### Acceptance Criteria

1. WHEN a visitor navigates to `/`, THEN THE System SHALL display a modern, professional landing page
2. WHEN a visitor views the landing page, THEN THE System SHALL display a clear headline explaining the portal's purpose
3. WHEN a visitor views the landing page, THEN THE System SHALL display a primary "Logg inn" CTA button
4. WHEN a visitor views the landing page, THEN THE System SHALL display a brief explanation of customer benefits (order overview, balance, payment history)
5. THE System SHALL use a personal, warm, and trustworthy design tone on the landing page
6. THE System SHALL ensure the landing page is fully responsive across all devices

### Requirement 8: Currency & Formatting

**User Story:** As a system user, I want all monetary amounts displayed consistently in Norwegian kroner, so that I can easily understand financial information.

#### Acceptance Criteria

1. THE System SHALL store all monetary amounts as integer øre (e.g., 15000 = 150,00 kr)
2. THE System SHALL provide a global `format_nok()` helper function
3. WHEN displaying any monetary amount, THEN THE System SHALL use the `format_nok()` helper to format as "X,XX kr"
4. THE System SHALL never use floating-point numbers for currency calculations

### Requirement 9: Data Integrity & Audit Trail

**User Story:** As an admin, I want deleted data to be preserved for audit purposes, so that I can review historical information if needed.

#### Acceptance Criteria

1. WHEN a user, item, order, order line, or payment is deleted, THEN THE System SHALL soft-delete the record (set `deleted_at` timestamp)
2. WHEN querying active records, THEN THE System SHALL exclude soft-deleted records by default
3. WHEN admin views deleted customers, THEN THE System SHALL display soft-deleted customers with their outstanding balances
4. WHEN admin permanently deletes a customer, THEN THE System SHALL hard-delete the record (only available for soft-deleted customers)

### Requirement 10: Security & Validation

**User Story:** As a system administrator, I want all user inputs validated and all routes protected, so that the system remains secure.

#### Acceptance Criteria

1. WHEN a user submits a form, THEN THE System SHALL validate all inputs via Form Request classes
2. WHEN validation fails, THEN THE System SHALL display error messages in Norwegian
3. THE System SHALL protect all forms with CSRF tokens
4. THE System SHALL scope all customer queries to `auth()->user()` to prevent data leakage
5. THE System SHALL apply `OrderPolicy` to all order show/edit routes
6. THE System SHALL protect all `/admin/*` routes with `EnsureUserIsAdmin` middleware
7. THE System SHALL hash all passwords using bcrypt
8. THE System SHALL generate temporary passwords using secure random strings (minimum 12 characters)

---

*End of Requirements Document*
