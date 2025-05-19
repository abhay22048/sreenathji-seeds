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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    try {
        // Load the Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        // Ensure the header row contains field names in bold
        $headerStyleArray = [
            'font' => [
                'bold' => true,
            ],
        ];

        $headerCells = ['A1', 'B1', 'C1', 'D1', 'E1', 'F1', 'I1'];
        foreach ($headerCells as $cell) {
            $sheet->getStyle($cell)->applyFromArray($headerStyleArray);
        }
        // Ensure the header row contains field names
        if ($sheet->getCell('A1')->getValue() === null) {
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Product Name');
            $sheet->setCellValue('C1', 'Category');
            $sheet->setCellValue('D1', 'Main Image');
            $sheet->setCellValue('E1', 'Description');
            $sheet->setCellValue('F1', 'Sowing Time');
            // $sheet->setCellValue('G1', 'Sowing Distance');
            // $sheet->setCellValue('H1', 'Soil Type');
            $sheet->setCellValue('I1', 'Fertilizer Info');
            // $sheet->setCellValue('J1', 'Care Info');
            // $sheet->setCellValue('K1', 'Irrigation Info');
            // $sheet->setCellValue('L1', 'Note'); "write this in header cless when add more fields 'J1', 'K1', 'L1'  ,  'G1', 'H1',"
        }
        // Skip the first column and start the product ID from column B
        $sheet->setCellValue('B1', 'ID');
        $sheet->setCellValue('C1', 'Product Name');
        $sheet->setCellValue('D1', 'Category');
        $sheet->setCellValue('E1', 'Main Image');
        $sheet->setCellValue('F1', 'Description');
        $sheet->setCellValue('G1', 'Sowing Time');
        // $sheet->setCellValue('H1', 'Sowing Distance');
        // $sheet->setCellValue('I1', 'Soil Type');
        $sheet->setCellValue('J1', 'Fertilizer Info');
        // $sheet->setCellValue('K1', 'Care Info');
        // $sheet->setCellValue('L1', 'Irrigation Info');
        // $sheet->setCellValue('M1', 'Note');
        // Get the last row number
        $highestRow = $sheet->getHighestRow();
        $newId = $highestRow + 1; // Increment ID to avoid overwriting
        
        // Capture form data
        $productName = $_POST['product_name'] ?? '';
        $category = $_POST['category'] ?? '';
        $description = $_POST['description'] ?? '';
        $sowingTime = $_POST['sowing_time'] ?? '';
        // $sowingDistance = $_POST['sowing_distance'] ?? '';
        // $soilType = $_POST['soil_type'] ?? '';
        $fertilizerInfo = $_POST['fertilizer_info'] ?? '';
        // $careInfo = $_POST['care_info'] ?? '';
        // $irrigationInfo = $_POST['irrigation_info'] ?? '';
        // $note = $_POST['note'] ?? '';
        
        // Handle image upload
        $mainImage = '';
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $targetDir = "assets/images/products/";
            
            // Create directory if it doesn't exist
            if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
            }
            
            // Use the original filename
            $fileName = basename($_FILES["main_image"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Allow certain file formats
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
            if (in_array(strtolower($fileType), $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $targetFilePath)) {
                    $mainImage = $targetFilePath;
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $messageType = "error";
                }
            } else {
                $message = "Sorry, only JPG, JPEG, PNG, GIF & WEBP files are allowed.";
                $messageType = "error";
            }
        }
        // Add new row to the sheet
        $sheet->setCellValue('A' . ($highestRow + 1), $newId);
        $sheet->setCellValue('B' . ($highestRow + 1), $productName);
        $sheet->setCellValue('C' . ($highestRow + 1), $category);
        $sheet->setCellValue('D' . ($highestRow + 1), $mainImage);
        $sheet->setCellValue('E' . ($highestRow + 1), $description);
        $sheet->setCellValue('F' . ($highestRow + 1), $sowingTime);
        // $sheet->setCellValue('G' . ($highestRow + 1), $sowingDistance);
        // $sheet->setCellValue('H' . ($highestRow + 1), $soilType);
        $sheet->setCellValue('I' . ($highestRow + 1), $fertilizerInfo);
        // $sheet->setCellValue('J' . ($highestRow + 1), $careInfo);
        // $sheet->setCellValue('K' . ($highestRow + 1), $irrigationInfo);
        // $sheet->setCellValue('L' . ($highestRow + 1), $note);
        
        // Save the spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
        
        $message = "Product added successfully!";
        $messageType = "success";
        
        // Record the last update time
        $updateTime = date("Y-m-d H:i:s");
        file_put_contents('assets/last_update.txt', $updateTime);
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Get existing categories from the products.php categories
$categories = ['vegetable', 'field-crop', 'f1-kitchen-garden'];

// As a backup, try to extract categories from the Excel file as well
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
    <title>Add Product - Shreenathji Seeds Admin</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styles for add product form */
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
                window.location.href = 'admin.php';
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
                    <li class="active"><a href="products.php"><i class="fas fa-seedling"></i> Products</a></li>
                </ul>
            </aside>

            <main class="main-content">
                <div class="page-header">
                    <h2><i class="fas fa-plus"></i> Add New Product</h2>
                    <nav class="breadcrumb">
                        <a href="admin.php">Dashboard</a> / 
                        <a href="adminproducts.php">Products</a> / 
                        <span>Add New Product</span>
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
                                    <input type="text" id="product_name" name="product_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="category">Category*</label>
                                    <select id="category" name="category" required>
                                        <option value="" disabled selected>Select a category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description">Description*</label>
                                <textarea id="description" name="description" required></textarea>
                                <div class="form-help">Provide a comprehensive description of the product.</div>
                            </div>
                            <div class="form-group">
                                <label for="main_image">Main Image</label>
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
                                    <textarea id="sowing_time" name="sowing_time" rows="4" cols="50" required></textarea>
                                </div>
                        </div>

                        <div class="form-section">
                            <h3>Care Instructions</h3>
                            <div class="form-group">
                                <!-- <label for="fertilizer_info">ખાતર (Fertilizer Info)*</label> -->
                                <textarea id="fertilizer_info" name="fertilizer_info" required></textarea>
                            </div>                            
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="confirmCancel()">Cancel</button>
                            <button type="submit" name="add_product" class="btn-submit">Add Product</button>
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
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            var category = document.getElementById('category').value;
            if (category === '') {
                alert('Please select a category');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>