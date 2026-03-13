<?php

define('ACCESS', true); // Prevent direct script access
include 'includes/db.php';

// Fetch active theme
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

// Handle search, sort, and pagination
$search = trim($_GET['search'] ?? '');
$sort   = $_GET['sort'] ?? 'date_desc';
$page   = max(intval($_GET['page'] ?? 1), 1);
$perPage = 6;
$offset = ($page - 1) * $perPage;

// Base query
$query = "SELECT * FROM properties WHERE 1 ";
$params = [];
$types = '';

// Search filter
if ($search) {
    $query .= "AND (title LIKE ? OR location LIKE ?) ";
    $like_search = "%$search%";
    $params[] = $like_search;
    $params[] = $like_search;
    $types .= 'ss';
}

// Sorting
$order_by = 'created_at DESC';
if ($sort === 'price_asc') $order_by = 'price ASC';
if ($sort === 'price_desc') $order_by = 'price DESC';
if ($sort === 'date_asc') $order_by = 'created_at ASC';

$query .= "ORDER BY $order_by LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

// Prepare statement
$stmt = $conn->prepare($query);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Count total for pagination
$count_query = "SELECT COUNT(*) as total FROM properties WHERE 1 ";
$count_params = [];
$count_types = '';
if ($search) {
    $count_query .= "AND (title LIKE ? OR location LIKE ?) ";
    $count_params[] = $like_search;
    $count_params[] = $like_search;
    $count_types .= 'ss';
}
$stmt_count = $conn->prepare($count_query);
if ($count_params) $stmt_count->bind_param($count_types, ...$count_params);
$stmt_count->execute();
$total = $stmt_count->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Properties - David&Family Engineering Services</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $theme_file; ?>">
<link rel="stylesheet" href="assets/style.css">
<style>
/* Hover effect */
.card:hover .card-body {
    background: rgba(0, 0, 0, 0.05);
    transition: 0.3s;
}
.hero-properties .overlay-text {
    position: absolute;
    top:50%; left:50%;
    transform: translate(-50%, -50%);
    text-align:center;
    color:white;
}
.card-img-top {
    height: 250px;
    object-fit: cover;
}
</style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="container-fluid p-0 hero-properties position-relative">
    <img src="assets/images/office1.jpg" class="w-100 img-fluid" alt="Our Properties">
    <div class="overlay-text">
        <h1 class="fw-bold">Our Properties</h1>
        <p class="lead">Browse our residential, commercial, and land development projects.</p>
    </div>
</div>

<!-- Search & Sort -->
<section class="container py-4">
    <form method="GET" class="row g-3 align-items-center">
        <div class="col-md-6">
            <input type="text" name="search" class="form-control" placeholder="Search properties..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4">
            <select name="sort" class="form-select">
                <option value="date_desc" <?php if($sort=='date_desc') echo 'selected'; ?>>Newest First</option>
                <option value="date_asc" <?php if($sort=='date_asc') echo 'selected'; ?>>Oldest First</option>
                <option value="price_asc" <?php if($sort=='price_asc') echo 'selected'; ?>>Price Low to High</option>
                <option value="price_desc" <?php if($sort=='price_desc') echo 'selected'; ?>>Price High to Low</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Apply</button>
        </div>
    </form>
</section>

<!-- Properties Listing -->
<section class="container py-4">
    <div class="row g-4">
        <?php while($property = $result->fetch_assoc()): ?>
            <?php
            $stmt_img = $conn->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1");
            $stmt_img->bind_param("i", $property['id']);
            $stmt_img->execute();
            $img_res = $stmt_img->get_result();
            $img_row = $img_res->fetch_assoc();
            $img_path = $img_row ? $img_row['image_path'] : 'assets/images/no-image.jpg';
            ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="<?php echo $img_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($property['title']); ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($property['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($property['location']); ?></p>
                        <p class="card-text fw-bold text-primary">
                            <?php echo number_format(floatval(str_replace([',','N'], '', $property['price'])), 0, '.', ','); ?>
                        </p>
                        <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <?php if($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?php if($i==$page) echo 'active'; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
