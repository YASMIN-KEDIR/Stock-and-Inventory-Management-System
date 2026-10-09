# Software Requirements Specification (SRS) & Project Scope Document
**Project Name:** Stock & Inventory Management System  
**Document Version:** 1.0.0  
**Target Platform:** Web (Laravel / PHP / MySQL / Tailwind CSS)  
**Document Status:** Approved for Client Review & Sign-Off  

---

## 1. Project Overview & Business Objectives

### 1.1 Purpose
This document defines the complete functional and non-functional requirements for a production-grade **Stock and Inventory Management System**. The system is engineered for a commercial business handling high-volume inventory movements, sales, purchases, customer credit balances, supplier obligations, and multi-installment payments.

### 1.2 Primary Business Goals
1. **Accurate Stock Control:** Eliminate stock discrepancies through a double-entry style stock movement ledger.
2. **Robust Credit & Debt Tracking:** Provide transparent customer and supplier credit balances with full payment history.
3. **Financial Data Integrity:** Ensure zero rounding errors, atomic database transactions (ACID), and immutable audit trails.
4. **Actionable Business Intelligence:** Deliver real-time low-stock alerts, daily/monthly sales reports, profit metrics, and dashboard analytics.
5. **Operational Reliability:** Ensure high performance, automated backups, disaster recovery, and robust access control.

---

## 2. User Roles & Access Architecture

### 2.1 Initial Version (Phase 1–Production Release)
- **Role:** `ADMIN` (System Administrator / Business Owner)
- **Access Level:** Full access to all modules, settings, transactions, audit logs, and reports.

### 2.2 Extensible Role Design (Future-Proofing)
While only the `ADMIN` role is active for the initial rollout, all authorization logic will use Laravel **Policies** and **Form Requests** structured around fine-grained permissions (e.g., `products.view`, `sales.create`, `reports.export`). This enables future roles (`Cashier`, `Storekeeper`, `Accountant`, `Manager`) to be added via database roles/permissions without restructuring application code.

---

## 3. Scope of Work (In-Scope Modules)

```
+-----------------------------------------------------------------------------------+
|                        STOCK & INVENTORY MANAGEMENT SYSTEM                        |
+-----------------------------------------------------------------------------------+
|                                                                                   |
|  [ Dashboard ] <---> [ Products & Categories ] <---> [ Inventory Ledger ]         |
|         ^                        ^                           ^                    |
|         |                        |                           |                    |
|  [ Reports ]             [ Purchases (In) ]           [ Sales (Out) ]             |
|         ^                        |                           |                    |
|         |                        v                           v                    |
|  [ Audit Logs ]        [ Supplier Payments ]       [ Customer Credit / Pay ]      |
|                                                                                   |
+-----------------------------------------------------------------------------------+
```

### Module 1: Authentication & Access Control
- Secure login and logout with brute-force rate limiting.
- Password hashing using Argon2id or Bcrypt.
- Session expiration and secure cookie management.
- Authorization policy checks on every resource endpoint.

### Module 2: Category Management
- Hierarchical or flat product categorization.
- Fields: Name, Code/Slug, Description, Status (Active/Inactive).
- Safe Deletion Rule: Categories linked to existing products cannot be hard-deleted; they can only be deactivated or reassigned.

### Module 3: Product Management
- Fields: Product Name, SKU (Unique), Barcode (Optional/Unique), Category, Supplier, Unit of Measure (e.g., Pcs, Kg, Box, Litre), Cost/Purchase Price, Selling Price, Minimum Stock Alert Level, Current Stock Quantity, Description, Product Image, Status (Active/Inactive).
- Server-side search, multi-parameter filtering (by category, status, stock level), sorting, and database-level pagination.
- Immutable SKU after initial transaction creation to preserve historical reporting.

### Module 4: Supplier Management & Purchase Tracking (Stock In)
- **Supplier Profile:** Name, Company Name, Phone, Email, Physical Address, Notes, Cumulative Outstanding Balance.
- **Purchase Order / Stock-In Record:**
  - Supplier selection, Purchase Reference/Invoice #, Purchase Date, Notes.
  - Line items: Product, Quantity, Unit Cost, Subtotal.
  - Overall Calculation: Subtotal, Tax/Shipping (if applicable), Total Amount.
  - Payment Details: Amount Paid, Remaining Balance, Payment Status (`UNPAID`, `PARTIALLY_PAID`, `PAID`).
- **Confirmation Trigger:**
  - Automatically increments physical stock.
  - Writes a record into the `stock_transactions` ledger.
  - Updates supplier credit/balance ledger atomically.

