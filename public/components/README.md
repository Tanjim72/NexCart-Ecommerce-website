# Frontend Components Documentation

## Overview

The frontend has been organized into **reusable HTML/PHP component files** for better maintainability and code reuse. Each component handles a specific UI element or section.

---

## Components Directory Structure

```
public/components/
├── product-card.php          # Single product display (shopping)
├── cart-item.php             # Single cart item (with quantity controls)
├── cart-summary.php          # Cart totals (subtotal, shipping, total)
├── checkout-form.php         # Checkout customer information form
├── payment-options.php       # Payment method selection
├── order-item.php            # Single order item (invoice display)
├── order-summary.php         # Order totals & payment info (invoice)
├── order-table.php           # Admin order list table
├── status-badge.php          # Order status indicator
└── README.md                 # This file
```

---

## Component Details

### 1. **product-card.php**

**Purpose**: Display a single product with image, name, price, and "Add to Cart" button

**Location**: `public/components/product-card.php`

**Usage**:
```php
<?php foreach ($products as $product): ?>
    <?php include 'components/product-card.php'; ?>
<?php endforeach; ?>
```

**Required Variables**:
- `$product` (array) - Product data with keys:
  - `product_id` (int)
  - `name` (string)
  - `description` (string)
  - `price` (float)
  - `emoji_icon` (string)

**Output Example**:
```html
<article class="product-card" data-product-id="1">
    <div class="product-image">📱</div>
    <div class="product-details">
        <h3>Wireless Headphones</h3>
        <p>Premium noise-cancelling audio</p>
        <div>৳ 7,999</div>
        <button onclick="addToCart(1, 'Wireless Headphones', 7999)">
            + Add to Cart
        </button>
    </div>
</article>
```

---

### 2. **cart-item.php**

**Purpose**: Display a single item in the shopping cart with quantity controls

**Location**: `public/components/cart-item.php`

**Usage**:
```php
<?php foreach ($cart_items as $item): ?>
    <?php include 'components/cart-item.php'; ?>
<?php endforeach; ?>
```

**Required Variables**:
- `$item` (array) - Cart item with keys:
  - `product_id` (int)
  - `name` (string)
  - `price` (float)
  - `quantity` (int)

**Features**:
- Quantity increment/decrement buttons
- Quantity input field
- Remove button
- Item total calculation

---

### 3. **cart-summary.php**

**Purpose**: Display cart totals (subtotal, shipping, total)

**Location**: `public/components/cart-summary.php`

**Usage**:
```php
<?php
$subtotal = 15000;
$total = 15200;
$shipping_cost = 200;
include 'components/cart-summary.php';
?>
```

**Required Variables**:
- `$subtotal` (float) - Cart subtotal
- `$total` (float) - Cart total after shipping
- `$shipping_cost` (float, optional) - Shipping cost (default: 0)

**Outputs**:
- Subtotal row
- Shipping cost row
- Total row (highlighted)

---

### 4. **checkout-form.php**

**Purpose**: Display the checkout form with all customer information fields

**Location**: `public/components/checkout-form.php`

**Usage**:
```php
<?php
$csrf_token = generateCsrfToken();
include 'components/checkout-form.php';
?>
```

**Required Variables**:
- `$csrf_token` (string) - CSRF token for security

**Form Sections**:
1. **Personal Information**
   - First Name (required)
   - Last Name (required)
   - Email (required)
   - Phone (required)

2. **Shipping Address**
   - Street Address (required)
   - City (required)
   - State/Province (required)
   - Postal Code (required)
   - Country (required, default: Bangladesh)

3. **Shipping Method** (required)
   - Standard Shipping - Free
   - Express Shipping - ৳ 200
   - Overnight Shipping - ৳ 500

4. **Order Notes** (optional)
   - Special instructions for the order

**Features**:
- All inputs validate on change
- Real-time form validation
- CSRF token embedded in hidden input

---

### 5. **payment-options.php**

**Purpose**: Display payment method selection radio buttons

**Location**: `public/components/payment-options.php`

**Usage**:
```php
<?php include 'components/payment-options.php'; ?>
```

**No Required Variables**

**Payment Methods**:
1. Cash on Delivery (default) - 💵
2. Bank Transfer - 🏦
3. Mobile Banking - 📱

