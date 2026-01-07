<?php
/**
 * Database Configuration Sample File
 * 
 * IMPORTANT: Copy this file to config/config.php and update with your credentials.
 * DO NOT commit config.php to version control!
 */

// Database Configuration
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'nextcart';
const DB_PORT = 3306;

// Application Configuration
const APP_NAME = 'NextCart';
const APP_URL = 'http://localhost/Prii_ecomerce/public';
const TIMEZONE = 'Asia/Dhaka';

// Session Configuration
const SESSION_LIFETIME = 3600; // 1 hour in seconds
const SECURE_COOKIES = false;  // Set to true in production with HTTPS
const HTTPONLY_COOKIES = true;
const SAMESITE_COOKIES = 'Lax'; // 'Lax', 'Strict', or 'None'

// Email Configuration (optional)
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_USER = '';
const SMTP_PASS = '';
const FROM_EMAIL = 'noreply@nextcart.local';

// Admin Configuration
const ADMIN_USERNAME = 'admin';
const ADMIN_PASSWORD = 'admin123'; // Change this in production!
