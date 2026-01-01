-- ============================================
-- SECTOR 3: Order & Payment System Database Schema
-- ============================================
-- This schema covers:
-- - Shopping Cart & Checkout
-- - Multiple Payment Options
-- - Order Tracking
-- - Invoice Generation
-- - Refund & Return Management
-- ============================================

-- Create orders table (CORE - Main order data)
CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(200) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    
    -- Delivery Address
    delivery_address VARCHAR(500) NOT NULL,
    delivery_city VARCHAR(100) NOT NULL,
    delivery_state VARCHAR(100) NOT NULL,
    delivery_postal_code VARCHAR(20) NOT NULL,
    delivery_country VARCHAR(100) DEFAULT 'Bangladesh',
    
    -- Order Information
    order_notes TEXT,
    
    -- Pricing
    subtotal DECIMAL(12, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    coupon_code VARCHAR(50),
    order_total DECIMAL(12, 2) NOT NULL,
    
    -- Shipping & Payment
    shipping_method VARCHAR(100) DEFAULT 'standard',
    payment_method VARCHAR(100) NOT NULL,
    payment_status VARCHAR(50) DEFAULT 'pending',
    
    -- Order Status
    order_status VARCHAR(50) DEFAULT 'pending',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_order_number (order_number),
    INDEX idx_customer_email (customer_email),
    INDEX idx_order_status (order_status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create order_items table (Order line items)
CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100),
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    item_total DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create payments table (Payment tracking)
CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'BDT',
    transaction_id VARCHAR(255),
    reference_number VARCHAR(255),
    status VARCHAR(50) DEFAULT 'pending',
    gateway_response LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_transaction (transaction_id),
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create order_tracking table (Real-time tracking)
CREATE TABLE IF NOT EXISTS order_tracking (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    status_description TEXT,
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create refunds table (Return & refund management)
CREATE TABLE IF NOT EXISTS refunds (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    refund_amount DECIMAL(12, 2) NOT NULL,
    reason VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'requested',
    initiated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_date TIMESTAMP NULL,
    reference_number VARCHAR(255),
    notes TEXT,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create returns table (Item-level returns)
CREATE TABLE IF NOT EXISTS returns (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    order_item_id INT UNSIGNED NOT NULL,
    return_quantity INT UNSIGNED NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'requested',
    return_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_date TIMESTAMP NULL,
    received_date TIMESTAMP NULL,
    notes TEXT,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create coupons table (Discount codes for checkout)
CREATE TABLE IF NOT EXISTS coupons (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    coupon_code VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    discount_type VARCHAR(10) NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    min_purchase DECIMAL(10, 2) DEFAULT 0,
    max_usage INT UNSIGNED DEFAULT 999999,
    current_usage INT UNSIGNED DEFAULT 0,
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_coupon_code (coupon_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create invoices table (Invoice generation & storage)
CREATE TABLE IF NOT EXISTS invoices (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id INT UNSIGNED NOT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    invoice_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date TIMESTAMP NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    shipping DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(12, 2) NOT NULL,
    paid_amount DECIMAL(12, 2) DEFAULT 0,
    pdf_file VARCHAR(255),
    status VARCHAR(50) DEFAULT 'draft',
    sent_to_email TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_invoice_number (invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE DATA: Test Orders
-- ============================================

INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, delivery_address, delivery_city, delivery_state, delivery_postal_code, subtotal, shipping_cost, order_total, payment_method, order_status, payment_status) VALUES
('ORD-2026-0001', 'Ahmed Hassan', 'ahmed@example.com', '01711223344', '123 Main Street', 'Dhaka', 'Dhaka Division', '1205', 15999.00, 200.00, 16199.00, 'cash_on_delivery', 'pending', 'pending'),
('ORD-2026-0002', 'Fatima Khan', 'fatima@example.com', '01912334455', '456 Park Road', 'Chittagong', 'Chittagong Division', '4000', 7999.00, 500.00, 8499.00, 'mobile_banking', 'processing', 'completed');

INSERT INTO order_items (order_id, product_id, product_name, product_sku, quantity, unit_price, item_total) VALUES
(1, 101, 'Wireless Headphones', 'WH-001', 2, 7999.00, 15998.00),
(2, 102, 'Smart Watch', 'SW-001', 1, 7999.00, 7999.00);

INSERT INTO coupons (coupon_code, description, discount_type, discount_value, min_purchase, start_date, end_date, status) VALUES
('WELCOME10', '10% discount on first order', 'percent', 10.00, 0.00, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'active'),
('SUMMER500', '500 BDT off on orders above 5000', 'fixed', 500.00, 5000.00, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active');
