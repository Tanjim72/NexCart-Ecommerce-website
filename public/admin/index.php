<?php
/**
 * Admin Dashboard / Orders Listing
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Simple authentication (in production, use proper auth system)
$is_authenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Verify credentials (in production, query database)
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_authenticated'] = true;
        session_regenerate_id(true);
        redirect(APP_URL . '/admin/index.php');
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect(APP_URL . '/admin/index.php');
}

// Page-specific CSS files (for admin pages) - Now consolidated in common.css
// $page_css = ['admin-page.css'];
// Page-specific JS files (for admin pages) - Now consolidated in utilities.js
// $page_js = ['admin-page.js'];

// If not authenticated, show login form
if (!$is_authenticated) {
    $page_title = 'Admin Login';
    include __DIR__ . '/../../includes/header.php';
    ?>

    <section class="content active" style="padding: 3rem 1rem;">
        <div style="max-width: 400px; margin: 3rem auto; background: #f7fafc; padding: 2rem; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #667eea;">Admin Login</h2>
            
            <?php if (isset($login_error)): ?>
                <div style="background: #fff5f5; color: #f56565; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #f56565;">
                    <?php echo escape($login_error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 1.5rem;">
                    <label for="username" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">Username</label>
                    <input type="text" id="username" name="username" required placeholder="admin" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 2rem;">
                    <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem;">
                </div>

                <button type="submit" name="login" value="1" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                    Login
                </button>
            </form>

            <p style="text-align: center; margin-top: 1rem; color: #718096; font-size: 0.9rem;">
                Demo: username <strong>admin</strong>, password <strong>admin123</strong>
            </p>
        </div>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php';
    exit;
}

// Authenticated - show dashboard
$page_title = 'Admin Dashboard';
include __DIR__ . '/../../includes/header.php';

// Get pagination
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $per_page;
$total_orders = countOrders();
$total_pages = ceil($total_orders / $per_page);

// Get orders for current page
$orders = getOrders($per_page, $offset);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $new_status = sanitize($_POST['new_status'] ?? '');
    
    if ($order_id && $new_status) {
        updateOrderStatus($order_id, $new_status);
        redirect(APP_URL . '/admin/index.php?page=' . $page);
    }
}
?>

<section class="content active" style="padding: 2rem 1rem;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin: 0; color: #2d3748;">Orders Management</h2>
                <p style="margin: 0.5rem 0 0 0; color: #718096;">Total Orders: <?php echo escape($total_orders); ?></p>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?php echo APP_URL; ?>/admin/products.php" style="background: #9f7aea; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    📦 Manage Products
                </a>
                <a href="<?php echo APP_URL; ?>/admin/add-product.php" style="background: #48bb78; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    + Add Product
                </a>
                <a href="<?php echo APP_URL; ?>/admin/index.php?logout=1" style="background: #f56565; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    Logout
                </a>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p>No orders yet.</p>
            </div>
        <?php else: ?>
            <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <th style="padding: 1rem; text-align: left;">Order ID</th>
                                <th style="padding: 1rem; text-align: left;">Customer</th>
                                <th style="padding: 1rem; text-align: left;">Email</th>
                                <th style="padding: 1rem; text-align: right;">Total</th>
                                <th style="padding: 1rem; text-align: center;">Status</th>
                                <th style="padding: 1rem; text-align: left;">Date</th>
                                <th style="padding: 1rem; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem; font-weight: 600; color: #667eea;">#<?php echo escape(str_pad($order['id'], 6, '0', STR_PAD_LEFT)); ?></td>
                                <td style="padding: 1rem;"><?php echo escape($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                <td style="padding: 1rem; font-size: 0.9rem; color: #718096;"><?php echo escape($order['email']); ?></td>
                                <td style="padding: 1rem; text-align: right; font-weight: 600;">৳ <?php echo number_format($order['order_total'], 2); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span class="status-badge <?php echo escape(strtolower($order['status'])); ?>"><?php echo escape(ucfirst($order['status'])); ?></span>
                                </td>
                                <td style="padding: 1rem; font-size: 0.9rem; color: #718096;"><?php echo escape(date('M d, Y', strtotime($order['created_at']))); ?></td>
                                <td style="padding: 1rem; text-align: center;">
                                    <a href="<?php echo APP_URL; ?>/admin/orders.php?id=<?php echo escape($order['id']); ?>" style="color: #667eea; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo APP_URL; ?>/admin/index.php?page=<?php echo $i; ?>" 
                       style="<?php echo $i === $page ? 'background: #667eea; color: white;' : 'background: #e2e8f0; color: #2d3748;'; ?> padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
