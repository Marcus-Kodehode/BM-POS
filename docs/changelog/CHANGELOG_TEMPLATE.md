# Changelog

All notable changes to BMPOS will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Feature descriptions for upcoming changes

### Changed
- Improvements to existing features

### Fixed
- Bug fixes

---

## [1.0.0] - YYYY-MM-DD

### Added
- **Auth System** — Admin and customer roles with secure login
- **Customer Management** — Admin can create and manage customer accounts
- **Inventory System** — Track items with status (available, reserved, sold, archived)
- **Order Management** — Create orders, add items, track payments
- **Payment Tracking** — Record partial payments (avdrag) against orders
- **Outstanding Balance** — Automatic calculation per order and per customer
- **Customer Portal** — Read-only view for customers to see their orders and balance
- **Landing Page** — Modern, professional entry point with login CTA

### Security
- ✅ Role-based access control (admin/customer)
- ✅ Order policies prevent data leakage
- ✅ All routes protected with middleware
- ✅ Form Request validation on all inputs
- ✅ CSRF protection on all forms
- ✅ Passwords hashed with bcrypt

### Technical
- Laravel 11 with Blade templates
- PostgreSQL database (local + Neon prod)
- Laravel Breeze for authentication
- Integer øre for all currency values
- Soft deletes on critical tables

---

## Template for Future Versions

## [X.Y.Z] - YYYY-MM-DD

### Added
- **[Feature Name]** — Brief description of what it does
  - Sub-feature or detail
  - Another detail
- **[Another Feature]** — Description

### Changed
- **[Feature Name]** — What changed and why
  - Before: Old behavior
  - After: New behavior
- **UI Improvements** — List of visual/UX changes

### Fixed
- **[Bug Description]** — What was broken and how it's fixed
- **[Another Bug]** — Description

### Deprecated
- **[Feature Name]** — Will be removed in version X.Y.Z
  - Migration path: How to adapt

### Removed
- **[Feature Name]** — Why it was removed
  - Alternative: What to use instead

### Security
- **[Security Fix]** — Description without revealing exploit details
- **[Security Improvement]** — Proactive security enhancement

### Performance
- **[Optimization]** — What was improved and expected impact
  - Before: X seconds
  - After: Y seconds

### Database
- **Migration:** `YYYY_MM_DD_description` — What changed in schema
- **Seeder:** New/updated seeders

### Breaking Changes
- ⚠️ **[Change Description]** — What breaks and how to fix
  - Old way: `code example`
  - New way: `code example`
  - Migration guide: [Link or steps]

---

## Version Guidelines

**Version Format:** MAJOR.MINOR.PATCH

- **MAJOR** — Breaking changes, major features, architecture changes
- **MINOR** — New features, non-breaking improvements
- **PATCH** — Bug fixes, small improvements, security patches

**Examples:**
- `1.0.0` → `1.0.1` — Bug fix (patch)
- `1.0.1` → `1.1.0` — New feature (minor)
- `1.1.0` → `2.0.0` — Breaking change (major)

---

## Categories Explained

**Added** — New features, pages, functionality  
**Changed** — Modifications to existing features  
**Fixed** — Bug fixes  
**Deprecated** — Features marked for removal  
**Removed** — Features that were removed  
**Security** — Security-related changes  
**Performance** — Speed/efficiency improvements  
**Database** — Schema changes, migrations  
**Breaking Changes** — Changes that require code updates

---

## Writing Good Changelog Entries

✅ **Good:**
- "Added order cancellation with automatic item status revert"
- "Fixed outstanding balance calculation when payments exceed total"
- "Changed landing page design to be more personal and warm"

❌ **Bad:**
- "Updated stuff" (too vague)
- "Fixed bug" (which bug?)
- "Changed code" (what changed?)

**Tips:**
- Write for users, not developers (unless it's a technical change)
- Be specific but concise
- Include "why" if it's not obvious
- Link to issues/PRs if relevant
- Group related changes together

---

*Template Version: 1.0*
