<?php
/**
 * Product Creation Form for Magento 2
 * Location: pub/create-group-product.php
 * Updated to create Multiple Simple Products and Grouped Product
 */

// Use only for development - remove in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Magento Bootstrap
    require_once __DIR__ . '/../app/bootstrap.php';
    
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    
    // Set area code
    $state = $objectManager->get(\Magento\Framework\App\State::class);
    $state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px;'>Magento Bootstrap Error: " . $e->getMessage() . "</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createProducts($objectManager, $_POST);
    
    if ($result['success']) {
        echo "<div style='color: green; padding: 20px; border: 2px solid green; border-radius: 5px; margin: 20px;'>";
        echo "<h3>✅ Products Created Successfully!</h3>";
        
        // Simple Products Summary
        if (!empty($result['simple_products'])) {
            echo "<div style='margin-top: 20px;'>";
            echo "<h4>Created Simple Products:</h4>";
            echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; margin-top: 10px;'>";
            
            foreach ($result['simple_products'] as $simpleProduct) {
                echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; border-left: 4px solid #2e7d32;'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($simpleProduct['name']) . "</p>";
                echo "<p><strong>SKU:</strong> " . htmlspecialchars($simpleProduct['sku']) . "</p>";
                echo "<p><strong>Price:</strong> $" . number_format($simpleProduct['price'], 2) . "</p>";
                echo "<p><strong>ID:</strong> " . $simpleProduct['id'] . "</p>";
                echo "<p><strong>URL:</strong> <a href='" . htmlspecialchars($simpleProduct['url']) . "' target='_blank' style='color: #0066cc;'>View Product</a></p>";
                echo "</div>";
            }
            
            echo "</div>";
            echo "</div>";
        }
        
        // Grouped Product Info
        if ($result['grouped_product_id']) {
            echo "<div style='margin-top: 20px; background: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 4px solid #1565c0;'>";
            echo "<h4 style='margin-top: 0; color: #1565c0;'>Grouped Product Created:</h4>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($result['grouped_product_name']) . "</p>";
            echo "<p><strong>SKU:</strong> " . htmlspecialchars($result['grouped_product_sku']) . "</p>";
            echo "<p><strong>ID:</strong> " . $result['grouped_product_id'] . "</p>";
            echo "<p><strong>URL Key:</strong> " . htmlspecialchars($result['grouped_product_url']) . "</p>";
            echo "<p><strong>URL:</strong> <a href='" . htmlspecialchars($result['grouped_product_url_full']) . "' target='_blank' style='color: #0066cc;'>View Grouped Product</a></p>";
            
            // Associated Products
            if (!empty($result['associated_products'])) {
                echo "<p><strong>Associated Products:</strong> " . count($result['associated_products']) . " simple product(s)</p>";
                echo "<ul>";
                foreach ($result['associated_products'] as $associated) {
                    echo "<li>" . htmlspecialchars($associated['name']) . " (SKU: " . htmlspecialchars($associated['sku']) . ", Qty: " . $associated['qty'] . ")</li>";
                }
                echo "</ul>";
            }
            
            echo "</div>";
        }
        
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 20px; border: 2px solid red; border-radius: 5px; margin: 20px;'>❌ Error: " . $result['message'] . "</div>";
    }
}

// Get categories for dropdown
$categories = getCategories($objectManager);

// Display the form
displayForm($categories);

/**
 * Display the product creation form
 */
