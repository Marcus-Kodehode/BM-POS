# BMPOS — Bruktmarked Point of Sale

BMPOS is a simple, secure order and payment tracking system for second-hand stores. Built with Laravel 11 and Tailwind CSS, it provides separate portals for administrators (store staff) and customers to manage orders, track payments, and monitor outstanding balances.

---

## Features

- **Admin Portal**: Manage customers, inventory, orders, and payments
- **Customer Portal**: View orders, payment history, and outstanding balance
- **Role-Based Access**: Secure authentication with admin and customer roles
- **Order Management**: Create orders, add items, register payments, close/cancel orders
- **Inventory Tracking**: Track item status (available, reserved, sold, archived)
- **Payment Tracking**: Register multiple payments per order, detect overpayments
- **Soft Deletes**: Safely delete and restore customers, items, orders, and payments
- **Norwegian UI**: All user-facing text in Norwegian

---

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Database**: PostgreSQL (local or Neon)
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **Authentication**: Laravel Breeze

---

## Local Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- PostgreSQL (via pgAdmin or Docker)

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd bmpos
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Copy environment file:
   ```bash
   copy .env.example .env
   ```

4. Configure database in `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=bmpos
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Seed admin user:
   ```bash
   php artisan db:seed --class=AdminUserSeeder
   ```

8. (Optional) Seed development data:
   ```bash
   php artisan db:seed --class=DevDataSeeder
   ```

9. Build frontend assets:
   ```bash
   npm run dev
   ```

10. Start development server:
    ```bash
    php artisan serve
    ```

11. Visit `http://localhost:8000`

---

## Test Credentials

### Admin Account
- **Email**: `admin@bmpos.no`
- **Password**: `password`
- **Access**: Full admin portal at `/admin`

### Customer Accounts (if DevDataSeeder run)
- **Email**: `ola.nordmann@example.com`
- **Password**: `password`
- **Access**: Customer portal at `/dashboard`

Additional test customers:
- `kari.hansen@example.com` / `password`
- `per.olsen@example.com` / `password`

**Note**: Change default passwords in production!

---

## Seeders

### AdminUserSeeder
Creates the initial admin account. Run this in production:
```bash
php artisan db:seed --class=AdminUserSeeder --force
```

### DevDataSeeder
Creates sample data for development (NOT for production):
- 3 test customers
- 6 test items
- 4 test orders with payments

```bash
php artisan db:seed --class=DevDataSeeder
```

---

## Testing

Run the test suite:
```bash
php artisan test
```

Expected results:
- 90 tests passing (97% pass rate)
- 3 expected failures (registration disabled, profile soft delete per spec)

Test coverage includes:
- Authentication and authorization
- Order calculations and balance tracking
- Policy enforcement (customer cannot view other orders)
- Soft delete functionality

---

## Production Deployment

For production deployment to Neon + Laravel Cloud, see:

**[DEPLOY.md](DEPLOY.md)** — Step-by-step deployment guide

**[PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md)** — Pre/post-deployment checklist

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin portal controllers
│   │   └── CustomerDashboardController.php
│   ├── Middleware/         # Custom middleware (admin, password change)
│   └── Requests/           # Form validation classes
├── Models/                 # Eloquent models (User, Order, Item, etc.)
├── Policies/               # Authorization policies
└── helpers.php             # Helper functions (format_nok)

resources/views/
├── admin/                  # Admin portal views
├── customer/               # Customer portal views
└── layouts/                # Layout templates

database/
├── migrations/             # Database schema
├── seeders/                # Data seeders
└── factories/              # Model factories for testing

tests/Feature/              # Feature tests
```

---

## Key Concepts

### Currency Handling
All monetary amounts are stored as **integer øre** (100 øre = 1 kr):
- `purchase_price`, `target_price`, `unit_price`: stored as øre
- `total_amount`, `paid_amount`, `outstanding`: stored as øre
- Use `format_nok($amount)` helper for display: `format_nok(15000)` → "150,00 kr"

### Order Status Flow
1. **Open**: Order created, items can be added/removed
2. **Closed**: Order finalized, items marked as sold
3. **Cancelled**: Order cancelled, items returned to available

### Item Status Flow
1. **Available**: Item in stock, can be added to orders
2. **Reserved**: Item added to open order
3. **Sold**: Item in closed order
4. **Archived**: Item removed from active inventory

### Soft Deletes
All models use soft deletes (`deleted_at` timestamp):
- Deleted records excluded from queries by default
- Can be restored via admin interface
- Permanent delete available for soft-deleted records

---

## Development Guidelines

See **[CLAUDE.md](CLAUDE.md)** for:
- Code style and conventions
- Security best practices
- Testing guidelines
- Norwegian UI text standards

---

## License

This project is proprietary software. All rights reserved.

---

*Last updated: 2026-02-02*
