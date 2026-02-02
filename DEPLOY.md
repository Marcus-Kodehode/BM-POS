# BMPOS Deployment Guide

This guide covers deploying BMPOS to production using **Neon** (PostgreSQL) and **Laravel Cloud**.

---

## Prerequisites

- GitHub account with repository access
- Laravel Cloud account
- Neon account (free tier available)
- Local development environment working

---

## Step 1: Prepare Neon Database

### 1.1 Create Neon Project

1. Go to [https://neon.tech](https://neon.tech)
2. Sign in or create account
3. Click **"New Project"**
4. Configure:
   - **Name**: `bmpos-production`
   - **Region**: Choose closest to your users
   - **PostgreSQL version**: 16 (or latest)
5. Click **"Create Project"**

### 1.2 Get Database Credentials

After project creation, find your connection details:

**Option 1: Dashboard**
1. In your Neon project, click **"Dashboard"** in the left sidebar
2. Look for **"Connection Details"** or **"Get connected to your new database"** section
3. Click **"Connection string"** to see all credentials

**Option 2: Connection Details Page**
1. In the left sidebar, click **"Connection Details"** or **"Quickstart"**
2. You'll see:
   - **Host** (endpoint): `ep-xxx-xxx.region.aws.neon.tech`
   - **Database**: `neondb` (default)
   - **User**: Your username
   - **Password**: Click "Show" to reveal (or "Reset password" if you lost it)
   - **Port**: `5432` (always)

**Option 3: Connection String**
Neon provides a full connection string like:
```
postgresql://username:password@ep-xxx-xxx.eu-central-1.aws.neon.tech/neondb?sslmode=require
```

Extract the parts:
- **Host**: `ep-xxx-xxx.eu-central-1.aws.neon.tech`
- **Database**: `neondb`
- **User**: `username` (before the colon)
- **Password**: `password` (between colon and @)
- **Port**: `5432`

**If you can't find the password:**
1. Go to **"Settings"** → **"Roles"** in the left sidebar
2. Click on your username
3. Select **"Reset password"**
4. Copy the new password (shown only once!)

**Important**: Copy these credentials - you'll need them for Laravel Cloud.

### 1.3 Enable SSL/TLS

Neon requires SSL connections. This is handled automatically by setting `DB_SSLMODE=require` in your environment variables.

---

## Step 2: Prepare Laravel Cloud

### 2.1 Connect GitHub Repository

1. Go to [Laravel Cloud Dashboard](https://cloud.laravel.com)
2. Click **"New Project"**
3. Select your GitHub repository
4. Choose branch: `main` (or your production branch)

### 2.2 Configure Environment Variables

In Laravel Cloud project settings, add these environment variables:

```env
# Application
APP_NAME=BMPOS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (Neon)
DB_CONNECTION=pgsql
DB_HOST=<your-neon-host>.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=<your-neon-username>
DB_PASSWORD=<your-neon-password>
DB_SSLMODE=require

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database

# Mail (configure if needed)
MAIL_MAILER=log
```

**Critical**: Set `DB_SSLMODE=require` - Neon requires encrypted connections.

### 2.3 Generate Application Key

Laravel Cloud will generate `APP_KEY` automatically on first deployment. If you need to generate manually:

```bash
php artisan key:generate --show
```

Then add it to environment variables.

---

## Step 3: Initial Deployment

### 3.1 Deploy Application

1. In Laravel Cloud dashboard, click **"Deploy"**
2. Wait for build to complete (2-5 minutes)
3. Check deployment logs for errors

### 3.2 Run Migrations

After successful deployment, run migrations via Laravel Cloud CLI or dashboard:

```bash
php artisan migrate --force
```

The `--force` flag is required in production.

### 3.3 Seed Admin User

Create the initial admin account:

```bash
php artisan db:seed --class=AdminUserSeeder --force
```

**Default admin credentials:**
- Email: `admin@bmpos.no`
- Password: `password`

**⚠️ IMPORTANT**: Change this password immediately after first login!

---

## Step 4: Post-Deployment Configuration

### 4.1 Configure Custom Domain (Optional)

1. In Laravel Cloud, go to **Domains**
2. Add your custom domain
3. Update DNS records as instructed
4. Update `APP_URL` environment variable

### 4.2 Test Application

1. Visit your application URL
2. Test landing page loads
3. Log in as admin
4. Verify dashboard displays
5. Create a test customer
6. Create a test order

### 4.3 Change Admin Password

1. Log in as admin
2. Go to Profile
3. Change password to something secure
4. Log out and log back in to verify

---

## Step 5: Optional - Seed Development Data

If you want sample data for testing (NOT recommended for production):

```bash
php artisan db:seed --class=DevDataSeeder --force
```

This creates:
- 3 test customers
- 6 test items
- 4 test orders with payments

---

## Environment Variables Reference

### Required Variables

| Variable | Description | Example |
|---|---|---|
| `APP_NAME` | Application name | `BMPOS` |
| `APP_ENV` | Environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_URL` | Application URL | `https://bmpos.example.com` |
| `DB_CONNECTION` | Database driver | `pgsql` |
| `DB_HOST` | Neon host | `ep-xxx.neon.tech` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `neondb` |
| `DB_USERNAME` | Database user | From Neon |
| `DB_PASSWORD` | Database password | From Neon |
| `DB_SSLMODE` | SSL mode | `require` |

### Optional Variables

| Variable | Description | Default |
|---|---|---|
| `SESSION_DRIVER` | Session storage | `database` |
| `CACHE_STORE` | Cache storage | `database` |
| `MAIL_MAILER` | Mail driver | `log` |

---

## Troubleshooting

### Database Connection Fails

**Error**: `SQLSTATE[08006] could not connect to server`

**Solution**: 
1. Verify `DB_SSLMODE=require` is set
2. Check Neon credentials are correct
3. Ensure Neon project is active (not suspended)

### Migrations Fail

**Error**: `Nothing to migrate` or migration errors

**Solution**:
1. Check database is empty (fresh install)
2. Run `php artisan migrate:fresh --force` (⚠️ destroys all data)
3. Verify database user has CREATE permissions

### Admin Seeder Fails

**Error**: `Duplicate entry` or `already exists`

**Solution**:
Admin user already exists. Either:
1. Use existing admin credentials
2. Reset password via database if forgotten
3. Run `php artisan migrate:fresh --force` and re-seed (⚠️ destroys all data)

### SSL/TLS Errors

**Error**: `SSL connection required`

**Solution**:
Ensure `DB_SSLMODE=require` is set in environment variables. Laravel's PostgreSQL driver handles SSL automatically when this is set.

---

## Security Checklist

Before going live, verify:

- [ ] `APP_DEBUG=false` in production
- [ ] `DB_SSLMODE=require` is set
- [ ] Admin password changed from default
- [ ] Custom domain configured with HTTPS
- [ ] Environment variables secured (not in repository)
- [ ] Database backups configured in Neon
- [ ] Test customer and admin flows work
- [ ] All routes require authentication
- [ ] CSRF protection active on all forms

---

## Maintenance

### Database Backups

Neon provides automatic backups. Configure retention in Neon dashboard:
1. Go to project settings
2. Configure backup retention (7-30 days recommended)

### Monitoring

Laravel Cloud provides:
- Application logs
- Error tracking
- Performance metrics

Access via Laravel Cloud dashboard.

### Updates

To deploy updates:
1. Push changes to GitHub
2. Laravel Cloud auto-deploys (if enabled)
3. Or manually trigger deployment in dashboard

---

## Rollback Procedure

If deployment fails:

1. In Laravel Cloud, go to **Deployments**
2. Find last working deployment
3. Click **"Redeploy"**
4. Verify application works

---

## Support

- **Laravel Cloud**: [https://cloud.laravel.com/support](https://cloud.laravel.com/support)
- **Neon**: [https://neon.tech/docs](https://neon.tech/docs)
- **Laravel Docs**: [https://laravel.com/docs](https://laravel.com/docs)

---

*Last updated: 2026-02-02*