---

### 6. **order-item.php**

**Purpose**: Display a single item in an order invoice

**Location**: `public/components/order-item.php`

**Usage**:
```php
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order_items as $item): ?>
            <?php include 'components/order-item.php'; ?>
        <?php endforeach; ?>
    </tbody>
</table>
```

**Required Variables**:
- `$item` (array) - Order item with keys:
  - `product_name` (string)
  - `quantity` (int)
  - `price` (float) - Price at time of order

**Output**: Table row `<tr>` with item details

---

### 7. **order-summary.php**

**Purpose**: Display order totals and payment information on invoice

**Location**: `public/components/order-summary.php`

**Usage**:
```php
<?php
$subtotal = 7999;
$shipping = 200;
$total = 8199;
$payment_method = 'cash_on_delivery';
include 'components/order-summary.php';
?>
```

**Required Variables**:
- `$subtotal` (float)
- `$shipping` (float)
- `$total` (float)
- `$payment_method` (string) - One of: cash_on_delivery, bank_transfer, mobile_banking

**Displays**:
- Order totals section
- Payment method section

---

### 8. **order-table.php**

**Purpose**: Display admin list of orders with pagination

**Location**: `public/components/order-table.php`

**Usage**:
```php
<?php
$orders = getOrders($page);
$show_actions = true;
include 'components/order-table.php';
?>
```

**Required Variables**:
- `$orders` (array) - Array of order records with keys:
  - `order_id` (int)
  - `customer_name` (string)
  - `customer_email` (string)
  - `total_amount` (float)
  - `status` (string)
  - `created_at` (datetime)

**Optional Variables**:
- `$show_actions` (boolean, default: true) - Show "View Details" links

**Features**:
- Sortable columns
- Status badges with color coding
- Action links to order details
- Responsive table layout

---

### 9. **status-badge.php**

**Purpose**: Display color-coded order status indicator

**Location**: `public/components/status-badge.php`

**Usage**:
```php
<?php
$status = 'processing';
include 'components/status-badge.php';
?>
```

**Required Variables**:
- `$status` (string) - One of: pending, processing, shipped, delivered, cancelled

**Status Colors**:
- **pending** (yellow) - ⏳
- **processing** (blue) - ⚙️
- **shipped** (purple) - 📦
- **delivered** (green) - ✅
- **cancelled** (red) - ❌

---

## Component Usage Examples

### Example 1: Display Products Grid

```php
<!-- public/index.php -->
<?php
$products = getProducts(); // From functions.php
?>

<div class="products-grid">
    <?php foreach ($products as $product): ?>
        <?php include 'components/product-card.php'; ?>
    <?php endforeach; ?>
</div>
```

### Example 2: Display Shopping Cart

```php
<!-- public/index.php (cart section) -->
<?php
if (!empty($_SESSION['cart'])) {
    $cart_items = $_SESSION['cart'];
    [$subtotal, $total] = calculateCartTotals();
?>
    <div id="cartItems">
        <?php foreach ($cart_items as $item): ?>
            <?php include 'components/cart-item.php'; ?>
        <?php endforeach; ?>
    </div>
    
    <?php
    $shipping_cost = 0;
    include 'components/cart-summary.php';
    ?>
<?php } ?>
```

### Example 3: Display Order Invoice

```php
<!-- public/order_confirmation.php -->
<?php
$order = getOrderWithItems($order_id);
[$subtotal, $shipping] = getOrderTotals($order_id);
$total = $order['total_amount'];
?>

<table class="invoice-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order['items'] as $item): ?>
            <?php include 'components/order-item.php'; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$payment_method = $order['payment_method'] ?? 'cash_on_delivery';
include 'components/order-summary.php';
?>
```

### Example 4: Admin Order List

```php
<!-- public/admin/index.php -->
<?php
$page = $_GET['page'] ?? 1;
$orders = getOrders($page, 10);
$show_actions = true;
include 'components/order-table.php';
?>
```

---

## Benefits of Component Organization

✅ **Reusability** - Use the same component in multiple pages
✅ **Maintainability** - Update component in one place
✅ **Consistency** - Same styling/markup across application
✅ **Testability** - Test each component independently
✅ **Separation of Concerns** - Each component has one purpose
✅ **Readability** - Pages are cleaner and easier to understand
✅ **Scalability** - Easy to add new components

