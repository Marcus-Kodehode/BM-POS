# Task [N] Summary — [Short Descriptive Title]

**Date:** YYYY-MM-DD  
**Status:** ✅ Complete / 🚧 In Progress / ⏸️ Paused  
**Related:** Task [N-1], Task [N+1] (if applicable)

---

## What Was Done

Brief 2-3 sentence overview of what this task accomplished.

**Key Changes:**
- Added/Modified [feature/component]
- Implemented [functionality]
- Fixed [issue]

---

## Files Changed

**Created:**
- `path/to/new/file.php` — Purpose
- `path/to/another/file.blade.php` — Purpose

**Modified:**
- `path/to/existing/file.php` — What changed
- `config/app.php` — Added configuration

**Deleted:**
- `path/to/old/file.php` — Why removed

---

## How to Test

**Quick Test (2 min):**
```bash
# Commands to run
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

**What to Expect:**
1. Navigate to `/admin` → Should see admin dashboard
2. Try to access as customer → Should get 403
3. Check database → `users` table has `role` column

**Test Credentials (if applicable):**
- Admin: `admin@example.com` / `password`
- Customer: `customer@example.com` / `password`

---

## Security Notes

**What Protects This:**
- ✅ Middleware: `EnsureUserIsAdmin` on all `/admin/*` routes
- ✅ Policy: `OrderPolicy` checks ownership
- ✅ Validation: `StoreOrderRequest` validates all inputs
- ✅ Scoping: Queries use `auth()->user()->orders()`

**Verified Checklist:**
- [ ] No data leakage between users
- [ ] All forms have CSRF protection
- [ ] All inputs validated via Form Requests
- [ ] Policies applied on show/edit routes

---

## Notes for Next Task

**Dependencies:**
- This task must be complete before starting Task [N+1]
- Database must be migrated before testing

**Known Issues:**
- None / [Describe any temporary workarounds]

**TODOs (if any):**
- [ ] Add more comprehensive tests (optional)
- [ ] Improve error messages (nice-to-have)

---

## Quick Reference

**New Routes:**
- `GET /admin` → Admin dashboard
- `POST /admin/customers` → Create customer

**New Models:**
- `Order` — Handles order logic and calculations
- `OrderLine` — Links items to orders

**New Helpers:**
- `format_nok($amount)` — Formats øre to "X,XX kr"

---

*Template Version: 1.0*
