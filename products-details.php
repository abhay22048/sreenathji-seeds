<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Shreenathji Seeds</title>
    <link rel="stylesheet" href="assets/css/product-details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="/">
                    <img src="assets/images/logo.png" alt="Shreenathji Seeds Logo">
                </a>
            </div>
            <nav>
                <ul class="navigation">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="index.html#about">About Us</a></li>
                    <li class="active"><a href="products.php">Products</a></li>
                    <li><a href="index.html#research">R & D</a></li>
                    <li><a href="index.html#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php
    // Include PhpSpreadsheet library
    require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\IOFactory;

    // Get the product_id from the URL
    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

    // Define the file path
    $filePath = 'assets/products.xlsx';

    // Check if the file exists
    if (!file_exists($filePath)) {
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the spreadsheet
        $headers = ['ID', 'Product Name', 'Category', 'Main Image', 'Description', 'Sowing Time', 'Sowing Distance', 'Soil Type', 'Fertilizer Info', 'Care Info', 'Irrigation Info', 'Note'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1'; // Convert column index to letter (A, B, C, etc.)
            $sheet->setCellValue($cell, $header);
        }

        // Save the spreadsheet to the file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    }

    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);

    // Get the first sheet
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);

    // Check if a product ID was provided in the URL
    if ($product_id === null) {
        echo '<div class="container"><div class="error-message">No product ID provided.</div></div>';
        include 'footer.php';
        exit;
    }

    $productFound = false;

    // Skip the header row and loop through the data
    foreach ($data as $index => $row) {
        if ($index == 1) continue; // Skip header row

        // Extract data from each row
        $id = $row['A'];
        
        // Check if this is the product we're looking for
        if ($id == $product_id) {
            $productFound = true;
            $productName = $row['B'];
            $category = $row['C'];
            $mainImage = $row['D'];
            $description = $row['E'];
            $sowingTime = $row['F'];
            $sowingDistance = $row['G'];
            $soilType = $row['H'];
            $fertilizerInfo = $row['I'];
            $careInfo = $row['J'];
            $irrigationInfo = $row['K'];
            $note = $row['L'];
    ?>
        <!-- Product Hero Section -->
        <section class="product-hero">
            <div class="container">        
                <h1 class="animate__animated animate__fadeInUp"><?php echo $productName; ?></h1>
                <div class="product-category animate__animated animate__fadeInUp animate__delay-1s">
                    <span class="category-badge"><?php echo $category; ?></span>
                </div>
            </div>
        </section>

        <!-- Product Details Section -->
        <section class="product-details">
            <div class="container">
            <div class="product-grid">
                <!-- Product Images Gallery -->
                <div class="product-gallery" data-aos="fade-right">
                <div class="main-image">
                    <img id="mainImage" src="<?php echo $mainImage; ?>" alt="<?php echo $productName; ?>">
                </div>
                </div>

                <!-- Product Information -->
                <div class="product-info" data-aos="fade-left">
                <div class="product-description">
                    <h2><?php echo $description; ?></h2>
                </div>

                <div class="product-specifications">
                    <div class="spec-item">
                    <span class="spec-name">વાવેતર સમય:</span>
                    <span class="spec-value"><?php echo $sowingTime; ?></span>
                    </div>
                    <div class="spec-item">
                    <span class="spec-name">વાવણી અંતર:</span>
                    <span class="spec-value"><?php echo $sowingDistance; ?></span>
                    </div>
                    <div class="spec-item">
                    <span class="spec-name">જમીન:</span>
                    <span class="spec-value"><?php echo $soilType; ?></span>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </section>

        <!-- Growing Guide Section -->
        <section class="growing-guide" data-aos="fade-up">
            <div class="container">
            <h2>ઉછેર વિષે માહિતી</h2>
            <div class="tabs">
                <button class="tab-btn active" data-tab="planting">ખાતર</button>
                <button class="tab-btn" data-tab="care">પાક સંરક્ષણ</button>
                <button class="tab-btn" data-tab="harvesting">પિયત</button>
                <button class="tab-btn" data-tab="note">ખુલાસો</button>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="planting">
                <div class="content-wrapper">
                    <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon">
                        <i class="fas fa-seedling"></i>
                        </div>
                        <div class="timeline-content">
                        <p><?php echo $fertilizerInfo; ?></p>
                        </div>
                    </div>                            
                    </div>
                </div>
                </div>
                <div class="tab-pane" id="care">
                <div class="care-container">
                    <div class="care-item">
                    <div class="care-icon">
                        <i class="fas fa-shield-virus"></i>
                    </div>
                    <p><?php echo $careInfo; ?></p>
                    </div>
                </div>
                </div>
                <div class="tab-pane" id="harvesting">
                <div class="harvesting-content">
                    <div class="harvesting-text">
                    <p><?php echo $irrigationInfo; ?></p>
                    </div>
                </div>
                </div>
                <div class="tab-pane" id="note">
                <div class="storage-content">
                    <div class="storage-methods">
                    <div class="storage-method">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p><?php echo $note; ?></p>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </section>
    <?php
            break; // Exit the loop after finding the product
        }
    }
    
    // If no product was found with the provided ID
    if (!$productFound) {
        echo '<div class="container"><div class="error-message">Product not found.</div></div>';
    }
    ?>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <div class="footer-top">
                <div class="footer-logo">
                    <a href="/">
                        <img src="assets/images/logo.png" alt="Shreenathji Seeds Logo" style="background-color: white; padding: 10px; border-radius: 8px;">
                      </a>
                      

                    <p class="tagline">A Real Friend Of Farmer</p>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h3>Quick Links</h3>
                        <ul >
                            <li><a href="index.html">Home</a></li>
                            <li><a href="index.html#about">About Us</a></li>
                            <li class="active"><a href="products.php">Products</a></li>
                            <li><a href="index.html#research">R & D</a></li>
                            <li><a href="index.html#contact">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Contact Us</h3>
                        <ul class="contact-info">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Main Bazar, Dhari, Dist: Amreli, Gujarat</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <span>+91 9879394448</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>info@shreenathji.com</span>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Follow Us</h3>
                        <div class="social-links">
                            <a href="https://www.facebook.com/people/Shreenathji-vegetable-Seeds/61552541011064/?mibextid=LQQJ4d" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com/shreenathji_seeds_farm/?igsh=MWo2c2k3dTN6N2w3Zw%3D%3D#" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
                            <!-- <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Copyright © 2025 | Powered by <a href="https://shreenathijseedsfarm.com">shreenathijseedsfarm.com</a></p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <div id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/product_details.js"></script>
</body>
</html>