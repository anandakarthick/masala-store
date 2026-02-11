# Masala Store - Project Documentation

## Overview
SV Products - A Laravel 12 e-commerce platform for selling homemade spices and masala products with multi-channel support.

## Tech Stack
- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Blade templates, Tailwind CSS 4, Alpine.js
- **Database:** SQLite (dev) / MySQL (prod)
- **Build:** Vite
- **Auth:** Laravel Sanctum (API), Session (web), Google OAuth

## Project Structure
```
app/
├── Http/Controllers/
│   ├── Admin/              # Admin panel controllers
│   ├── Api/                # Mobile API controllers (v1)
│   ├── CheckoutController  # Web checkout
│   ├── RazorpayController  # Razorpay payments
│   └── PhonePeController   # PhonePe payments
├── Models/                 # Eloquent models (User, Product, Order, etc.)
├── Services/               # Business logic (FCM, Referral, Platforms)
├── Jobs/                   # Queue jobs (emails, notifications)
└── Mail/                   # Mailable classes

database/migrations/        # Schema migrations
resources/views/
├── admin/                  # Admin panel views
├── frontend/               # Customer-facing views
└── emails/                 # Email templates

routes/
├── web.php                 # Web + admin routes
└── api.php                 # API v1 routes
```

## Key Models
- `User` - Customers with wallet, referral codes
- `Product` - Products with variants, GST, stock tracking
- `Order` - Orders with items, payment status, delivery
- `PaymentMethod` - Payment gateway configurations
- `SellingPlatform` - Shopify/WooCommerce integrations

## Payment Integrations
| Gateway | Code | Controller |
|---------|------|------------|
| Cash on Delivery | `cod` | - |
| Razorpay | `razorpay` | `RazorpayController` |
| PhonePe | `phonepe` | `PhonePeController` |
| UPI (Manual) | `upi` | - |
| Bank Transfer | `bank_transfer` | - |

## API Endpoints
Base URL: `/api/v1/`

### Public
- `POST /auth/register`, `/auth/login`, `/auth/google`
- `GET /products`, `/categories`, `/home`
- `GET/POST /cart/*`
- `POST /orders/track`

### Protected (Sanctum)
- `GET /auth/me`, `PUT /auth/profile`
- `GET/POST /orders`, `/checkout`, `/wishlist`
- `POST /phonepe/create-order`
- `GET /phonepe/check-status/{orderNumber}`

## Commands
```bash
composer dev          # Run dev server with queue
composer test         # Run tests
php artisan migrate   # Run migrations
php artisan queue:work # Process queue jobs
```

## Environment Variables
Key settings in `.env`:
- `RAZORPAY_KEY_ID`, `RAZORPAY_KEY_SECRET`
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
- `FCM_SERVER_KEY` (Firebase push notifications)

Payment credentials are stored in `payment_methods.settings` JSON column.

## Admin Panel
Access: `/admin` (requires admin role)
- Dashboard, Orders, Products, Categories
- Customers, Coupons, Reports
- Payment Methods, Selling Platforms
- Settings, Banners

## Key Features
- Multi-channel selling (Shopify, WooCommerce)
- Product variants (size, weight)
- Custom combos (build your own)
- Wallet & referral system
- Push notifications (FCM)
- Invoice PDF generation
- Order tracking
