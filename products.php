<?php
/**
 * Admin - Products Management Page
 * View and edit all products (price, stock)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Simple authentication
$is_authenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;

if (!$is_authenticated) {
    redirect(APP_URL . '/admin/index.php');
}

// Page-specific CSS files
$page_css = ['admin-page.css'];
$page_js = ['admin-page.js'];

$page_title = 'Products Management';
include __DIR__ . '/../../includes/header.php';

// Handle update
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    if ($product_id && $price > 0 && $stock >= 0) {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                UPDATE products 
                SET price = :price, stock = :stock 
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':price' => $price,
                ':stock' => $stock,
                ':id' => $product_id
            ]);

            $success_message = 'Product updated successfully!';
        } catch (Exception $e) {
            $error_message = 'Failed to update product: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Please fill in all required fields correctly';
    }
}

// Get all products
try {
    $pdo = Database::connect();
    $stmt = $pdo->query("SELECT id, name, price, stock, image FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
    $error_message = 'Failed to load products: ' . $e->getMessage();
}
?>

<section class="content active" style="padding: 2rem 1rem;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="margin: 0; color: #2d3748;">Products Management</h2>
                <p style="margin: 0.5rem 0 0 0; color: #718096;">View and edit all products</p>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="<?php echo APP_URL; ?>/admin/add-product.php" style="background: #48bb78; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    + Add Product
                </a>
                <a href="<?php echo APP_URL; ?>/admin/index.php" style="background: #667eea; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    ← Back to Orders
                </a>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div style="background: #f0fff4; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #48bb78;">
                ✅ <?php echo escape($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div style="background: #fff5f5; color: #742a2a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #f56565;">
                ❌ <?php echo escape($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p>No products found.</p>
                <a href="<?php echo APP_URL; ?>/admin/add-product.php" style="background: #48bb78; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 1rem;">
                    Add your first product
                </a>
            </div>
        <?php else: ?>
            <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <th style="padding: 1rem; text-align: left; min-width: 50px;">Image</th>
                                <th style="padding: 1rem; text-align: left; min-width: 150px;">Product Name</th>
                                <th style="padding: 1rem; text-align: right; min-width: 120px;">Price (৳)</th>
                                <th style="padding: 1rem; text-align: center; min-width: 100px;">Stock</th>
                                <th style="padding: 1rem; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;" id="product-row-<?php echo $product['id']; ?>">
                                <td style="padding: 1rem; text-align: center;">
                                    <img src="<?php echo APP_URL; ?>/admin/get-image.php?id=<?php echo escape($product['id']); ?>" 
                                         alt="<?php echo escape($product['name']); ?>" 
                                         style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 6px;">
                                </td>
                                <td style="padding: 1rem; color: #2d3748;"><?php echo escape($product['name']); ?></td>
                                <td style="padding: 1rem; text-align: right;">
                                    <span class="price-display" id="price-display-<?php echo $product['id']; ?>" onclick="editPrice(<?php echo $product['id']; ?>)" style="cursor: pointer; font-weight: 600; color: #667eea;">
                                        ৳ <?php echo number_format($product['price'], 0); ?>
                                    </span>
                                    <form class="price-form" id="price-form-<?php echo $product['id']; ?>" style="display: none; display: flex; gap: 0.5rem;">
                                        <input type="number" class="price-input" value="<?php echo escape($product['price']); ?>" min="1" step="0.01" style="width: 100px; padding: 0.5rem; border: 2px solid #667eea; border-radius: 4px; font-size: 0.9rem;">
                                        <button type="button" onclick="savePrice(<?php echo $product['id']; ?>, this.parentElement.querySelector('.price-input').value)" style="background: #48bb78; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: 600;">✓</button>
                                        <button type="button" onclick="cancelPrice(<?php echo $product['id']; ?>)" style="background: #cbd5e0; color: #2d3748; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: 600;">✕</button>
                                    </form>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span class="stock-display" id="stock-display-<?php echo $product['id']; ?>" onclick="editStock(<?php echo $product['id']; ?>)" style="cursor: pointer; font-weight: 600; color: <?php echo $product['stock'] > 10 ? '#48bb78' : ($product['stock'] > 0 ? '#ed8936' : '#f56565'); ?>;">
                                        <?php echo escape($product['stock']); ?>
                                    </span>
                                    <form class="stock-form" id="stock-form-<?php echo $product['id']; ?>" style="display: none; display: flex; gap: 0.5rem; justify-content: center;">
                                        <input type="number" class="stock-input" value="<?php echo escape($product['stock']); ?>" min="0" style="width: 80px; padding: 0.5rem; border: 2px solid #667eea; border-radius: 4px; font-size: 0.9rem;">
                                        <button type="button" onclick="saveStock(<?php echo $product['id']; ?>, this.parentElement.querySelector('.stock-input').value)" style="background: #48bb78; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: 600;">✓</button>
                                        <button type="button" onclick="cancelStock(<?php echo $product['id']; ?>)" style="background: #cbd5e0; color: #2d3748; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: 600;">✕</button>
                                    </form>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <button onclick="deleteProduct(<?php echo $product['id']; ?>)" style="background: #f56565; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 0.9rem;">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 2rem; padding: 1rem; background: #f7fafc; border-radius: 8px; border-left: 4px solid #667eea;">
                <p style="margin: 0; color: #2d3748; font-weight: 600;">💡 Tip: Click on any price or stock value to edit it directly</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<form id="updateForm" method="POST" style="display: none;">
    <input type="hidden" name="update_product" value="1">
    <input type="hidden" name="product_id" id="updateProductId">
    <input type="hidden" name="price" id="updatePrice">
    <input type="hidden" name="stock" id="updateStock">
</form>

<script>
function editPrice(productId) {
    document.getElementById('price-display-' + productId).style.display = 'none';
    document.getElementById('price-form-' + productId).style.display = 'flex';
    document.querySelector('#price-form-' + productId + ' .price-input').focus();
}

function cancelPrice(productId) {
    document.getElementById('price-display-' + productId).style.display = 'inline';
    document.getElementById('price-form-' + productId).style.display = 'none';
}

function savePrice(productId, newPrice) {
    if (newPrice > 0) {
        document.getElementById('updateProductId').value = productId;
        document.getElementById('updatePrice').value = newPrice;
        document.getElementById('updateStock').value = document.querySelector('[data-stock="' + productId + '"]')?.value || 0;
        
        // For inline update, we'll submit via AJAX
        fetch('<?php echo APP_URL; ?>/admin/products-api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'update_price', product_id: productId, price: newPrice})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    } else {
        alert('Please enter a valid price');
    }
}

function editStock(productId) {
    document.getElementById('stock-display-' + productId).style.display = 'none';
    document.getElementById('stock-form-' + productId).style.display = 'flex';
    document.querySelector('#stock-form-' + productId + ' .stock-input').focus();
}

function cancelStock(productId) {
    document.getElementById('stock-display-' + productId).style.display = 'inline';
    document.getElementById('stock-form-' + productId).style.display = 'none';
}

function saveStock(productId, newStock) {
    if (newStock >= 0) {
        fetch('<?php echo APP_URL; ?>/admin/products-api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'update_stock', product_id: productId, stock: newStock})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    } else {
        alert('Please enter a valid stock quantity');
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        fetch('<?php echo APP_URL; ?>/admin/products-api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'delete', product_id: productId})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('product-row-' + productId).style.opacity = '0.5';
                setTimeout(() => location.reload(), 300);
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
