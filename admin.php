<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.html');
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Example static credentials (replace with DB check later)
    if ($user === 'abhay' && $pass === '1234') {
        $_SESSION['username'] = $user;
        // No need to send password to localStorage for security reasons
        // Just set login status to true
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid credentials!';
    }
}

// Check login status
$loggedIn = isset($_SESSION['username']) && $_SESSION['username'] === 'abhay';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shreenathji Seeds - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        // Check if user is already logged in via localStorage
        document.addEventListener('DOMContentLoaded', function() {
            // If PHP session is active, store in localStorage
            <?php if ($loggedIn): ?>
                localStorage.setItem('loggedIn', 'true');
                localStorage.setItem('username', '<?= $_SESSION['username'] ?>');
            <?php endif; ?>
            
            // If user is logged out but localStorage says logged in, redirect to login
            <?php if (!$loggedIn): ?>
                if(localStorage.getItem('loggedIn') === 'true') {
                    // Redirect to same page to attempt session login
                    window.location.href = 'admin.php';
                }
            <?php endif; ?>
        });

        // Function to handle logout
        function logout() {
            // Clear localStorage
            localStorage.removeItem('loggedIn');
            localStorage.removeItem('username');
            // Redirect to logout URL
            window.location.href = 'admin.php?logout=1';
        }
    </script>
</head>
<body>
    <?php if (!$loggedIn): ?>
        <div id="login-popup" class="login-popup" style="display:flex;">
            <div class="login-box">
                <h2>Admin Login</h2>
                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <?php if (isset($error)): ?>
                    <p id="login-error" style="color:red;"><?= $error ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="admin-container">
            <header class="admin-header">
                <div class="logo">
                    <h1>Shreenathji Seeds</h1>
                    <p>Admin Panel</p>
                </div>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span id="username-display"><?= $_SESSION['username'] ?></span>
                    <a href="javascript:void(0)" onclick="logout()" class="logout-btn">Logout</a>
                </div>
            </header>

            <div class="admin-content">
                <aside class="sidebar">
                    <ul>
                        <li class="active"><a href="admin.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                        <li><a href="adminproducts.php"><i class="fas fa-seedling"></i> Products</a></li>
                    </ul>
                </aside>

                <main class="main-content">
                    <div class="welcome-section">
                        <h2>Welcome to Shreenathji Seeds Admin Panel</h2>
                        <p>Manage your product inventory, update product details, and maintain your product database.</p>
                    </div>

                    <?php
                    require 'vendor/autoload.php'; // Include PHPExcel or PhpSpreadsheet library
                

                    // Load the Excel file
                    $filePath = 'assets/products.xlsx'; // Path to your Excel file
                    $spreadsheet = IOFactory::load($filePath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $data = $sheet->toArray(null, true, true, true);

                    // Parse data from Excel
                    $products = [];
                    foreach ($data as $index => $row) {
                        if ($index === 1) {
                            // Skip header row
                            continue;
                        }
                        $products[] = [
                            'id' => $row['A'],
                            'name' => $row['B'],
                            'category' => $row['C'],
                        ];
                    }

                    $categories = array_unique(array_column($products, 'category'));

                    // Get total products count
                    $totalProducts = count($products);

                    // Get total categories count
                    $totalCategories = count($categories);

                    // Example last update (replace with actual logic if needed)
                    $lastUpdate = date('Y-m-d');
                    ?>

                    <div class="stats-section">
                        <div class="stat-card">
                            <i class="fas fa-seedling"></i>
                            <div class="stat-info">
                                <h3>Total Products</h3>
                                <p id="total-products"><?= $totalProducts ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-list"></i>
                            <div class="stat-info">
                                <h3>Categories</h3>
                                <p id="total-categories"><?= $totalCategories ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-edit"></i>
                            <div class="stat-info">
                                <h3>Last Updated</h3>
                                <p id="last-update"><?= $lastUpdate ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="quick-actions">
                        <h3>Quick Actions</h3>
                        <div class="action-buttons">
                            <a href="add_products.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
                            <a href="adminproducts.php?action=edit" class="btn btn-secondary"><i class="fas fa-list"></i> View All Products</a>
                        </div>
                    </div>
                </main>
            </div>

            <footer class="admin-footer">
                <p>&copy; 2025 Shreenathji Seeds - Admin Panel</p>
            </footer>
        </div>
    <?php endif; ?>
</body>
</html>