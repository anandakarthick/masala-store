# Masala Store - Laravel E-Commerce Application

A complete Laravel-based e-commerce application for selling Masala, Oils, Candles & Return Gifts.

## Features

### Admin Panel
- **Dashboard** - Overview of sales, orders, and inventory
- **Product Management** - Full CRUD with images, pricing, stock management
- **Category Management** - Hierarchical categories with SEO fields
- **Order Management** - Process orders, update status, generate invoices
- **Customer Management** - View and manage customer accounts
- **Coupon System** - Create percentage/fixed discount coupons
- **Reports** - Sales, product, stock, and customer reports
- **Settings** - Business info, shipping charges, banners

### Customer Frontend
- **Product Catalog** - Browse by category, search, filter, sort
- **Shopping Cart** - Add/update/remove items
- **Checkout** - Guest & registered checkout with COD/UPI
- **Order Tracking** - Track orders by order number
- **User Account** - Order history, profile management

## Installation

### Requirements
- PHP 8.2+
- Composer
- Node.js & NPM
- SQLite (default) or MySQL

### Setup Steps

1. **Navigate to project directory:**
```bash
cd D:\cladue\masala-store
```

2. **Install PHP dependencies:**
```bash
composer install
```

3. **Install Node dependencies:**
```bash
npm install
```

4. **Run database migrations:**
```bash
php artisan migrate
```

5. **Seed the database:**
```bash
php artisan db:seed
```

6. **Create storage link:**
```bash
php artisan storage:link
```

7. **Build assets:**
```bash
npm run build
```

8. **Start the development server:**
```bash
php artisan serve
```

9. **Access the application:**
- Frontend: http://localhost:8000
- Admin Panel: http://localhost:8000/admin

## Default Admin Credentials

- **Email:** admin@masalastore.com
- **Password:** password

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # Admin controllers
│   │   ├── AuthController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   └── ...
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── RoleMiddleware.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Order.php
│   ├── Cart.php
│   └── ...
database/
├── migrations/              # Database migrations
├── seeders/                 # Database seeders
resources/
├── views/
│   ├── layouts/            # Layout templates
│   ├── admin/              # Admin views
│   ├── frontend/           # Frontend views
│   └── auth/               # Authentication views
routes/
└── web.php                 # All routes
```

## Key Routes

### Frontend
- `/` - Home page
- `/products` - Product listing
- `/products/{slug}` - Product detail
- `/category/{slug}` - Category products
- `/cart` - Shopping cart
- `/checkout` - Checkout page
- `/track` - Order tracking

### Admin
- `/admin` - Dashboard
- `/admin/products` - Product management
- `/admin/categories` - Category management
- `/admin/orders` - Order management
- `/admin/customers` - Customer management
- `/admin/coupons` - Coupon management
- `/admin/reports/sales` - Sales reports
- `/admin/reports/stock` - Stock reports
- `/admin/settings` - General settings

## Customization

### Adding New Categories
Categories can be added via Admin Panel > Categories > Add Category

### Updating Business Information
Go to Admin Panel > Settings to update:
- Business name, email, phone, address
- GST number
- Logo
- Shipping charges
- Free shipping threshold

### Managing Products
Products support:
- Multiple images
- Discount pricing
- Weight/quantity units (g, kg, ml, L, piece)
- Stock tracking with low stock alerts
- Batch numbers and expiry dates
- GST percentage
- SEO fields

## Technologies Used

- **Backend:** Laravel 12
- **Frontend:** Blade Templates, Tailwind CSS
- **JavaScript:** Alpine.js
- **Database:** SQLite (default)
- **Icons:** Font Awesome

## License

MIT License
