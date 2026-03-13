<?php

define('ACCESS', true); // Prevent direct script access
include 'includes/db.php';

// Fetch active theme securely
$stmt_theme = $conn->prepare("SELECT site_theme FROM settings LIMIT 1");
$stmt_theme->execute();
$result_theme = $stmt_theme->get_result();
$theme_row = $result_theme->fetch_assoc();

$theme_map = [
    'green'   => 'theme-olive.css',
    'elegant' => 'theme-bright.css',
    'premium' => 'theme-deep.css',
    'teal'    => 'theme-teal.css',
    'dark'    => 'theme-dark.css'
];

$theme_file = 'assets/themes/' . ($theme_map[$theme_row['site_theme']] ?? 'theme-olive.css');

// Handle search and sorting
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'latest'; // latest, price_asc, price_desc

$order_by = "created_at DESC"; // default
if ($sort === 'price_asc') $order_by = "CAST(REPLACE(REPLACE(price,'N',''),',','') AS UNSIGNED) ASC";
if ($sort === 'price_desc') $order_by = "CAST(REPLACE(REPLACE(price,'N',''),',','') AS UNSIGNED) DESC";

// Pagination
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total matching properties
$count_sql = "SELECT COUNT(*) as total FROM properties WHERE title LIKE ? OR location LIKE ?";
$stmt_count = $conn->prepare($count_sql);
$search_param = "%$search%";
$stmt_count->bind_param("ss", $search_param, $search_param);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_props = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_props / $limit);

// Fetch properties
$sql = "SELECT * FROM properties WHERE title LIKE ? OR location LIKE ? ORDER BY $order_by LIMIT ?, ?";
$stmt_props = $conn->prepare($sql);
$stmt_props->bind_param("ssii", $search_param, $search_param, $offset, $limit);
$stmt_props->execute();
$property_result = $stmt_props->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Properties - David&Family Engineering Services</title>
<meta name="description" content="Explore the available properties developed and managed by David&Family Engineering Services.">
<meta name="keywords" content="Properties, Real Estate, Civil Engineering, Construction, Land Development">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $theme_file; ?>">
<link rel="stylesheet" href="assets/style.css">

<style>
/* Hero overlay text */
.hero-properties {
    position: relative;
    overflow: hidden;
}
.hero-properties img {
    width: 100%;
    height: auto;
}
.hero-properties .overlay-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    text-align: center;
}

.hero-properties .overlay-text h1,
.hero-properties .overlay-text p {
    color: white !important;
}

.card-img-overlay {
    pointer-events: none;
    bottom: 10px;
    left: 10px;
    padding: 5px 10px;
    border-radius: 6px;
}

.card-img-top {
    width: 100%;
    height: 250px; /* set a uniform height */
    object-fit: cover; /* crops and fills the area nicely */
}

.property-card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
}
.property-card:hover {
    transform: scale(1.05);
    z-index: 10;
}
.property-card .card-overlay {
    position: absolute;
    top:0; left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.5);
    color:white;
    display:flex;
    justify-content:center;
    align-items:center;
    text-align:center;
    opacity:0;
    transition: opacity 0.3s;
    padding:10px;
}
.property-card:hover .card-overlay {
    opacity:1;
}
</style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="container-fluid p-0 hero-properties mb-4">
    <img src="assets/images/office1.jpg" alt="Our Properties">
    <div class="overlay-text">
        <h1 class="fw-bold display-4">Our Properties</h1>
        <p class="lead">Browse our residential, commercial, and land development projects.</p>
    </div>
</div>

<!-- Search and Sort -->
<div class="container mb-4">
    <form method="get" class="row g-2 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search properties..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="latest" <?php if($sort=='latest') echo 'selected'; ?>>Latest</option>
                <option value="price_asc" <?php if($sort=='price_asc') echo 'selected'; ?>>Price: Low to High</option>
                <option value="price_desc" <?php if($sort=='price_desc') echo 'selected'; ?>>Price: High to Low</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Search / Sort</button>
        </div>
    </form>
</div>

<!-- Properties Section -->
<section class="container py-2">
    <div class="row g-4">
        <?php while($property = $property_result->fetch_assoc()): ?>
            <?php
            $stmt_img = $conn->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1");
            $stmt_img->bind_param("i", $property['id']);
            $stmt_img->execute();
            $img_res = $stmt_img->get_result();
            $img_row = $img_res->fetch_assoc();
            $img_path = $img_row ? $img_row['image_path'] : 'assets/images/no-image.jpg';
            ?>
            <div class="col-md-4">
                <div class="card property-card h-100">
                    <img src="<?php echo $img_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($property['title']); ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($property['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($property['location']); ?></p>
                        <?php
                        // Ensure number_format works
                        $price_num = floatval(str_replace(['N',','],'',$property['price']));
                        ?>
                        <p class="card-text fw-bold text-primary">N<?php echo number_format($price_num); ?></p>
                        <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Pagination -->
<div class="container mt-4">
    <nav>
        <ul class="pagination justify-content-center">
            <?php for($i=1;$i<=$total_pages;$i++): ?>
                <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                    <a class="page-link" href="?search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
