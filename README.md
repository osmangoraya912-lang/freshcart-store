# FreshCart Store — Full-Stack Online Store (PHP + MySQL)

A complete, real online store built with **plain PHP 8, MySQL, Bootstrap 5, HTML, CSS and JavaScript** — no framework, no React, no Node.js. Everything a small store needs, ready to install and sell.

## Features

**Storefront (customers)**
- Homepage with hero, categories and featured products
- Shop page: search, category filter, sort by price / newest / name
- Product detail page with related products and stock info
- Session cart: add, update quantity, remove, clear
- Checkout with Cash on Delivery — order number, stock is reduced automatically
- Order success page + order tracking by order number (with phone check)
- Customer accounts: sign up, log in, view your orders

**Admin panel (`/admin`)**
- Dashboard: revenue, orders, products, customers, low-stock alerts, recent orders
- Products: add / edit / delete with image upload (JPG/PNG/WebP, max 2 MB)
- Categories: add / delete (blocked while products use it)
- Orders: view items, change status (Pending → Confirmed → Shipped → Delivered / Cancelled)

**Security**
- PDO prepared statements everywhere (no SQL injection)
- `password_hash` for all passwords, session regeneration on login
- CSRF tokens on every form, output escaped with `htmlspecialchars`
- Uploads validated by type + size, stored with random names, PHP execution blocked

## Tech stack

PHP 8.1+ · MySQL 8 / MariaDB · Bootstrap 5 · HTML5 · CSS3 · JavaScript

## Install

**Option A — browser installer (easiest)**
1. Upload this folder to your hosting (XAMPP `htdocs`, or InfinityFree `htdocs`).
2. Create an empty MySQL database (InfinityFree: MySQL Databases in the control panel).
3. Open `http://your-site/install.php`, enter the database details + your admin account.
4. **Delete `install.php`** after it says success.

**Option B — manual**
1. `mysql -u root -p -e "CREATE DATABASE freshcart CHARACTER SET utf8mb4;"`
2. `mysql -u root -p freshcart < schema.sql`
3. Edit `config.php` with your database details.
4. Create the admin in MySQL or register a normal account and set `is_admin = 1` for it.

## Demo accounts (after install)

- **Admin:** the account you create in the installer (`/admin/login.php`)
- **Customer:** register a new account from the storefront

## File structure

```
config.php            Database + store settings (written by install.php)
install.php           Browser installer (delete after install)
schema.sql            Tables + 3 categories + 8 demo products
index.php             Homepage          shop.php        Shop / search / filters
product.php           Product detail    cart.php        Cart
checkout.php          Checkout (COD)    order-success.php  Confirmation
track.php             Track order       login.php / register.php / logout.php
account.php           My orders
admin/                Admin panel (login, dashboard, products, orders, categories)
includes/             db.php, functions.php, header.php, footer.php, product-card.php
assets/css/style.css  Cream + red theme
assets/js/app.js      Small front-end helpers
assets/images/        Hero + product images
uploads/              Product images uploaded from the admin panel
```

## Store rules

- Prices in PKR. Free delivery over Rs. 5,000, otherwise Rs. 200 flat.
- Payment: Cash on Delivery.

## Built by

Muhammad Usman — BSIT student, web developer (HTML, CSS, Bootstrap, JavaScript, PHP, MySQL, WordPress).
