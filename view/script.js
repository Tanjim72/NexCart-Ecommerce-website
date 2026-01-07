// Initialize session
if (!localStorage.getItem('nexcart_session')) {
    localStorage.setItem('nexcart_session', Date.now());
}

// Load demo products
function initProducts() {
    if (!localStorage.getItem('products')) {
        const demoProducts = [
            {id: 1, name: "Smartphone", price: 299, stock: 10, category: "electronics"},
            {id: 2, name: "T-Shirt", price: 25, stock: 50, category: "clothing"},
            {id: 3, name: "Laptop", price: 899, stock: 5, category: "electronics"}
        ];
        localStorage.setItem('products', JSON.stringify(demoProducts));
    }
}

// Dashboard stats
function updateStats() {
    const products = JSON.parse(localStorage.getItem('products') || '[]');
    const lowStock = products.filter(p => p.stock < 10).length;
    
    document.getElementById('total-products')?.textContent = products.length;
    document.getElementById('low-stock')?.textContent = lowStock;
}

// Load product catalog
function loadCatalog() {
    const products = JSON.parse(localStorage.getItem('products') || '[]');
    const grid = document.getElementById('product-grid');
    if (!grid) return;
    
    grid.innerHTML = products.map(p => `
        <div class="product-card">
            <h3>${p.name}</h3>
            <p>Price: $${p.price}</p>
            <p>Stock: ${p.stock}</p>
            <a href="product_details.php?id=${p.id}">View Details</a>
        </div>
    `).join('');
}

// Search products
function searchProducts() {
    const search = document.getElementById('search-box')?.value.toLowerCase() || '';
    const min = parseFloat(document.getElementById('min-price')?.value) || 0;
    const max = parseFloat(document.getElementById('max-price')?.value) || Infinity;
    
    const products = JSON.parse(localStorage.getItem('products') || '[]');
    const results = products.filter(p => 
        p.name.toLowerCase().includes(search) && 
        p.price >= min && 
        p.price <= max
    );
    
    const container = document.getElementById('search-results');
    if (container) {
        container.innerHTML = results.map(p => `
            <div class="product-card">
                <h3>${p.name}</h3>
                <p>$${p.price}</p>
            </div>
        `).join('');
    }
}

// Load product details
function loadProductDetails(id) {
    const products = JSON.parse(localStorage.getItem('products') || '[]');
    const product = products.find(p => p.id == id);
    
    if (product) {
        document.getElementById('product-name').textContent = product.name;
        document.getElementById('product-price').textContent = `Price: $${product.price}`;
        document.getElementById('product-stock').textContent = `Stock: ${product.stock}`;
    }
}

// Add new product
function addProduct() {
    const name = document.getElementById('product-name').value;
    const price = parseFloat(document.getElementById('product-price').value);
    const stock = parseInt(document.getElementById('product-stock').value);
    
    if (!name || price <= 0 || stock < 0) {
        alert('Please fill all fields correctly');
        return false;
    }
    
    const products = JSON.parse(localStorage.getItem('products') || '[]');
    const newId = products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
    
    products.push({
        id: newId,
        name: name,
        price: price,
        stock: stock,
        category: "general"
    });
    
    localStorage.setItem('products', JSON.stringify(products));
    alert('Product added successfully!');
    return false;
}

// Update stock
function updateStock() {
    const quantity = parseInt(document.getElementById('stock-quantity').value);
    // In real app, you would update specific product
    alert(`Stock updated by ${quantity} units`);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initProducts();
    updateStats();
    loadCatalog();
});