### Module 5: Customer Management & Sales Tracking (Stock Out)
- **Customer Profile:** Full Name, Phone, Email, Address, Notes, Cumulative Outstanding Credit Balance.
- **Sales Invoice / Order:**
  - Customer selection (or Walk-in / Cash Customer), Invoice # (Auto-generated unique format), Sale Date.
  - Line items: Product, Quantity, Unit Price, Line Discount, Line Total.
  - Overall Calculation: Subtotal, Overall Discount, Net Total Payable.
  - Initial Payment: Amount Paid at sale time, Payment Method (Cash, Bank Transfer, POS/Card, Cheque), Remaining Balance.
  - Payment Status: `PAID`, `PARTIALLY_PAID`, `UNPAID`.
- **Validation & Stock Safety:**
  - Real-time stock verification: Prevents completing a sale if `requested_quantity > available_stock`.
  - Atomically decrements product stock and records an outbound stock transaction.

### Module 6: Customer Credit & Multi-Installment Payment System
- Support for deferred/credit sales where balance > 0.
- Capability to record multiple partial payments against a single sale invoice over time.
- Payment Record: Payment Date, Amount Paid, Payment Method, Reference/Receipt Number, Notes, Received By (Admin).
- Status auto-updates (`UNPAID` $\rightarrow$ `PARTIALLY_PAID` $\rightarrow$ `PAID`).
- Complete **Customer Account Statement** showing running balances, invoice history, and timestamped payment history.

### Module 7: Supplier Debt & Payment Tracking
- Recording outbound payments made to suppliers against open purchase invoices.
- Supplier ledger statement tracking total purchased, total paid, and net balance owed.

### Module 8: Transaction-Based Stock Ledger & Adjustments
- **Stock Movement Types:**
  - `PURCHASE` (Incoming from Supplier)
  - `SALE` (Outgoing to Customer)
  - `CUSTOMER_RETURN` (Incoming return from customer)
  - `SUPPLIER_RETURN` (Outgoing return to supplier)
  - `ADJUSTMENT` (Manual correction for damage, shrinkage, audit discrepancy)
- Every transaction stores: `product_id`, `type`, `quantity_change` (+/-), `balance_after`, `reference_type`, `reference_id`, `user_id`, `reason/notes`, `created_at`.
- Stock quantities are strictly auditable from the transaction log.

### Module 9: Low-Stock Alerts & Notification Engine
- Real-time detection when `current_quantity <= minimum_stock_level`.
- Highlighted badges on product listings and dedicated Low-Stock Alert widget on the main dashboard.
- Direct quick-action link to create a Purchase Order for low-stock items.

### Module 10: Executive Dashboard
- **KPI Summary Cards:** Today's Sales, Today's Purchases, Today's Cash Collected, Outstanding Customer Receivables, Outstanding Supplier Payables, Total Stock Valuation (at cost & at retail).
- **Alert Panel:** Real-time low-stock product list.
- **Recent Activity Streams:** Recent 10 sales, recent 10 purchases, recent payment receipts.

### Module 11: Reports & Business Intelligence
- Filterable by Custom Date Range, Customer, Supplier, Category, Product, Payment Status.
- **Report Types:**
  1. Sales Summary & Detailed Sales Report
  2. Purchase Summary & Detailed Purchase Report
  3. Stock Valuation & Inventory Movement Ledger Report
  4. Low Stock / Reorder Report
  5. Customer Outstanding Debt & Aging Report
  6. Customer Payment Collection Report
  7. Supplier Payable & Payment History Report
  8. Gross Profit Margin Report (Calculated from Selling Price minus Weighted Unit Cost)
- Export formats: Printable Clean View / PDF-ready view and CSV/Excel data export.

### Module 12: Audit Trail & Activity Logging
- Immutable log recording key system events:
  - User Logins / Failed Login attempts
  - Product / Category creation & edits
  - Purchase & Sale confirmations
  - Payment additions
  - Stock manual adjustments
  - Critical system configuration updates
