<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Shreenathji Seeds</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <link rel="stylesheet" href="assets/css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@700&display=swap" rel="stylesheet">

</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <a href="/">
                    <img src="assets/images/logo.png" alt="Shreenathji Seeds Logo" class="animate__animated animate__fadeIn">
                  </a>
                  
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="index.html#about">About Us</a></li>
                    <li><a href="products.php" class="active">Products</a></li>
                    <li><a href="index.html#research">R & D</a></li>
                    <li><a href="index.html#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Products Banner -->
    <section class="banner">
        <div class="container">
            <h1 class="animate__animated animate__fadeInUp" style="font-family: 'Merienda', cursive; color:rgb(255, 255, 255);">Our Products</h1>
            <div class="separator" data-aos="fade-up">
                <span class="yellow-line"></span>
                <span class="green-line"></span>
                <span class="yellow-line"></span>
            </div>
        </div>
    </section>

    <!-- Products Categories -->
    <section class="categories">
        <div class="container">
            <div class="category-filters" data-aos="fade-up">
                <button class="category-btn active" data-category="all">All Products</button>
                <button class="category-btn" data-category="vegetable">Vegetable Seeds</button>
                <button class="category-btn" data-category="field-crop">Field Crop Seeds</button>
                <button class="category-btn" data-category="f1-kitchen-garden"> F1 Kitchen Garden</button>
            </div>
        </div>
    </section>

    <!-- Products Display -->
    <?php
    require 'vendor/autoload.php'; // Include PhpSpreadsheet library

    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $filePath = 'assets/products.xlsx';

    // Check if the file exists, if not, create it
    if (!file_exists($filePath)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add header row
        $headers = ['ID', 'Product Name', 'Category', 'Main Image', 'Description', 'Sowing Time', 'Sowing Distance', 'Soil Type', 'Fertilizer Info', 'Care Info', 'Irrigation Info', 'Note'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    // Load the XLSX file
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    echo '<section class="products-display">
    <div class="container">
        <div class="products-grid">';

    foreach ($rows as $index => $row) {
        if ($index === 0) continue; // Skip the header row
        
        // Make sure we have enough columns
        if (count($row) >= 5) {
            $id = $row[0]; // ID is the first column (index 0)
            $productName = $row[1];
            $category = $row[2];
            $mainImage = $row[3];
            $description = $row[4];

            // Add CSS class based on category for filtering
            $categoryClass = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $category));

            echo '<div class="product-card ' . htmlspecialchars($categoryClass) . '" data-aos="fade-up">
                <div class="product-image">
                    <img src="' . htmlspecialchars($mainImage) . '" alt="' . htmlspecialchars($productName) . '">
                </div>
                <div class="product-info">
                    <h3>' . htmlspecialchars($productName) . '</h3>
                    <p>' . htmlspecialchars($description) . '</p>
                    <a href="products-details.php?product_id=' . urlencode($id) . '" class="product-btn">View Details</a>
                </div>
            </div>';
        }
    }

    echo '    </div>
    </div>
    </section>';
    ?>

    <!-- Load More Button -->
    <div class="load-more-container">
        <button class="load-more-btn" data-aos="fade-up">Load More Products</button>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="/">
                        <img src="assets/images/logo.png" alt="Shreenathji Seeds Logo" class="footer-logo-img">
                      </a>
                      
                <p style="font-family: 'Merienda', cursive; color:rgb(255, 255, 255);">A Real Friend Of Farmer</p>
                </div>
                
                <div class="quick-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="index.html#about">About Us</a></li>
                        <li><a href="products.php" class="active">Products</a></li>
                        <li><a href="index.html#research">R & D</a></li>
                        <li><a href="index.html#contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="contact-info">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Main Bazar, Dhari, Dist: Amreli, Gujarat</p>
                    <p><i class="fas fa-phone"></i> +91 9879394448</p>
                    <p><i class="fas fa-envelope"></i> info@shreenathji.com</p>
                </div>
                
                <div class="social-links">
                    <h3>Follow Us</h3>
                    <div class="social-icons" >
                        <a href="https://www.facebook.com/people/Shreenathji-vegetable-Seeds/61552541011064/?mibextid=LQQJ4d" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/shreenathji_seeds_farm/?igsh=MWo2c2k3dTN6N2w3Zw%3D%3D#" target="_blank"><i class="fab fa-instagram"></i></a>
                        
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>Copyright © 2025 | Powered by <a href="https://shreenathijiseedsfarm.com">shreenathijiseedsfarm.com</a></p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/products.js"></script>
</body>
</html>