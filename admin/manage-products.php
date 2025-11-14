<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        // Add new product
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $image = $_POST['image'];
        
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $description, $price, $category, $image);
        
        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $error = "Error adding product: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['update_product'])) {
        // Update product
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $image = $_POST['image'];
        
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category=?, image=? WHERE id=?");
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image, $id);
        
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
        } else {
            $error = "Error updating product: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['delete_product'])) {
        // Delete product
        $id = $_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Product deleted successfully!";
        } else {
            $error = "Error deleting product: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Bitech</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-header {
            background: var(--white);
            padding: 1rem 0;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .product-form {
            background: var(--white);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .products-table {
            background: var(--white);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background-color: var(--background-light);
            font-weight: 600;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>Manage Products - Bitech</h2>
                <div>
                    <a href="index.php" class="btn btn-secondary">Dashboard</a>
                    <a href="logout.php" class="btn btn-secondary" style="margin-left: 1rem;">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="product-form">
            <h3>Add New Product</h3>
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="computers">Computers</option>
                            <option value="laptops">Laptops</option>
                            <option value="mobiles">Mobile Phones</option>
                            <option value="parts">Spare Parts</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Image File Name</label>
                        <input type="text" id="image" name="image" placeholder="e.g., product1.jpg" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
            </form>
        </div>

        <div class="products-table">
            <h3>All Products</h3>
            <?php if ($products->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</td>
                            <td>$<?php echo $product['price']; ?></td>
                            <td><?php echo ucfirst($product['category']); ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="btn btn-secondary" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px;">
            <h3>Edit Product</h3>
            <form method="POST" action="" id="editForm">
                <input type="hidden" name="id" id="editId">
                <div class="form-group">
                    <label for="editName">Product Name</label>
                    <input type="text" id="editName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="editDescription">Description</label>
                    <textarea id="editDescription" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editPrice">Price ($)</label>
                    <input type="number" id="editPrice" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="editCategory">Category</label>
                    <select id="editCategory" name="category" required>
                        <option value="computers">Computers</option>
                        <option value="laptops">Laptops</option>
                        <option value="mobiles">Mobile Phones</option>
                        <option value="parts">Spare Parts</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editImage">Image File Name</label>
                    <input type="text" id="editImage" name="image" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function editProduct(id) {
        // In a real application, you would fetch the product data via AJAX
        // For simplicity, we'll get it from the table row
        const row = document.querySelector(`tr:has(td:first-child:contains('${id}')`);
        if (row) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = row.cells[2].textContent;
            document.getElementById('editDescription').value = row.cells[3].textContent.replace('...', '');
            document.getElementById('editPrice').value = row.cells[4].textContent.replace('$', '');
            document.getElementById('editCategory').value = row.cells[5].textContent.toLowerCase();
            document.getElementById('editImage').value = row.cells[1].querySelector('img').src.split('/').pop();
            
            document.getElementById('editModal').style.display = 'block';
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    </script>
</body>
</html>