- Audit records store: `user_id`, `action`, `entity_type`, `entity_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, `timestamp`.
- Audit records cannot be edited or deleted via the UI.

---

## 4. Out-of-Scope (Excluded from Current Version)

To maintain focus, stability, and on-time delivery, the following features are explicitly **excluded** from this version:
1. Multi-warehouse / multi-branch stock transfers (System assumes single central store/warehouse).
2. Direct e-commerce public storefront or third-party marketplace API integrations (e.g., Shopify/WooCommerce).
3. Automated payment gateway integrations requiring customer self-checkout (All payments are recorded by Admin as Cash/Card/Bank Transfer).
4. Direct thermal printer hardware raw ESC/POS drivers (System uses browser standard printing / printable formatted HTML invoices).
5. Native mobile apps (System will be fully responsive and accessible via mobile/tablet web browsers).

---

## 5. Non-Functional & Quality Requirements

### 5.1 Financial & Data Integrity Rules
- **No Floating-Point Arithmetic for Money:** All financial figures (prices, discounts, payments, totals) are stored as `DECIMAL(15, 2)` or `DECIMAL(15, 4)` in MySQL and handled with high precision in PHP (avoiding binary floating-point rounding bugs).
- **ACID Database Transactions:** Any business action involving multiple table updates (e.g., Creating a Sale + Creating Sale Items + Decrementing Stock + Creating Stock Movement + Creating Initial Payment) must be wrapped inside `DB::transaction()`. If any step fails, the entire operation is rolled back with zero orphaned rows.
- **Strict Concurrency Safety:** Stock deductions utilize database row locking (`lockForUpdate`) during order confirmation to prevent race conditions when multiple sales occur simultaneously.

### 5.2 Security Specifications
- Protection against OWASP Top 10 vulnerabilities (SQL Injection via Eloquent PDO parameter binding, XSS prevention via Blade escaping, CSRF protection on all mutating requests).
- Rate-limiting on authentication routes to prevent credential stuffing.
- Secure environment configuration (`APP_DEBUG=false` in production, secure session storage, HTTPS enforcement).

### 5.3 Performance & Scalability
- Database indexes on all foreign keys, lookup codes (SKU, barcode, invoice numbers), and query filter fields (status, dates).
- Database-level pagination (`paginate()`) on all index tables; zero unrestrained `::all()` queries on large datasets.
- Eloquent Eager Loading (`with(['category', 'supplier', 'items'])`) to prevent $N+1$ query performance degradation.

### 5.4 Backup, Recovery & Maintenance
- Automated daily database dump strategy.
- Off-server backup archiving recommendation.
- Documented step-by-step disaster recovery procedure.

---

## 6. Project Delivery Phases & Milestones

| Phase | Milestone Description | Primary Deliverables |
|:---:|:---|:---|
| **Phase 1** | Requirements & Scope Definition | Formal SRS Document & Client Acceptance Sign-off *(Current)* |
| **Phase 2** | Business Rules & Use Case Mapping | Detailed state machines, financial calculation rules, validation schemas |
| **Phase 3** | Database Architecture & ER Design | Normalized Schema, Table Definitions, Foreign Keys, Indexes, ER Diagram |
| **Phase 4** | Project Setup & Foundation | Clean Laravel Setup, Tailwind CSS, Base Layouts, Component Kit |
| **Phase 5** | Authentication & Admin Access | Secure Auth, Profile Management, Policy Architecture |
| **Phase 6** | Categories & Products Management | Full CRUD, SKU generator, Image upload, Low-stock threshold config |
| **Phase 7** | Suppliers & Purchases (Stock In) | Supplier ledger, Purchase Order workflow, Atomic stock increment |
| **Phase 8** | Customers & Sales (Stock Out) | Customer directory, Invoice builder, Atomic stock decrement, Invoice printing |
| **Phase 9** | Transactional Stock Management | Stock movement ledger, Manual adjustments with audit reasons |
| **Phase 10** | Credit Management & Payments | Customer/Supplier payment receipts, partial payment workflows, ledger statements |
| **Phase 11** | Reporting Engine & Export | Date-range filters, Sales/Purchase/Profit/Stock reports, CSV/PDF export |
| **Phase 12** | Executive Dashboard | Real-time metric cards, Low-stock alerts, visual charts & trends |
| **Phase 13** | Audit Trail & Security Hardening | Activity logging, Input sanitation audit, Row-locking verification |
| **Phase 14** | Automated & Manual Testing | Feature tests, Transaction rollback tests, Edge-case validation |
| **Phase 15** | Production Readiness & Backups | Environment configs, automated backup scripts, health checks |
| **Phase 16** | Deployment & Server Setup | VPS setup, Nginx, PHP-FPM, MySQL, SSL, Domain DNS configuration |
| **Phase 17** | Client Acceptance & Sign-off | Formal UAT (User Acceptance Testing) walkthrough with client |
| **Phase 18** | User Manual & Staff Handover | User training guide, operational manual, administrator documentation |
| **Phase 19** | Post-Launch Support & Maintenance | 30-day warranty support, maintenance plan, backup verification |

---

## 7. Client Acceptance Sign-Off

By signing below, the client agrees that the requirements and scope outlined in this document accurately represent the business needs and form the official baseline for the application build.

**Client Representative:** ___________________________________  
**Signature:** _______________________________________________  
**Date:** ____________________________________________________  

**Lead Developer / Contractor:** _____________________________  
**Signature:** _______________________________________________  
**Date:** ____________________________________________________  