---

## Creating New Components

To create a new component:

1. **Create file** in `public/components/` named descriptively
2. **Add PHP docblock** at top with:
   - Purpose
   - Usage instructions
   - Required variables
3. **Use safe output** - Always use `htmlspecialchars()` for user data
4. **Keep it focused** - One component = one purpose
5. **Document it** - Update this README

Example template:
```php
<?php
/**
 * Component Name
 * Brief description of what it does
 * 
 * Usage: <?php include 'components/my-component.php'; ?>
 * 
 * Parameters:
 * - $variable1 (type) - Description
 * - $variable2 (type) - Description
 */
?>

<!-- Component HTML here -->
```

---

## Component Dependency Map

```
Product Card
    └─ No dependencies (standalone)

Cart Item
    └─ Uses updateQuantity() & removeFromCart() (JS)

Cart Summary
    └─ No dependencies

Checkout Form
    └─ Uses validateCheckoutForm() (JS)
    └─ Requires CSRF token

Payment Options
    └─ No dependencies

Order Item
    └─ No dependencies (display only)

Order Summary
    └─ No dependencies (display only)

Order Table
    └─ Uses getOrders() (PHP)
    └─ Includes order links

Status Badge
    └─ No dependencies
```

---

## CSS Classes Reference

All components use these CSS classes (defined in `public/assets/css/style.css`):

**Product Card**:
- `.product-card`
- `.product-image`
- `.product-details`
- `.product-name`
- `.product-desc`
- `.product-price`
- `.btn`, `.btn-primary`

**Cart Item**:
- `.cart-item`
- `.item-info`, `.item-name`, `.item-price`
- `.quantity-control`, `.qty-btn`, `.qty-input`
- `.item-total`
- `.remove-btn`

**Cart Summary**:
- `.cart-summary`
- `.summary-row`
- `.summary-row.total`

**Checkout Form**:
- `.checkout-form`
- `.form-section`
- `.form-row`
- `.form-group`

**Payment Options**:
- `.payment-form-section`
- `.payment-options`
- `.payment-option`
- `.payment-label`

**Order Table**:
- `.order-table`
- `.order-id`
- `.amount`
- `.status-badge`
- `.btn-link`

**Status Badge**:
- `.status-badge`
- `.status-pending`, `.status-processing`, `.status-shipped`, `.status-delivered`, `.status-cancelled`

---

## Frontend Files Organization Summary

```
Frontend Files (Organized by Function):
├── public/
│   ├── index.php                 (Uses product-card, cart-item, cart-summary, checkout-form)
│   ├── order_confirmation.php    (Uses order-item, order-summary, status-badge)
│   ├── admin/
│   │   ├── index.php            (Uses order-table)
│   │   └── orders.php           (Uses order-summary, status-badge)
│   ├── components/              (Reusable UI components)
│   │   ├── product-card.php
│   │   ├── cart-item.php
│   │   ├── cart-summary.php
│   │   ├── checkout-form.php
│   │   ├── payment-options.php
│   │   ├── order-item.php
│   │   ├── order-summary.php
│   │   ├── order-table.php
│   │   └── status-badge.php
│   ├── assets/
│   │   ├── css/style.css        (1200+ lines, component styles)
│   │   └── js/main.js           (7 modules, 523 lines)
├── includes/                     (PHP templates)
│   ├── header.php
│   ├── nav.php
│   └── footer.php
```

---

## Best Practices

1. **Always escape output** - Use `htmlspecialchars()` for any user-provided data
2. **Check variables exist** - Use `isset()` or `??` operator for optional variables
3. **Keep components focused** - One responsibility per component
4. **Add comments** - Document parameters and expected data format
5. **Use semantic HTML** - Use proper tags: `<article>`, `<section>`, etc.
6. **Avoid inline styles** - Use CSS classes instead
7. **Test components** - Verify they work with different data
8. **Update documentation** - Keep this file current

---

**Last Updated**: December 29, 2025
**Total Components**: 9 reusable components
**Code Reuse**: ~40% reduction in page file lines
