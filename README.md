# laravel-saas-store-support-portal

A Laravel SQLite SaaS/Product Store and Support Portal with public website, customer dashboard, admin panel, order management, invoices, payments, support tickets, teams, users, and activity logs.

## Features

- Public product/service website with pricing, contact, support, and order tracking pages.
- Customer registration, login, dashboard, profile, orders, invoices, payments, and support tickets.
- Admin panel under `/admin` for users, teams, team members, categories, products, pricing plans, orders, payments, invoices, support tickets, contact messages, activity logs, and settings.
- SQLite migrations and seeded demo data.
- Bootstrap 5 and Font Awesome Blade UI.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

Set this in `.env`:

```env
DB_CONNECTION=sqlite
```

Run the app:

```bash
php artisan migrate --seed
php artisan serve
```

## Admin Login

```text
Email: admin@example.com
Password: password
```

## Useful Checks

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan route:list
```
## Branching & promote

```
feature/*  →  develop (dev)  →  qa  →  main (production)
```

1. Open a PR from `feature/<name>` into `develop`.
2. After QA sign-off on `develop`, run **Actions → Promote** (`develop` → `qa`).
3. After QA environment verification, run **Promote** (`qa` → `main`).
4. CI (PHPUnit) must pass on every PR to `develop`, `qa`, and `main`.
