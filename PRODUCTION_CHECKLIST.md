# BMPOS Production Checklist

Use this checklist before deploying to production and after each deployment.

---

## Pre-Deployment Checklist

### Environment Configuration

- [ ] `APP_ENV=production` set
- [ ] `APP_DEBUG=false` set
- [ ] `APP_URL` set to production domain
- [ ] `APP_KEY` generated and set
- [ ] `DB_SSLMODE=require` set for Neon
- [ ] All database credentials correct
- [ ] Mail configuration set (if using email)

### Security Verification

- [ ] All routes require authentication (except `/` and `/login`)
- [ ] Admin routes protected by `admin` middleware
- [ ] Customer routes protected by `auth` middleware
- [ ] CSRF protection active on all forms (`@csrf` present)
- [ ] OrderPolicy applied on order show routes
- [ ] Form Request validation on all store/update actions
- [ ] No mass assignment vulnerabilities (`$fillable` defined on all models)
- [ ] Soft deletes implemented on all models
- [ ] Password change enforcement working (`password_change_required`)

### Database Verification

- [ ] Migrations run successfully
- [ ] AdminUserSeeder run successfully
- [ ] Admin password changed from default
- [ ] Database backups configured in Neon
- [ ] All amounts stored as integer øre (no floats)
- [ ] All models use SoftDeletes trait

### Code Quality

- [ ] No debug statements (`dd()`, `dump()`, `var_dump()`)
- [ ] No commented-out code blocks
- [ ] All file headers and footers present
- [ ] Norwegian UI text, English code identifiers
- [ ] `format_nok()` helper used for all currency display

---

## Post-Deployment Checklist

### Smoke Tests

- [ ] Landing page loads (`/`)
- [ ] Login page loads (`/login`)
- [ ] Admin can log in
- [ ] Customer can log in
- [ ] Guest redirected to login on protected routes

### Admin Flow Tests

- [ ] Admin dashboard displays metrics
- [ ] Admin can create customer
- [ ] Temporary password generated and displayed
- [ ] Admin can view customer detail
- [ ] Admin can edit customer
- [ ] Admin can soft-delete customer
- [ ] Admin can restore customer
- [ ] Admin can permanently delete customer
- [ ] Admin can create item
- [ ] Admin can edit item (including status)
- [ ] Admin can soft-delete item
- [ ] Admin can create order
- [ ] Admin can add order lines
- [ ] Admin can delete order lines
- [ ] Admin can register payments
- [ ] Admin can delete payments
- [ ] Admin can manually override total
- [ ] Admin can close order
- [ ] Admin can cancel order
- [ ] Outstanding balance calculates correctly
- [ ] Overpayment warning displays when applicable

### Customer Flow Tests

- [ ] Customer dashboard displays
- [ ] Password change alert shows if required
- [ ] Summary cards display correct totals
- [ ] Open orders list displays
- [ ] Customer can view orders list
- [ ] Customer can view own order detail
- [ ] Customer CANNOT view other customer's orders (403)
- [ ] Customer CANNOT access admin routes (403)
- [ ] Customer can change password
- [ ] Password change clears `password_change_required` flag
- [ ] Customer can soft-delete own account
- [ ] Outstanding balance warning shows on account deletion

### Data Integrity Tests

- [ ] Order total recalculates when lines added/deleted
- [ ] Item status changes to `reserved` when added to order
- [ ] Item status changes to `sold` when order closed
- [ ] Item status changes to `available` when order cancelled
- [ ] Payments sum correctly for `paid_amount`
- [ ] Outstanding = total - paid
- [ ] Soft-deleted records excluded from queries
- [ ] Soft-deleted records can be restored
- [ ] Order cancellation soft-deletes lines and payments
- [ ] Cancelled orders excluded from customer totals

### UI/UX Tests

- [ ] All pages responsive (mobile, tablet, desktop)
- [ ] Flash messages display correctly
- [ ] Confirmation modals work
- [ ] Empty states display when no data
- [ ] Status badges display with correct colors
- [ ] Currency formatted correctly (format_nok)
- [ ] Forms show validation errors in Norwegian
- [ ] Navigation works (sidebar, links)
- [ ] Logout works

### Performance Tests

- [ ] Dashboard loads in < 2 seconds
- [ ] Order list loads in < 2 seconds
- [ ] Customer list loads in < 2 seconds
- [ ] No N+1 query issues (check logs)
- [ ] Eager loading working on order queries

---

## Security Audit

### Authentication

- [ ] No public registration route
- [ ] Login redirects work (admin → `/admin`, customer → `/dashboard`)
- [ ] Session timeout configured
- [ ] Password hashing working (bcrypt)
- [ ] Remember me token working

### Authorization

- [ ] Admin middleware blocks customers from `/admin/*`
- [ ] OrderPolicy prevents cross-customer access
- [ ] Policies registered in AuthServiceProvider
- [ ] All queries scoped to `auth()->user()` where applicable

### Input Validation

- [ ] All forms use Form Request classes
- [ ] Email validation working
- [ ] Phone validation working (optional field)
- [ ] At least one contact method required (email or phone)
- [ ] Price validation (integer, min 0)
- [ ] Quantity validation (integer, min 1)
- [ ] Status validation (enum values)

### Data Protection

- [ ] Passwords never displayed after creation
- [ ] Sensitive data not logged
- [ ] Database credentials not in repository
- [ ] `.env` file not committed
- [ ] SSL/TLS enabled (Neon)
- [ ] HTTPS enforced on production domain

---

## Monitoring Setup

- [ ] Laravel Cloud error tracking enabled
- [ ] Application logs accessible
- [ ] Database monitoring enabled in Neon
- [ ] Backup retention configured (7-30 days)
- [ ] Uptime monitoring configured (optional)

---

## Documentation

- [ ] README.md updated
- [ ] DEPLOY.md accurate
- [ ] .env.example complete
- [ ] Admin credentials documented (securely)
- [ ] Test credentials documented (if applicable)

---

## Rollback Plan

- [ ] Previous deployment identified
- [ ] Rollback procedure tested
- [ ] Database backup before deployment
- [ ] Rollback contact person identified

---

## Sign-Off

**Deployment Date**: _______________

**Deployed By**: _______________

**Verified By**: _______________

**Issues Found**: _______________

**Status**: ⬜ Approved ⬜ Issues Found ⬜ Rolled Back

---

*Checklist Version: 1.0*
*Last Updated: 2026-02-02*