function displayForm($categories) {
    // Same form HTML as before - unchanged
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Magento Products</title>
        <style>
            /* Same CSS as before - unchanged */
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            }
            
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            
            .container {
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                width: 100%;
                max-width: 900px;
                margin: 0 auto;
                overflow: hidden;
            }
            
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            
            .header h1 {
                font-size: 28px;
                font-weight: 600;
                margin-bottom: 8px;
            }
            
            .header p {
                opacity: 0.9;
                font-size: 15px;
            }
            
            .form-container {
                padding: 40px;
            }
            
            .form-section {
                background: #f8fafc;
                border-radius: 10px;
                padding: 25px;
                margin-bottom: 25px;
                border-left: 4px solid #667eea;
            }
            
            .form-section h3 {
                color: #667eea;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 18px;
            }
            
            .form-section h3 svg {
                width: 20px;
                height: 20px;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: #4a5568;
                font-weight: 500;
                font-size: 14px;
            }
            
            .form-control {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e2e8f0;
                border-radius: 8px;
                font-size: 15px;
                transition: all 0.3s ease;
                background: white;
            }
            
            .form-control:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }
            
            select.form-control {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%234a5568' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 16px center;
                background-size: 16px;
                padding-right: 40px;
            }
            
            .btn {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 15px 30px;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
            
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            }
            
            .btn-secondary {
                background: #6c757d;
            }
            
            .btn-secondary:hover {
                background: #5a6268;
                box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
            }
            
            .btn-danger {
                background: #dc3545;
            }
            
            .btn-danger:hover {
                background: #c82333;
                box-shadow: 0 10px 25px rgba(220, 53, 69, 0.3);
            }
            
            .btn svg {
                width: 20px;
                height: 20px;
            }
            
            .required {
                color: #f56565;
            }
            
            .info-box {
                background: #e6f7ff;
                border: 1px solid #91d5ff;
                border-radius: 8px;
                padding: 15px;
                margin-top: 20px;
                font-size: 14px;
                color: #0050b3;
            }
            
            .simple-product-row {
                background: white;
                border: 2px solid #e2e8f0;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                position: relative;
            }
            
            .simple-product-row:last-child {
                margin-bottom: 0;
            }
            
            .row-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #e2e8f0;
            }
            
            .row-title {
                font-weight: 600;
                color: #4a5568;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .row-number {
                background: #667eea;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }
            
            .remove-row-btn {
                background: #f56565;
                color: white;
                border: none;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
            }
            
            .remove-row-btn:hover {
                background: #e53e3e;
            }
            
            .products-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
            
            @media (max-width: 768px) {
                .products-grid {
                    grid-template-columns: 1fr;
                }
            }
            
            .form-actions {
                display: flex;
                gap: 15px;
                margin-top: 30px;
            }
            
            .form-actions .btn {
                flex: 1;
            }
            
            @media (max-width: 650px) {
                .container {
                    margin: 10px;
                }
                
                .header, .form-container {
                    padding: 25px;
                }
                
                .form-section {
                    padding: 20px;
                }
                
                .form-actions {
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Create Multiple Products</h1>
                <p>Create Multiple Simple Products and a Grouped Product</p>
            </div>
            
            <div class="form-container">
                <form method="POST" action="" id="productForm">
                    
                    <!-- Simple Products Section -->
                    <div class="form-section" id="simpleProductsSection">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 12h8"></path>
                                <path d="M12 8v8"></path>
                            </svg>
                            Simple Products (Add Multiple)
                        </h3>
                        
                        <div id="simpleProductsContainer">
                            <!-- Product Row 1 -->
                            <div class="simple-product-row" data-row="1">
                                <div class="row-header">
                                    <div class="row-title">
                                        <div class="row-number">1</div>
                                        Simple Product #1
                                    </div>
                                    <button type="button" class="remove-row-btn" onclick="removeProductRow(1)" style="display: none;">×</button>
                                </div>
                                <div class="products-grid">
                                    <div class="form-group">
                                        <label for="product_name_1">Product Name <span class="required">*</span></label>
                                        <input type="text" 
                                               id="product_name_1" 
                                               name="products[1][name]" 
                                               class="form-control product-name" 
                                               placeholder="e.g., Product A"
                                               required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sku_1">SKU <span class="required">*</span></label>
                                        <input type="text" 
                                               id="sku_1" 
                                               name="products[1][sku]" 
                                               class="form-control product-sku" 
                                               placeholder="e.g., product-a-001"
                                               required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="price_1">Price ($) <span class="required">*</span></label>
                                        <input type="number" 
                                               id="price_1" 
                                               name="products[1][price]" 
                                               class="form-control product-price" 
                                               step="0.01"
                                               min="0.01"
                                               value="19.99"
                                               required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="qty_1">Quantity in Group</label>
                                        <input type="number" 
                                               id="qty_1" 
                                               name="products[1][group_qty]" 
                                               class="form-control group-qty" 
                                               min="1"
                                               value="1">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description_1">Description (Optional)</label>
                                    <textarea id="description_1" 
                                              name="products[1][description]" 
                                              class="form-control" 
                                              placeholder="Product description"
                                              rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 20px;">
                            <button type="button" class="btn btn-secondary" onclick="addProductRow()" style="width: auto; padding: 10px 20px;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Another Simple Product
                            </button>
                        </div>
                    </div>
                    
                    <!-- Grouped Product Section -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="3" x2="9" y2="21"></line>
                                <line x1="15" y1="3" x2="15" y2="21"></line>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="3" y1="15" x2="21" y2="15"></line>
                            </svg>
                            Grouped Product Details
                        </h3>
                        
                        <div class="products-grid">
                            <div class="form-group">
                                <label for="grouped_product_name">Grouped Product Name <span class="required">*</span></label>
                                <input type="text" 
                                       id="grouped_product_name" 
                                       name="grouped_product_name" 
                                       class="form-control" 
                                       placeholder="e.g., Product Bundle Pack"
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="grouped_sku">Grouped Product SKU <span class="required">*</span></label>
                                <input type="text" 
                                       id="grouped_sku" 
                                       name="grouped_sku" 
                                       class="form-control" 
                                       placeholder="e.g., grouped-bundle-001"
                                       required>
                            </div>
                        </div>
                        
                        <div class="products-grid">
                            <div class="form-group">
                                <label for="grouped_url">Grouped Product URL Key</label>
                                <input type="text" 
                                       id="grouped_url" 
                                       name="grouped_url" 
                                       class="form-control" 
                                       placeholder="e.g., product-bundle-pack">
                                <small style="color: #6c757d; font-size: 12px; margin-top: 5px; display: block;">
                                    Leave empty for auto-generation
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label for="grouped_description">Grouped Product Description</label>
                                <textarea id="grouped_description" 
                                          name="grouped_description" 
                                          class="form-control" 
                                          placeholder="Description for the grouped product"
                                          rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Category Section -->
                    <div class="form-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                            Category Selection
                        </h3>
                        
                        <div class="form-group">
                            <label for="category_id">Category for All Products <span class="required">*</span></label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['value']); ?>">
                                        <?php echo htmlspecialchars($category['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <strong>ℹ️ Information:</strong> 
                        <span>This will create all simple products listed above and then create a grouped product containing all of them. Each simple product can have its own quantity in the grouped product.</span>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"/>
                            </svg>
                            Create All Products
                        </button>
                        
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
            // Same JavaScript as before - unchanged
            let productRowCount = 1;
            
            document.addEventListener('DOMContentLoaded', function() {
                updateRowNumbers();
            });
            
            function addProductRow() {
                productRowCount++;
                const container = document.getElementById('simpleProductsContainer');
                const newRow = document.createElement('div');
                newRow.className = 'simple-product-row';
                newRow.setAttribute('data-row', productRowCount);
                
                newRow.innerHTML = `
                    <div class="row-header">
                        <div class="row-title">
                            <div class="row-number">${productRowCount}</div>
                            Simple Product #${productRowCount}
                        </div>
                        <button type="button" class="remove-row-btn" onclick="removeProductRow(${productRowCount})">×</button>
                    </div>
                    <div class="products-grid">
                        <div class="form-group">
                            <label for="product_name_${productRowCount}">Product Name <span class="required">*</span></label>
                            <input type="text" 
                                   id="product_name_${productRowCount}" 
                                   name="products[${productRowCount}][name]" 
                                   class="form-control product-name" 
                                   placeholder="e.g., Product ${String.fromCharCode(64 + productRowCount)}"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sku_${productRowCount}">SKU <span class="required">*</span></label>
                            <input type="text" 
                                   id="sku_${productRowCount}" 
                                   name="products[${productRowCount}][sku]" 
                                   class="form-control product-sku" 
                                   placeholder="e.g., product-${String.fromCharCode(96 + productRowCount)}-001"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price_${productRowCount}">Price ($) <span class="required">*</span></label>
                            <input type="number" 
                                   id="price_${productRowCount}" 
                                   name="products[${productRowCount}][price]" 
                                   class="form-control product-price" 
                                   step="0.01"
                                   min="0.01"
                                   value="19.99"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="qty_${productRowCount}">Quantity in Group</label>
                            <input type="number" 
                                   id="qty_${productRowCount}" 
                                   name="products[${productRowCount}][group_qty]" 
                                   class="form-control group-qty" 
                                   min="1"
                                   value="1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description_${productRowCount}">Description (Optional)</label>
                        <textarea id="description_${productRowCount}" 
                                  name="products[${productRowCount}][description]" 
                                  class="form-control" 
                                  placeholder="Product description"
                                  rows="2"></textarea>
                    </div>
                `;
                
                container.appendChild(newRow);
                updateRowNumbers();
            }
            
            function removeProductRow(rowNumber) {
                const row = document.querySelector(`.simple-product-row[data-row="${rowNumber}"]`);
                if (row) {
                    row.remove();
                    updateRowNumbers();
                }
            }
            
            function updateRowNumbers() {
                const rows = document.querySelectorAll('.simple-product-row');
                rows.forEach((row, index) => {
                    const rowNumber = index + 1;
                    row.setAttribute('data-row', rowNumber);
                    row.querySelector('.row-number').textContent = rowNumber;
                    row.querySelector('.row-title').innerHTML = `
                        <div class="row-number">${rowNumber}</div>
                        Simple Product #${rowNumber}
                    `;
                    
                    // Update input names and IDs
                    const inputs = row.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        const id = input.getAttribute('id');
                        
                        if (name && name.includes('products[')) {
                            const newName = name.replace(/products\[\d+\]/, `products[${rowNumber}]`);
                            input.setAttribute('name', newName);
                        }
                        
                        if (id && id.match(/_\d+$/)) {
                            const newId = id.replace(/_\d+$/, `_${rowNumber}`);
                            input.setAttribute('id', newId);
                        }
                    });
                    
                    // Update label for attributes
                    const labels = row.querySelectorAll('label');
                    labels.forEach(label => {
                        const forAttr = label.getAttribute('for');
                        if (forAttr && forAttr.match(/_\d+$/)) {
                            const newFor = forAttr.replace(/_\d+$/, `_${rowNumber}`);
                            label.setAttribute('for', newFor);
                        }
                    });
                    
                    // Show/hide remove button
                    const removeBtn = row.querySelector('.remove-row-btn');
                    if (rows.length > 1) {
                        removeBtn.style.display = 'flex';
                        removeBtn.setAttribute('onclick', `removeProductRow(${rowNumber})`);
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
                
                productRowCount = rows.length;
            }
            
            function resetForm() {
                if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                    document.getElementById('productForm').reset();
                    
                    // Remove all but first product row
                    const container = document.getElementById('simpleProductsContainer');
                    const rows = container.querySelectorAll('.simple-product-row');
                    rows.forEach((row, index) => {
                        if (index > 0) {
                            row.remove();
                        }
                    });
                    
                    productRowCount = 1;
                    updateRowNumbers();
                    
                    // Reset grouped product name and SKU
                    document.getElementById('grouped_product_name').value = '';
                    document.getElementById('grouped_sku').value = '';
                    document.getElementById('grouped_url').value = '';
                    document.getElementById('grouped_description').value = '';
                    document.getElementById('category_id').value = '';
                }
            }
            
            // Validate form before submission
            document.getElementById('productForm').addEventListener('submit', function(e) {
                const productRows = document.querySelectorAll('.simple-product-row');
                let isValid = true;
                const skus = new Set();
                
                productRows.forEach(row => {
                    const skuInput = row.querySelector('.product-sku');
                    const priceInput = row.querySelector('.product-price');
                    
                    // Check for duplicate SKUs
                    if (skuInput.value) {
                        if (skus.has(skuInput.value.toLowerCase())) {
                            alert(`Duplicate SKU found: ${skuInput.value}. Please use unique SKUs for each product.`);
                            isValid = false;
                            skuInput.focus();
                        }
                        skus.add(skuInput.value.toLowerCase());
                    }
                    
                    // Validate price
                    if (priceInput.value <= 0) {
                        alert('Price must be greater than 0.');
                        isValid = false;
                        priceInput.focus();
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        </script>
    </body>
    </html>
    <?php
}

/**
 * Get categories for dropdown
 */
function getCategories($objectManager) {
    $categories = [];
    
    try {
        $categoryCollection = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Category\Collection::class);
        $categoryCollection->addAttributeToSelect('name')
            ->addAttributeToFilter('level', ['gteq' => 2])
            ->addAttributeToSort('path')
            ->setPageSize(50);
        
        foreach ($categoryCollection as $category) {
            $indent = str_repeat('--', $category->getLevel() - 2);
            $categories[] = [
                'value' => $category->getId(),
                'label' => $indent . ($indent ? ' ' : '') . $category->getName()
            ];
        }
    } catch (Exception $e) {
        // Return empty array if error
    }
    
    return $categories;
}

/**
 * Create products function - Creates Multiple Simple Products and Grouped Product
 */
function createProducts($objectManager, $postData) {
    try {
        // Validate required fields
        if (empty($postData['category_id'])) {
            throw new Exception('Category is required');
        }
        
        if (empty($postData['products']) || !is_array($postData['products'])) {
            throw new Exception('At least one simple product is required');
        }
        
        if (empty($postData['grouped_product_name']) || empty($postData['grouped_sku'])) {
            throw new Exception('Grouped Product Name and SKU are required');
        }
        
        // Get services
        $productFactory = $objectManager->create(\Magento\Catalog\Model\ProductFactory::class);
        $productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        
        // Check for duplicate SKUs
        $allSkus = [];
        foreach ($postData['products'] as $productData) {
            if (empty($productData['name']) || empty($productData['sku']) || empty($productData['price'])) {
                throw new Exception('All simple products must have Name, SKU, and Price');
            }
            
            $sku = trim($productData['sku']);
            if (in_array(strtolower($sku), array_map('strtolower', $allSkus))) {
                throw new Exception("Duplicate SKU found: {$sku}. Please use unique SKUs.");
            }
            $allSkus[] = $sku;
        }
        
        // Check if grouped SKU already exists
        try {
            $productRepository->get($postData['grouped_sku']);
            throw new Exception("Grouped product SKU '{$postData['grouped_sku']}' already exists");
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Good, doesn't exist
        }
        
        // Check if simple product SKUs already exist
        $existingSkus = [];
        foreach ($postData['products'] as $productData) {
            try {
                $productRepository->get($productData['sku']);
                $existingSkus[] = $productData['sku'];
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // SKU doesn't exist
            }
        }
        
        if (!empty($existingSkus)) {
            throw new Exception("Some SKUs already exist: " . implode(', ', $existingSkus));
        }
        
        $createdSimpleProducts = [];
        $associatedProducts = [];
        
        // Step 1: Create All Simple Products
        foreach ($postData['products'] as $index => $productData) {
            $simpleProduct = $productFactory->create();
            
            // Set Simple Product basic attributes
            $simpleProduct->setSku(trim($productData['sku']));
            $simpleProduct->setName(trim($productData['name']));
            $simpleProduct->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
            $simpleProduct->setAttributeSetId(4); // Default attribute set
            $simpleProduct->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE);
            $simpleProduct->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            
            // Set price
            $price = is_numeric($productData['price']) ? (float)$productData['price'] : 19.99;
            $simpleProduct->setPrice($price);
            
            // Set other attributes
            $simpleProduct->setWeight(1);
            $simpleProduct->setTaxClassId(2); // Taxable Goods
            
            // Set description if provided
            if (!empty($productData['description'])) {
                $simpleProduct->setDescription(trim($productData['description']));
                $simpleProduct->setShortDescription(substr(trim($productData['description']), 0, 255));
            }
            
            // Set category
            $simpleProduct->setCategoryIds([$postData['category_id']]);
            
            // Set website IDs
            $websiteIds = [1]; // Default
            try {
                $websites = $storeManager->getWebsites();
                $websiteIds = [];
                foreach ($websites as $website) {
                    $websiteIds[] = $website->getId();
                }
            } catch (Exception $e) {
                // Use default
            }
            $simpleProduct->setWebsiteIds($websiteIds);
            
            // Set stock data
            $simpleProduct->setStockData([
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 100
            ]);
            
            // Save Simple Product
            $simpleProduct = $productRepository->save($simpleProduct);
            
            // Store product info
            $productUrl = $storeManager->getStore()->getBaseUrl() . $simpleProduct->getUrlKey() . '.html';
            $createdSimpleProducts[] = [
                'id' => $simpleProduct->getId(),
                'name' => $simpleProduct->getName(),
                'sku' => $simpleProduct->getSku(),
                'price' => $simpleProduct->getPrice(),
                'url' => $productUrl
            ];
            
            // Add to associated products array for grouped product
            $defaultQty = isset($productData['group_qty']) && is_numeric($productData['group_qty']) 
                ? (int)$productData['group_qty'] 
                : 1;
            
            $associatedProducts[$simpleProduct->getId()] = [
                'qty' => $defaultQty,
                'position' => $index + 1,
                'sku' => $simpleProduct->getSku()
            ];
        }
        
        // Step 2: Create Grouped Product
        $groupedProduct = $productFactory->create();
        
        // Set Grouped Product basic attributes
        $groupedProduct->setSku(trim($postData['grouped_sku']));
        $groupedProduct->setName(trim($postData['grouped_product_name']));
        $groupedProduct->setTypeId('grouped'); // Use string 'grouped' instead of constant
        $groupedProduct->setAttributeSetId(4); // Default attribute set
        $groupedProduct->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $groupedProduct->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        
        // Set URL key
        if (!empty($postData['grouped_url'])) {
            // Clean URL key
            $urlKey = preg_replace('/[^a-z0-9\-]/', '-', strtolower($postData['grouped_url']));
            $urlKey = preg_replace('/-+/', '-', trim($urlKey, '-'));
            $groupedProduct->setUrlKey($urlKey);
        }
        
        // Set category
        $groupedProduct->setCategoryIds([$postData['category_id']]);
        
        // Set website IDs
        $groupedProduct->setWebsiteIds($websiteIds);
        
        // Set price (grouped products don't have price directly)
        $groupedProduct->setPrice(0);
        
        // Set description if provided
        if (!empty($postData['grouped_description'])) {
            $groupedProduct->setDescription(trim($postData['grouped_description']));
            $groupedProduct->setShortDescription(substr(trim($postData['grouped_description']), 0, 255));
        }
        
        // Set stock data for grouped product
        $groupedProduct->setStockData([
            'use_config_manage_stock' => 0,
            'manage_stock' => 0,
            'is_in_stock' => 1
        ]);
        
        // Save Grouped Product FIRST (without associations)
        $groupedProduct = $productRepository->save($groupedProduct);
        $groupedProductId = $groupedProduct->getId();
        
        // Step 3: Associate Simple Products with Grouped Product
        // METHOD 1: Using setData() approach (Most reliable)
        try {
            // Prepare the grouped links data in the correct format
            $groupedLinksData = [];
            
            foreach ($associatedProducts as $productId => $linkData) {
                $groupedLinksData[$productId] = [
                    'qty' => $linkData['qty'],
                    'position' => $linkData['position']
                ];
            }
            
            // Set the grouped links data
            $groupedProduct->setData('grouped_link_data', $groupedLinksData);
            
            // Save the grouped product with associations
            $groupedProduct = $productRepository->save($groupedProduct);
            
        } catch (Exception $e) {
            // METHOD 2: Direct database update for grouped product links
            try {
                $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
                $connection = $resource->getConnection();
                
                // Get link type ID for grouped products (usually 3)
                $linkTypeTable = $resource->getTableName('catalog_product_link_type');
                $linkTypeId = $connection->fetchOne(
                    $connection->select()
                        ->from($linkTypeTable, ['link_type_id'])
                        ->where('code = ?', 'super')
                );
                
                if (!$linkTypeId) {
                    $linkTypeId = 3; // Default for grouped products
                }
                
                $linkTable = $resource->getTableName('catalog_product_link');
                $linkAttributeTable = $resource->getTableName('catalog_product_link_attribute');
                $linkAttributeIntTable = $resource->getTableName('catalog_product_link_attribute_int');
                
                // Get qty attribute ID
                $qtyAttributeId = $connection->fetchOne(
                    $connection->select()
                        ->from($linkAttributeTable, ['product_link_attribute_id'])
                        ->where('product_link_attribute_code = ?', 'qty')
                        ->where('link_type_id = ?', $linkTypeId)
                );
                
                if (!$qtyAttributeId) {
                    $qtyAttributeId = 10; // Default qty attribute ID for grouped
                }
                
                foreach ($associatedProducts as $productId => $linkData) {
                    // Check if link already exists
                    $existingLink = $connection->fetchOne(
                        $connection->select()
                            ->from($linkTable, ['link_id'])
                            ->where('product_id = ?', $groupedProductId)
                            ->where('linked_product_id = ?', $productId)
                            ->where('link_type_id = ?', $linkTypeId)
                    );
                    
                    if ($existingLink) {
                        // Update existing link
                        $linkId = $existingLink;
                    } else {
                        // Insert new link
                        $connection->insert($linkTable, [
                            'product_id' => $groupedProductId,
                            'linked_product_id' => $productId,
                            'link_type_id' => $linkTypeId
                        ]);
                        $linkId = $connection->lastInsertId($linkTable);
                    }
                    
                    // Insert or update qty attribute
                    $existingQty = $connection->fetchOne(
                        $connection->select()
                            ->from($linkAttributeIntTable, ['value_id'])
                            ->where('link_id = ?', $linkId)
                            ->where('product_link_attribute_id = ?', $qtyAttributeId)
                    );
                    
                    if ($existingQty) {
                        // Update qty
                        $connection->update(
                            $linkAttributeIntTable,
                            ['value' => $linkData['qty']],
                            [
                                'link_id = ?' => $linkId,
                                'product_link_attribute_id = ?' => $qtyAttributeId
                            ]
                        );
                    } else {
                        // Insert qty
                        $connection->insert($linkAttributeIntTable, [
                            'link_id' => $linkId,
                            'product_link_attribute_id' => $qtyAttributeId,
                            'value' => $linkData['qty']
                        ]);
                    }
                }
                
            } catch (Exception $e2) {
                // METHOD 3: Try using product link management
                try {
                    $productLinkManagement = $objectManager->create(
                        \Magento\Catalog\Api\ProductLinkManagementInterface::class
                    );
                    
                    foreach ($associatedProducts as $productId => $linkData) {
                        // Get the simple product to get its SKU
                        $simpleProduct = $productRepository->getById($productId);
                        
                        // Create product link
                        $productLink = $objectManager->create(\Magento\Catalog\Api\Data\ProductLinkInterface::class);
                        $productLink->setSku($groupedProduct->getSku())
                            ->setLinkType('associated')
                            ->setLinkedProductSku($simpleProduct->getSku())
                            ->setPosition($linkData['position']);
                        
                        // Set extension attributes for qty
                        $extensionAttributes = $objectManager->create(
                            \Magento\Catalog\Api\Data\ProductLinkExtension::class
                        );
                        $extensionAttributes->setQty($linkData['qty']);
                        $productLink->setExtensionAttributes($extensionAttributes);
                        
                        // Save the link
                        $productLinkManagement->save($productLink);
                    }
                    
                } catch (Exception $e3) {
                    throw new Exception("Failed to associate products: " . $e3->getMessage());
                }
            }
        }
        
        // Get grouped product URL
        $groupedProductUrl = $storeManager->getStore()->getBaseUrl() . $groupedProduct->getUrlKey() . '.html';
        
        // Prepare associated products info for display
        $associatedProductsInfo = [];
        foreach ($createdSimpleProducts as $simpleProduct) {
            $qty = isset($associatedProducts[$simpleProduct['id']]) 
                ? $associatedProducts[$simpleProduct['id']]['qty'] 
                : 1;
            
            $associatedProductsInfo[] = [
                'name' => $simpleProduct['name'],
                'sku' => $simpleProduct['sku'],
                'qty' => $qty
            ];
        }
        
        return [
            'success' => true,
            'simple_products' => $createdSimpleProducts,
            'grouped_product_id' => $groupedProduct->getId(),
            'grouped_product_name' => $groupedProduct->getName(),
            'grouped_product_sku' => $groupedProduct->getSku(),
            'grouped_product_url' => $groupedProduct->getUrlKey(),
            'grouped_product_url_full' => $groupedProductUrl,
            'associated_products' => $associatedProductsInfo,
            'message' => 'Successfully created ' . count($createdSimpleProducts) . ' simple product(s) and 1 grouped product.'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}