<?php
session_start();
include '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get stats for dashboard
$products_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$contacts_count = $conn->query("SELECT COUNT(*) as count FROM contacts")->fetch_assoc()['count'];
$recent_contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bitech</title>
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
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .recent-contacts {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
        .contact-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .contact-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <h2>Bitech Admin</h2>
                <div>
                    <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
                    <a href="manage-products.php" class="btn btn-primary" style="margin-left: 1rem;">Manage Products</a>
                    <a href="logout.php" class="btn btn-secondary" style="margin-left: 1rem;">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Dashboard</h1>
        
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $products_count; ?></div>
                <div>Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $contacts_count; ?></div>
                <div>Contact Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$0</div>
                <div>Total Sales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div>Pending Orders</div>
            </div>
        </div>

        <div class="recent-contacts">
            <h3>Recent Contact Messages</h3>
            <?php if ($recent_contacts->num_rows > 0): ?>
                <?php while($contact = $recent_contacts->fetch_assoc()): ?>
                    <div class="contact-item">
                        <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                        <small>(<?php echo htmlspecialchars($contact['email']); ?>)</small>
                        <p><?php echo htmlspecialchars(substr($contact['message'], 0, 100)); ?>...</p>
                        <small><?php echo $contact['created_at']; ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No contact messages yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
