<?php
/**
 * Admin - Add Product Page
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
// $page_css = ['admin-page.css'];
// $page_js = ['admin-page.js'];

$page_title = 'Add Product';
include __DIR__ . '/../../includes/header.php';

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $image_data = null;

    // Handle image upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['size'] > 0) {
        $file = $_FILES['image_file'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed)) {
            $error_message = 'Invalid image format. Please upload JPG, PNG, GIF, or WebP.';
        } elseif ($file['size'] > $max_size) {
            $error_message = 'Image too large. Maximum size is 5MB.';
        } else {
            // Read image file as binary
            $image_data = file_get_contents($file['tmp_name']);
        }
    }

    if (!$error_message && $name && $price > 0 && $stock >= 0 && $image_data) {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, stock, image)
                VALUES (:name, :description, :price, :stock, :image)
            ");
            
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':stock' => $stock,
                ':image' => $image_data
            ]);

            $success_message = "Product '{$name}' added successfully!";
            
            // Clear form
            $_POST = [];
        } catch (Exception $e) {
            $error_message = 'Failed to add product: ' . $e->getMessage();
        }
    } elseif (!$error_message) {
        $error_message = 'Please fill in all required fields and upload an image';
    }
}
?>

<section class="content active" style="padding: 2rem 1rem;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 style="margin: 0; color: #2d3748;">Add New Product</h2>
                <p style="margin: 0.5rem 0 0 0; color: #718096;">Create a new product listing</p>
            </div>
            <a href="<?php echo APP_URL; ?>/admin/index.php" style="background: #667eea; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                ← Back to Orders
            </a>
        </div>

        <?php if ($success_message): ?>
            <div style="background: #f0fff4; color: #22543d; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #48bb78; display: flex; justify-content: space-between; align-items: center;">
                <span>✅ <?php echo escape($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div style="background: #fff5f5; color: #742a2a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #f56565;">
                ❌ <?php echo escape($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <fieldset style="border: none; padding: 0; margin: 0;">
                <legend style="display: none;">Product Information</legend>

                <!-- Product Name -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                        Product Name <span style="color: #f56565;">*</span>
                    </label>
                    <input type="text" id="name" name="name" required placeholder="e.g., Samsung Galaxy S21" 
                           value="<?php echo escape($_POST['name'] ?? ''); ?>"
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem; box-sizing: border-box;">
                </div>

                <!-- Description -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="description" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                        Description <span style="color: #f56565;">*</span>
                    </label>
                    <textarea id="description" name="description" required placeholder="Product description..." rows="4"
                              style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem; resize: vertical; box-sizing: border-box;"><?php echo escape($_POST['description'] ?? ''); ?></textarea>
                </div>

                <!-- Price -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="price" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                        Price (৳) <span style="color: #f56565;">*</span>
                    </label>
                    <input type="number" id="price" name="price" required min="1" step="0.01" placeholder="e.g., 45000" 
                           value="<?php echo escape($_POST['price'] ?? ''); ?>"
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem; box-sizing: border-box;">
                </div>

                <!-- Stock -->
                <div style="margin-bottom: 1.5rem;">
                    <label for="stock" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                        Stock Quantity <span style="color: #f56565;">*</span>
                    </label>
                    <input type="number" id="stock" name="stock" required min="0" placeholder="e.g., 50" 
                           value="<?php echo escape($_POST['stock'] ?? ''); ?>"
                           style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem; box-sizing: border-box;">
                </div>

                <!-- Image Upload -->
                <div style="margin-bottom: 2rem;">
                    <label for="image_file" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                        Upload Product Image <span style="color: #a0aec0;">(Optional - JPG, PNG, GIF, WebP up to 5MB)</span>
                    </label>
                    <div style="position: relative; border: 2px dashed #cbd5e0; border-radius: 8px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; background: #f7fafc;"
                         onclick="document.getElementById('image_file').click()"
                         onmouseover="this.style.borderColor='#667eea'; this.style.background='#f0f4ff';"
                         onmouseout="this.style.borderColor='#cbd5e0'; this.style.background='#f7fafc';">
                        <input type="file" id="image_file" name="image_file" accept="image/*" style="display: none;" onchange="updateFileName(this)">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📸</div>
                        <p style="margin: 0; color: #2d3748; font-weight: 600;">Click to upload or drag and drop</p>
                        <p style="margin: 0.5rem 0 0 0; color: #718096; font-size: 0.9rem;">Supported: JPG, PNG, GIF, WebP</p>
                        <p id="fileName" style="margin: 0.5rem 0 0 0; color: #667eea; font-weight: 600; display: none;"></p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="<?php echo APP_URL; ?>/admin/index.php" style="padding: 0.75rem 1.5rem; background: #e2e8f0; color: #2d3748; border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;">
                        Cancel
                    </a>
                    <button type="submit" name="add_product" value="1" style="padding: 0.75rem 2rem; background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.2s;">
                        ✅ Add Product
                    </button>
                </div>
            </fieldset>
        </form>

        <!-- Recent Products -->
        <?php
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query("SELECT id, name, price, stock FROM products ORDER BY created_at DESC LIMIT 5");
            $recent_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $recent_products = [];
        }
        ?>

        <?php if (!empty($recent_products)): ?>
        <div style="margin-top: 3rem;">
            <h3 style="color: #2d3748; margin-bottom: 1rem;">📦 Recent Products</h3>
            <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f7fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 1rem; text-align: left; color: #2d3748; font-weight: 600;">Image</th>
                                <th style="padding: 1rem; text-align: left; color: #2d3748; font-weight: 600;">Product Name</th>
                                <th style="padding: 1rem; text-align: right; color: #2d3748; font-weight: 600;">Price</th>
                                <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_products as $product): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem;">
                                    <img src="<?php echo APP_URL; ?>/admin/get-image.php?id=<?php echo escape($product['id']); ?>" 
                                         alt="<?php echo escape($product['name']); ?>" 
                                         style="max-width: 50px; max-height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td style="padding: 1rem; color: #2d3748;"><?php echo escape($product['name']); ?></td>
                                <td style="padding: 1rem; text-align: right; font-weight: 600; color: #667eea;">৳ <?php echo number_format($product['price'], 0); ?></td>
                                <td style="padding: 1rem; text-align: center; color: #718096;"><?php echo escape($product['stock']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = '✓ Selected: ' + input.files[0].name;
        fileName.style.display = 'block';
    } else {
        fileName.style.display = 'none';
    }
}

// Drag and drop functionality
const dropZone = document.querySelector('div[onmouseover]');
if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.borderColor = '#667eea';
        dropZone.style.background = '#f0f4ff';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#cbd5e0';
        dropZone.style.background = '#f7fafc';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('image_file').files = files;
            updateFileName(document.getElementById('image_file'));
        }
    });
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
