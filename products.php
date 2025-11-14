<?php
// Bitech - Products Page
include 'includes/config.php';
include 'includes/header.php';

// Fetch products from database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1>Our Products</h1>
        <p>Discover our wide range of electronic devices</p>
    </div>
</section>

<section class="products-section">
    <div class="container">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search products...">
            <button onclick="searchProducts()">Search</button>
        </div>
        
        <!-- Category Filter -->
        <div class="category-filter">
            <button class="filter-btn active" data-category="all">All Products</button>
            <button class="filter-btn" data-category="computers">Computers</button>
            <button class="filter-btn" data-category="laptops">Laptops</button>
            <button class="filter-btn" data-category="mobiles">Mobile Phones</button>
            <button class="filter-btn" data-category="parts">Spare Parts</button>
        </div>
        
        <!-- Products Grid -->
        <div class="products-grid" id="productsGrid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-category="<?php echo $product['category']; ?>">
                        <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <div class="product-info">
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="product-description"><?php echo $product['description']; ?></p>
                            <p class="product-price">$<?php echo $product['price']; ?></p>
                            <button class="btn btn-primary add-to-cart" data-id="<?php echo $product['id']; ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const productName = card.querySelector('h3').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Category filter functionality
document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        const category = this.getAttribute('data-category');
        const productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            if (category === 'all' || card.getAttribute('data-category') === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
