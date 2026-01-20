# 🔐 Razorpay Integration Setup Guide

## Overview

This guide will help you set up Razorpay payment gateway in your local SV Products e-commerce application.

---

## 📋 Prerequisites

1. **Razorpay Account** - Sign up at https://dashboard.razorpay.com/
2. **PHP cURL extension** - Usually enabled by default in XAMPP
3. **SSL (for production)** - Razorpay requires HTTPS for live mode

---

## 🚀 Quick Setup (5 Minutes)

### Step 1: Get Razorpay API Keys

1. Go to [Razorpay Dashboard](https://dashboard.razorpay.com/)
2. Sign up or login
3. Navigate to **Settings** → **API Keys**
4. Click **Generate Test Key** (for local development)
5. Copy both:
   - **Key ID** (starts with `rzp_test_` for test mode)
   - **Key Secret**

> ⚠️ **Important**: Keep your Key Secret safe! Never expose it in frontend code.

### Step 2: Configure in Admin Panel

1. Start your local server:
   ```bash
   cd D:\cladue\masala-store
   php artisan serve
   ```

2. Login to Admin Panel: `http://localhost:8000/admin`

3. Go to **Payment Methods** in the sidebar

4. Click **Edit** on **Razorpay**

5. Enter your credentials:
   - **Razorpay Key ID**: `rzp_test_xxxxxxxxxx`
   - **Razorpay Key Secret**: `your_secret_key`
   - **Webhook Secret**: (optional, for webhooks)

6. Enable the payment method by checking **Is Active**

7. Click **Save**

### Step 3: Test the Integration

1. Add products to cart
2. Proceed to checkout
3. Fill in shipping details
4. Select **Pay Online (Card/UPI/NetBanking)**
5. Complete checkout
6. On payment page, click **Pay ₹XXX**
7. Razorpay checkout will open

**Test Card Details:**
```
Card Number: 4111 1111 1111 1111
Expiry: Any future date (e.g., 12/25)
CVV: Any 3 digits (e.g., 123)
Name: Any name
OTP: 1234
```

**Test UPI:**
```
UPI ID: success@razorpay (for successful payment)
UPI ID: failure@razorpay (for failed payment)
```

---

## 🔧 Alternative Setup Methods

### Method A: Using Database Seeder

1. Edit the seeder file:
   ```
   D:\cladue\masala-store\database\seeders\RazorpaySetupSeeder.php
   ```

2. Replace the placeholder credentials:
   ```php
   'settings' => [
       'key_id' => 'rzp_test_YOUR_KEY_ID',
       'key_secret' => 'YOUR_KEY_SECRET',
       'webhook_secret' => '',
   ],
   ```

3. Run the seeder:
   ```bash
   cd D:\cladue\masala-store
   php artisan db:seed --class=RazorpaySetupSeeder
   ```

### Method B: Direct Database Update

```sql
UPDATE payment_methods 
SET 
    is_active = 1,
    settings = JSON_OBJECT(
        'key_id', 'rzp_test_YOUR_KEY_ID',
        'key_secret', 'YOUR_KEY_SECRET',
        'webhook_secret', ''
    )
WHERE code = 'razorpay';
```

---

## 📁 File Structure

Here's where the Razorpay integration code lives:

```
masala-store/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── RazorpayController.php      # Main Razorpay logic
│           ├── CheckoutController.php      # Checkout flow
│           └── Admin/
│               └── PaymentMethodController.php  # Admin settings
├── app/
│   └── Models/
│       └── PaymentMethod.php               # Payment method model
├── resources/
│   └── views/
│       └── frontend/
│           └── checkout/
│               ├── payment.blade.php       # Payment page
│               └── index.blade.php         # Checkout page
├── routes/
│   └── web.php                             # Razorpay routes
└── database/
    └── seeders/
        └── RazorpaySetupSeeder.php         # Setup seeder
```

---

## 🔄 Payment Flow

```
1. Customer fills checkout form
         ↓
2. Order created with status "pending"
         ↓
3. Redirect to payment page
         ↓
4. Frontend calls /razorpay/create-order
         ↓
5. Backend creates Razorpay order via API
         ↓
6. Razorpay checkout popup opens
         ↓
7. Customer completes payment
         ↓
8. Frontend receives payment response
         ↓
9. Frontend calls /razorpay/verify-payment
         ↓
10. Backend verifies signature
         ↓
11. Order updated to "paid" + "confirmed"
         ↓
12. Redirect to success page
```

---

## 🌐 Webhook Setup (Optional but Recommended)

Webhooks provide a backup payment confirmation in case the user closes the browser.

### Setup Steps:

1. Go to Razorpay Dashboard → **Webhooks**

2. Click **+ Add New Webhook**

3. Enter Webhook URL:
   ```
   https://yourdomain.com/razorpay/webhook
   ```
   > For local testing, use ngrok: `https://xxxx.ngrok.io/razorpay/webhook`

4. Select Events:
   - `payment.captured`
   - `payment.failed`

5. Copy the **Webhook Secret**

6. Add the secret to your Razorpay settings in Admin Panel

---

## 🧪 Testing Scenarios

### Successful Payment
1. Use test card: `4111 1111 1111 1111`
2. Complete OTP: `1234`
3. Order should be marked as "paid" and "confirmed"

### Failed Payment
1. Use test card: `4000 0000 0000 0002`
2. Payment will fail
3. User returns to payment page with error message

### Payment Cancelled
1. Open Razorpay checkout
2. Click X or press Escape
3. Error message: "Payment cancelled"

### UPI Payment
1. Select UPI in Razorpay
2. Enter: `success@razorpay`
3. Complete mock UPI flow

---

## 🐛 Troubleshooting

### Error: "Razorpay credentials are not configured"
- Check if Key ID and Key Secret are set in Admin Panel
- Verify Razorpay payment method is enabled

### Error: "Failed to create payment order"
- Check Laravel logs: `storage/logs/laravel.log`
- Verify API keys are correct (not swapped)
- Ensure cURL is enabled in PHP

### Payment Success but Order Not Updated
- Check if webhook is configured
- Verify webhook secret matches
- Check Laravel logs for webhook errors

### Razorpay Checkout Not Opening
- Check browser console for JavaScript errors
- Verify Razorpay script is loaded
- Ensure CSRF token is present

---

## 🔒 Security Best Practices

1. **Never expose Key Secret** in frontend code
2. **Always verify signatures** on payment completion
3. **Use webhooks** as backup confirmation
4. **Enable HTTPS** in production
5. **Use Live keys** only in production

---

## 📊 Razorpay Dashboard

### Useful Dashboard Sections:
- **Transactions** - View all payments
- **Settlements** - Track bank settlements
- **Disputes** - Handle chargebacks
- **Reports** - Download transaction reports

---

## 🔄 Switching to Live Mode

1. In Razorpay Dashboard, switch to **Live Mode**
2. Complete KYC verification
3. Generate **Live API Keys**
4. Update credentials in Admin Panel
5. Test with real ₹1 payment
6. Go live!

---

## 📞 Support

- **Razorpay Docs**: https://razorpay.com/docs/
- **Razorpay Support**: https://razorpay.com/support/
- **API Reference**: https://razorpay.com/docs/api/

---

## ✅ Checklist

- [ ] Created Razorpay account
- [ ] Generated Test API keys
- [ ] Added keys to Admin Panel
- [ ] Enabled Razorpay payment method
- [ ] Tested successful payment
- [ ] Tested failed payment
- [ ] Tested payment cancellation
- [ ] (Optional) Set up webhooks
- [ ] Ready for production!
