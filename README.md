# 🛍️ Merkato — Retail POS, Inventory & Finance Management System

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

**Merkato** is a lightning-fast, production-ready, and user-friendly Point of Sale (POS), Stock Inventory, and Financial Ledger system engineered for retail stores, wholesalers, supermarkets, and commercial businesses.

Designed specifically with **cashier ease-of-use** and **mathematical precision** in mind, Merkato eliminates complexity, provides offline high-speed responsiveness, prevents stock race conditions with row-level database locks, and maintains an immutable double-entry movement ledger.

---

## 🌟 Core Features & Modules

### ⚡ 1. Fast Cashier POS Terminal
- **Intuitive Single-Screen Checkout:** Tailored for non-technical cashiers with zero clutter.
- **Dynamic Product Selection:** Search by name, SKU, or barcode with live stock counter.
- **One-Touch Quantity Steppers:** Increment/decrement (`+` / `-`) quantities without retyping.
- **Quick Cash Tender Shortcuts:** Instant presets (`Exact Total`, `$10`, `$20`, `$50`, `$100`, `$500`).
- **Live Change Return Calculator:** Clear, bold display of exact cash change to hand back to the customer.
- **Customer Credit & Installments:** Allows partial payments, automatically tracking customer debt.
- **Thermal Receipt & PDF Invoicing:** Print-ready branded receipts with one click.

### 📦 2. Stock Catalog & Master Inventory
- Multi-unit product master (SKU, barcode, categories, safety stock levels).
- Real-time stock status badges: **In Stock**, **Low Stock Warning**, and **Out of Stock**.
- Cost price vs. retail selling price management with automatic margin calculation.

### 📥 3. Inbound Purchasing & Supplier Orders
- Multi-item Purchase Orders (Stock-In).
- Atomic inventory increment with latest supplier batch cost updates.
- Supplier debt & payables ledger tracking.

### 💰 4. Payments & Debt Recovery
- Separate customer debt collection and supplier bill settlement ledgers.
- Multi-installment payment recording (Cash, Card/POS, Bank Transfer, Cheque).
- Real-time receivables and payables balance updates.

### 📋 5. Immutable Stock Movement Ledger
- Append-only audit trail logging every stock event (`SALE`, `PURCHASE`, `ADJUSTMENT_ADD`, `ADJUSTMENT_SUBTRACT`, `RETURN`).
- Records `balance_before`, `quantity_delta`, `balance_after`, timestamp, user, and linked reference ID.

### 📊 6. Store Analytics & Financial Dashboard
- **Key KPIs:** Today's Sales, Cash in Drawer, Customer Debt to Collect, In-Store Stock Value, and Supplier Bills Due.
- Real-time low stock reorder alerts.
- Immutable system audit trail tracking all user operations and IP addresses.

---

## 🛠️ Technology Stack & Architecture

- **Backend Framework:** [Laravel 12](https://laravel.com) (PHP 8.2+)
- **Database:** MySQL / MariaDB (Strict Relational Schema, 13 Normalized Tables)
- **Frontend & Styling:** Blade Templates, Tailwind CSS, Alpine.js (Local Micro-Runtime), Lucide Icons
- **Performance:** Zero external CDN dependencies, instant local script execution, indexed foreign keys
- **Financial Precision:** Strict `DECIMAL(18,2)` handling across all monetary calculations
- **Concurrency Protection:** `DB::transaction()` with `lockForUpdate()` prevents overselling or race conditions

---

## 🚀 Quick Start Guide

### 1. Prerequisites
- **PHP** >= 8.2 (with `pdo_mysql`, `zip`, and `gd` extensions enabled in `php.ini`)
- **Composer** >= 2.x
- **MySQL / MariaDB** (via XAMPP, WampServer, or standalone MySQL service)
- **Node.js** >= 18.x (for asset compilation)

### 2. Installation

1. **Clone or Navigate to the Project Directory:**
   ```bash
   cd "Stock and inventory management system"
   ```

2. **Install Composer Dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM Packages & Build Assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Configure Environment File (`.env`):**
   Ensure your `.env` file is configured for your MySQL instance:
   ```env
   APP_NAME="Merkato"
   APP_ENV=local
   APP_KEY=base64:...
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=stock_inventory
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Create the Database:**
   In XAMPP phpMyAdmin (or MySQL CLI), create a database named `stock_inventory`:
   ```sql
   CREATE DATABASE stock_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

6. **Run Migrations & Seed Default Admin User:**
   ```bash
   php artisan migrate --seed
   ```
   *(Note: Migrations create a clean production database with zero mock products, ready for your real business data).*

7. **Start the Laravel Development Server:**
   ```bash
   php artisan serve --port=8000
   ```

8. **Open in Browser:**
   Navigate to: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Default Administrator Login

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin / Store Manager** | `admin@merkato.com` | `password123` |
| *Alternative Alias* | `admin@stockmaster.com` | `password123` |

---

## 📁 System Directory Structure

```text
├── app/
│   ├── Http/Controllers/        # Controllers (POS Sales, Purchases, Catalog, Payments, Reports)
│   ├── Models/                  # Eloquent Models with decimal casts & valuation accessors
│   └── Providers/               # AppServiceProvider (Memoized View Composers)
├── database/
│   ├── migrations/              # 13 normalized relational database migrations
│   └── seeders/                 # Clean DatabaseSeeder (Seeds only primary Admin)
├── docs/                        # Architectural Specifications & Business Invariants
│   ├── 01_PROJECT_REQUIREMENTS_AND_SCOPE.md
│   ├── 02_USE_CASES_AND_BUSINESS_RULES.md
│   └── 03_DATABASE_SCHEMA_AND_ER_DESIGN.md
├── public/
│   ├── build/                   # Compiled production CSS & JS assets
│   └── js/                      # Local Alpine.js & Lucide icon scripts (Zero CDN lag)
├── resources/
│   ├── css/                     # Tailwind CSS design system with high-contrast typography
│   └── views/
│       ├── auth/                # High-contrast login interface
│       ├── components/layouts/  # App master layout & Guest layout
│       ├── dashboard.blade.php  # Daily store cashier overview & KPIs
│       ├── sales/               # Cashier POS terminal & thermal receipt template
│       ├── products/            # Catalog management & stock level cards
│       ├── purchases/           # Inbound supplier stock-in orders
│       ├── payments/            # Multi-installment debt recovery ledger
│       ├── stock_transactions/  # Immutable movement audit log
│       └── reports/             # Business intelligence & valuation reports
└── routes/
    └── web.php                  # Web routes protected by authentication
```

---

## 🧪 Running Automated Tests

Merkato includes automated feature and unit tests covering authentication, route security, and catalog rendering:

```bash
php artisan test
```

---

## 📄 License

This software is released under the **MIT License**.
