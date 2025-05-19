<?php
session_start();

// Check login status
$loggedIn = isset($_SESSION['username']) && $_SESSION['username'] === 'abhay';

// Redirect if not logged in
if (!$loggedIn) {
    header('Location: admin.php');
    exit;
}

// Include PhpSpreadsheet library
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Define the file path
$filePath = 'assets/products.xlsx';
$message = '';
$messageType = '';

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $productId = $_GET['delete'];
        
        // Load the Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);
        
        // Create a new spreadsheet with the remaining products
        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        
        // Add the header row
        $headerRow = $data[1];
        foreach (range('A', 'L') as $col => $letter) {
            $newSheet->setCellValue($letter . '1', $headerRow[$letter]);
        }
        
        // Add all rows except the one to be deleted
        $newRowIndex = 2;
        foreach ($data as $index => $row) {
            if ($index == 1) continue; // Skip header row
            
            if ($row['A'] != $productId) {
                foreach (range('A', 'L') as $col => $letter) {
                    $newSheet->setCellValue($letter . $newRowIndex, $row[$letter]);
                }
                $newRowIndex++;
            }
        }
        
        // Save the new spreadsheet
        $writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');
        $writer->save($filePath);
        
        $message = "Product deleted successfully!";
        $messageType = "success";
        
        // Record the last update time
        $updateTime = date("Y-m-d H:i:s");
        file_put_contents('assets/last_update.txt', $updateTime);
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Get products from Excel file
$products = [];
$categories = [];
try {
    if (file_exists($filePath)) {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);
        
        // Skip header row and extract products
        foreach ($data as $index => $row) {
            if ($index == 1) continue; // Skip header row
            
            // Add to products array
            $products[] = [
                'id' => $row['A'],
                'name' => $row['B'],
                'category' => $row['C'],
                'image' => $row['D'],
                'description' => $row['E']
            ];
            
            // Add to categories array if unique
            if (!empty($row['C']) && !in_array($row['C'], $categories)) {
                $categories[] = $row['C'];
            }
        }
    }
} catch (Exception $e) {
    $message = "Error loading products: " . $e->getMessage();
    $messageType = "error";
}

// Get total products count
$totalProducts = count($products);

// Get total categories count
$totalCategories = count($categories);

