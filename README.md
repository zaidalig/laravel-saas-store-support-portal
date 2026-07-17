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

## SaaS Order Flow

Orders move through product selection → line items → totals/tax → fulfillment status → payments → invoice.

1. **Customer order** — Authenticated customers place an order from `/order` (or `/order/{product}`). The app creates an `Order` with `status=pending` and `payment_status=unpaid`, one `OrderItem` (product snapshot, qty, unit price, line total), and an auto-generated `Invoice` (`status=unpaid`, due in 14 days).
2. **Admin order** — Staff/admins can create orders in `/admin/orders` the same way (customer fields + product + qty). Admin-created orders do not auto-create an invoice; generate one later from `/admin/invoices` for orders that lack one.
3. **Line items & totals** — `subtotal = unit_price × quantity`. Tax uses `setting('tax_percentage')` (default 8%): `tax = round(subtotal × tax%/100, 2)`. `total = subtotal + tax` (discount supported on the model; create flows currently set `discount=0`).
4. **Order statuses** — `pending` → `confirmed` → `processing` → `completed`, or `cancelled`. Payment status is separate: `unpaid` / `partial` / `paid` / `refunded`.
5. **Payments** — Admins record payments against an order (`cash`, `bank_transfer`, `card`, `other`). Completed payment amounts are summed; order `payment_status` becomes `paid` when sum ≥ total, else `partial` or `unpaid`.
6. **Invoices** — Customer checkout creates an invoice immediately. Admins can generate invoices for remaining orders and update invoice status (`unpaid` / `partial` / `paid` / `cancelled`). Print view is available from the admin invoice list.

## Branching & promote

```
feature/*  →  develop (dev)  →  qa  →  main (production)
```

1. Open a PR from `feature/<name>` into `develop`.
2. After QA sign-off on `develop`, run **Actions → Promote** (`develop` → `qa`).
3. After QA environment verification, run **Promote** (`qa` → `main`).
4. CI (PHPUnit) must pass on every PR to `develop`, `qa`, and `main`.
