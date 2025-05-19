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

// Get the product ID from URL
$productId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$productId) {
    header('Location: products.php');
    exit;
}

// Initialize product data
$productData = null;

// Load the existing product
try {
    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);
    
    // Find the product
    foreach ($data as $index => $row) {
        if ($index == 1) continue; // Skip header row
        
        if ($row['A'] == $productId) {
            $productData = [
                'id' => $row['A'],
                'product_name' => $row['B'],
                'category' => $row['C'],
                'main_image' => $row['D'],
                'description' => $row['E'],
                'sowing_time' => $row['F'],
                'sowing_distance' => $row['G'],
                'soil_type' => $row['H'],
                'fertilizer_info' => $row['I'],
                // 'care_info' => $row['J'],
                // 'irrigation_info' => $row['K'],
                // 'note' => $row['L']
            ];
            $rowIndex = $index;
            break;
        }
    }
    
    if (!$productData) {
        $message = "Product not found!";
        $messageType = "error";
        header('Location: products.php');
        exit;
    }
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $messageType = "error";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    try {
        // Load the Excel file again (to avoid conflicts)
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Capture form data
        $productName = $_POST['product_name'] ?? '';
        $category = $_POST['category'] ?? '';
        if ($category === 'other' && !empty($_POST['new_category'])) {
            $category = $_POST['new_category'];
        }
        $description = $_POST['description'] ?? '';
        $sowingTime = $_POST['sowing_time'] ?? '';
        $sowingDistance = $_POST['sowing_distance'] ?? '';
        $soilType = $_POST['soil_type'] ?? '';
        $fertilizerInfo = $_POST['fertilizer_info'] ?? '';
        // $careInfo = $_POST['care_info'] ?? '';
        // $irrigationInfo = $_POST['irrigation_info'] ?? '';
        // $note = $_POST['note'] ?? '';
        
        // Handle image upload
        $mainImage = $productData['main_image']; // Default to existing image
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $targetDir = "assets/images/products/";
            
            // Create directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            // Generate unique filename
            $fileName = basename($_FILES["main_image"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Check if file already exists
            if (file_exists($targetFilePath)) {
                $fileName = time() . '_' . $fileName;
                $targetFilePath = $targetDir . $fileName;
            }
            
            // Allow certain file formats
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
            if (in_array(strtolower($fileType), $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $targetFilePath)) {
                    $mainImage = $targetFilePath;
                    
                    // Delete old image if it exists and is not the default
                    if (!empty($productData['main_image']) && file_exists($productData['main_image']) && $productData['main_image'] !== 'assets/images/no-image.jpg') {
                        @unlink($productData['main_image']);
                    }
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $messageType = "error";
                }
            } else {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $messageType = "error";
            }
        }
        
        // Update the row in the sheet
        $sheet->setCellValue('B' . $rowIndex, $productName);
        $sheet->setCellValue('C' . $rowIndex, $category);
        $sheet->setCellValue('D' . $rowIndex, $mainImage);
        $sheet->setCellValue('E' . $rowIndex, $description);
        $sheet->setCellValue('F' . $rowIndex, $sowingTime);
        $sheet->setCellValue('G' . $rowIndex, $sowingDistance);
        $sheet->setCellValue('H' . $rowIndex, $soilType);
        $sheet->setCellValue('I' . $rowIndex, $fertilizerInfo);
        // $sheet->setCellValue('J' . $rowIndex, $careInfo);
        // $sheet->setCellValue('K' . $rowIndex, $irrigationInfo);
        // $sheet->setCellValue('L' . $rowIndex, $note);
        
        // Save the spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
        
        $message = "Product updated successfully!";
        $messageType = "success";
        
        // Record the last update time
        $updateTime = date("Y-m-d H:i:s");
        file_put_contents('assets/last_update.txt', $updateTime);
        
        // Update the product data to reflect changes
        $productData = [
            'id' => $productId,
            'product_name' => $productName,
            'category' => $category,
            'main_image' => $mainImage,
            'description' => $description,
            'sowing_time' => $sowingTime,
            'sowing_distance' => $sowingDistance,
            'soil_type' => $soilType,
            'fertilizer_info' => $fertilizerInfo,
            // 'care_info' => $careInfo,
            // 'irrigation_info' => $irrigationInfo,
            // 'note' => $note
        ];
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Get existing categories
$categories = [];
try {
    if (file_exists($filePath)) {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);
        
        // Skip header row and extract categories
        foreach ($data as $index => $row) {
            if ($index == 1) continue; // Skip header row
            if (!empty($row['C']) && !in_array($row['C'], $categories)) {
                $categories[] = $row['C'];
            }
        }
    }
} catch (Exception $e) {
    // Handle error silently
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Shreenathji Seeds Admin</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for edit product form */
        .form-container {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-actions {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }
        
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #45a049;
        }
        
        .btn-cancel {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn-cancel:hover {
            background-color: #e5e5e5;
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
        
        .form-section {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        
        .form-section h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .form-help {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .current-image {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .current-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-right: 15px;
        }
        
        .current-image-info {
            font-size: 13px;
            color: #666;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-grid {
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
        
        function confirmCancel() {
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                window.location.href = 'products.php';
            }
        }
        
        // Preview image before upload
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('imagePreview').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
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
                    <li class="active"><a href="adminproducts.php"><i class="fas fa-seedling"></i> Products</a></li>
                    <!-- <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li> -->
                </ul>
            </aside>

            <main class="main-content">
                <div class="page-header">
                    <h2><i class="fas fa-edit"></i> Edit Product</h2>
                    <nav class="breadcrumb">
                        <a href="admin.php">Dashboard</a> / 
                        <a href="products.php">Products</a> / 
                        <span>Edit Product</span>
                    </nav>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-section">
                            <h3>Basic Information</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="product_name">Product Name*</label>
                                    <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($productData['product_name']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="category">Category*</label>
                                    <select id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="vegetable" <?= ('vegetable-Seeds' === $productData['category']) ? 'selected' : '' ?>>Vegetable Seeds</option>
                                        <option value="field-crop" <?= ('field-crop-Seeds' === $productData['category']) ? 'selected' : '' ?>>Field Crop Seeds</option>
                                        <option value="f1-kitchen-garden" <?= ('f1-kitchen-garden' === $productData['category']) ? 'selected' : '' ?>>F1 Kitchen Garden</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description*</label>
                                <textarea id="description" name="description" required><?= htmlspecialchars($productData['description']) ?></textarea>
                                <div class="form-help">Provide a comprehensive description of the product.</div>
                            </div>
                            <div class="form-group">
                                <label for="main_image">Main Image</label>
                                <?php if (!empty($productData['main_image'])): ?>
                                    <div class="current-image">
                                        <img src="<?= $productData['main_image'] ?>" alt="Current Image">
                                        <div class="current-image-info">
                                            <p>Current image: <?= basename($productData['main_image']) ?></p>
                                            <p>Upload a new image to replace the current one.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" id="main_image" name="main_image" accept="image/*" onchange="previewImage(this)">
                                <div class="form-help">Upload a clear, high-quality image. Recommended size: 800x600 px.</div>
                                <img id="imagePreview" src="#" alt="Image Preview" style="display:none; max-width:200px; margin-top:10px; border:1px solid #ddd;">
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>Growing Information</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <!-- <label for="sowing_time">વાવેતર સમય (Sowing Time)*</label> -->
                                    <textarea id="sowing_time" name="sowing_time" required><?= htmlspecialchars($productData['sowing_time']) ?></textarea>
                                </div>                               
                        </div>

                        <div class="form-section">
                            <h3>Care Instructions</h3>
                            <div class="form-group">
                                <!-- <label for="fertilizer_info">ખાતર (Fertilizer Info)*</label> -->
                                <textarea id="fertilizer_info" name="fertilizer_info" required><?= htmlspecialchars($productData['fertilizer_info']) ?></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="confirmCancel()">Cancel</button>
                            <button type="submit" name="update_product" class="btn-submit">Update Product</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>

        <footer class="admin-footer">
            <p>&copy; 2025 Shreenathji Seeds - Admin Panel</p>
        </footer>
    </div>

    <script>
        // Handle new category addition
        document.getElementById('category').addEventListener('change', function() {
            var newCategoryContainer = document.getElementById('new_category_container');
            if (this.value === 'other') {
                newCategoryContainer.style.display = 'block';
                document.getElementById('new_category').setAttribute('required', 'required');
            } else {
                newCategoryContainer.style.display = 'none';
                document.getElementById('new_category').removeAttribute('required');
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (document.getElementById('category').value === 'other' && 
                document.getElementById('new_category').value.trim() === '') {
                alert('Please enter a new category name');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>