// Get last update time
$lastUpdate = "Not available";
if (file_exists('assets/last_update.txt')) {
    $lastUpdate = file_get_contents('assets/last_update.txt');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Shreenathji Seeds Admin</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for products page */
        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .btn-add {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        
        .btn-add:hover {
            background-color: #45a049;
        }
        
        .products-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-item label {
            font-weight: bold;
            color: #555;
        }
        
        .filter-item select,
        .filter-item input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 150px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .products-table th,
        .products-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .products-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .products-table tr:last-child td {
            border-bottom: none;
        }
        
        .products-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit,
        .btn-delete,
        .btn-view {
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-edit {
            background-color: #4a6cf7;
            color: white;
        }
        
        .btn-delete {
            background-color: #f74a6c;
            color: white;
        }
        
        .btn-view {
            background-color: #6cf74a;
            color: #333;
        }
        
        .btn-edit:hover {
            background-color: #3a5ce7;
        }
        
        .btn-delete:hover {
            background-color: #e73a5c;
        }
        
        .btn-view:hover {
            background-color: #5ce73a;
        }
        
        .no-products {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            color: #555;
        }
        
        .no-products i {
            font-size: 40px;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a.active {
            background-color: #4a6cf7;
            color: white;
            border-color: #4a6cf7;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #f5f5f5;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Stats cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stats-card i {
            font-size: 30px;
            color: #4a6cf7;
            background: #f0f4ff;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .stats-card .stats-info h3 {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        
        .stats-card .stats-info p {
            margin: 5px 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .products-table {
                display: block;
                overflow-x: auto;
            }
            
            .products-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        function logout() {
            localStorage.removeItem('loggedIn');
            localStorage.removeItem('username');
            window.location.href = 'admin.php?logout=1';
        }
        
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete the product "' + name + '"? This action cannot be undone.')) {
                window.location.href = 'products.php?delete=' + id;
            }
        }
    </script>
</head>
<body>
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
                    <li><a href="admin.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li class="active"><a href="products.php"><i class="fas fa-seedling"></i> Products</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="products-header">
                    <h2><i class="fas fa-seedling"></i> Products Management</h2>
                    <a href="add_products.php" class="btn-add"><i class="fas fa-plus"></i> Add New Product</a>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <div class="stats-cards">
                    <div class="stats-card">
                        <i class="fas fa-seedling"></i>
                        <div class="stats-info">
                            <h3>Total Products</h3>
                            <p><?= $totalProducts ?></p>
                        </div>
                    </div>
                    <div class="stats-card">
                        <i class="fas fa-list"></i>
                        <div class="stats-info">
                            <h3>Categories</h3>
                            <p><?= $totalCategories ?></p>
                        </div>
                    </div>
                    <div class="stats-card">
                        <i class="fas fa-clock"></i>
                        <div class="stats-info">
                            <h3>Last Updated</h3>
                            <p><?= $lastUpdate ?></p>
                        </div>
                    </div>
                </div>

                <div class="products-filters">
                    <div class="filter-item">
                        <label for="filter-category">Category:</label>
                        <select id="filter-category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="search">Search:</label>
                        <input type="text" id="search" placeholder="Search by name...">
                    </div>
                </div>

                <?php if (count($products) > 0): ?>
                    <div class="table-responsive">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="products-table-body">
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td>
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                                            <?php else: ?>
                                                <div class="product-image" style="display:flex;align-items:center;justify-content:center;background:#f5f5f5;">
                                                    <i class="fas fa-image" style="color:#ddd;font-size:20px;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['category']) ?></td>
                                        <td>
                                            <div class="product-actions">
                                                <a href="edit_products.php?id=<?= $product['id'] ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <!-- <a href="javascript:void(0)" onclick="confirmDelete('<?= $product['id'] ?>', '<?= htmlspecialchars($product['name']) ?>')" class="btn-delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a> -->
                                                <a href="products-details.php?product_id=<?= $product['id'] ?>" target="_blank" class="btn-view">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php
                    $productsPerPage = 10;
                    $totalPages = ceil($totalProducts / $productsPerPage);
                    $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                    $currentPage = max(1, min($currentPage, $totalPages));

                    if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-seedling"></i>
                        <h3>No Products Found</h3>
                        <p>Get started by adding your first product.</p>
                        <a href="add_products.php" class="btn-add" style="margin-top:15px;"><i class="fas fa-plus"></i> Add New Product</a>
                    </div>
                <?php endif; ?>
            </main>
        </div>

        <footer class="admin-footer">
            <p>&copy; 2025 Shreenathji Seeds - Admin Panel</p>
        </footer>
    </div>

    <script>
        // Filter products by category
        document.getElementById('filter-category').addEventListener('change', function() {
            filterProducts();
        });
        
        // Filter products by search term
        document.getElementById('search').addEventListener('input', function() {
            filterProducts();
        });
        
        function filterProducts() {
            const category = document.getElementById('filter-category').value.toLowerCase();
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const rows = document.getElementById('products-table-body').getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const productName = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
                const productCategory = rows[i].getElementsByTagName('td')[3].textContent.toLowerCase();
                
                const matchesCategory = category === '' || productCategory === category;
                const matchesSearch = searchTerm === '' || productName.includes(searchTerm);
                
                rows[i].style.display = (matchesCategory && matchesSearch) ? '' : 'none';
            }
            
            // Update visible count
            const visibleRows = [...rows].filter(row => row.style.display !== 'none').length;
            if (visibleRows === 0) {
                // You could display a "no results" message here
                console.log('No products match your filters');
            }
        }
    </script>
</body>
</html>