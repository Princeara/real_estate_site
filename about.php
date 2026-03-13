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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About David&Family Engineering Services - Engineering Excellence, Delivered</title>

<!-- SEO -->
<meta name="description" content="Learn about David&Family Engineering Services, expert civil engineers specializing in property development and construction projects.">
<meta name="keywords" content="Civil Engineering, Construction, Real Estate, Property Development">

<!-- Open Graph -->
<meta property="og:title" content="David&Family Engineering Services">
<meta property="og:description" content="Expert civil engineers delivering property development and real estate solutions in Nigeria.">
<meta property="og:image" content="assets/images/banner1.jpg">
<meta property="og:url" content="https://yourdomain.com/about.php">
<meta property="og:type" content="website">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Theme & Main Styles -->
<link rel="stylesheet" href="<?php echo $theme_file; ?>">
<link rel="stylesheet" href="assets/style.css">

<!-- Inline Styles for About Page -->
<style>
/* HERO OVERLAY */
.hero-overlay {
    background: rgba(0,0,0,0.45);
    border-radius: 10px;
    padding: 1.5rem;
}
.hero-overlay h1 {
    font-size: 3rem;
}
.hero-overlay p.lead {
    font-size: 1.25rem;
}

/* OUR STORY */
.our-story-row img {
    width: 100%;
    height: auto;
    object-fit: cover;
}
.our-story-row .text-col {
    display: flex;
    align-items: center;
}

/* MVV CARDS */
.mvv-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: #fff;
}
.mvv-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

/* RESPONSIVE HERO TEXT */
@media (max-width: 768px) {
    .hero-overlay h1 { font-size: 2rem; }
    .hero-overlay p.lead { font-size: 1rem; }
}
</style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<div class="container-fluid p-0 position-relative">
    <img src="assets/images/banner1.jpg" class="img-fluid w-100" alt="About Us">
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white hero-overlay">
        <h1 class="fw-bold">David&Family Engineering Services</h1>
        <p class="lead">Excellence in Civil Engineering & Real Estate Development</p>
    </div>
</div>

<!-- OUR STORY -->
<section class="container py-5">
    <div class="row g-4 align-items-center our-story-row">
        <div class="col-md-6">
            <img src="assets/images/services1.jpg" class="img-fluid rounded shadow-sm" alt="Construction and Land Development">
        </div>
        <div class="col-md-6 text-col">
            <div class="my-auto">
                <h2 class="fw-bold mb-4">Our Story</h2>
                <p>David Adegbaju is a seasoned civil engineer with years of experience in construction, land development, and real estate management. We focus on delivering premium-quality services and projects that stand the test of time.</p>
                <p>From residential homes to commercial projects, our mission is to provide innovative, safe, and sustainable solutions for every client.</p>
                <p>Over the years, we have successfully completed numerous property development projects, transforming landscapes and communities with precision and professionalism.</p>
            </div>
        </div>
    </div>
</section>

<!-- MISSION, VISION & VALUES -->
<section class="container py-5 bg-light rounded shadow-sm">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Our Mission, Vision & Values</h2>
        <p class="lead">Guiding principles that drive David&Family Engineering Services forward.</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="mvv-card p-4 rounded shadow-sm h-100 text-center">
                <div class="mb-3"><i class="bi bi-flag-fill fs-1"></i></div>
                <h4 class="fw-bold mb-3">Mission</h4>
                <p>Deliver high-quality property development and civil engineering projects that combine innovation, sustainability, and client satisfaction.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mvv-card p-4 rounded shadow-sm h-100 text-center">
                <div class="mb-3"><i class="bi bi-eye-fill fs-1"></i></div>
                <h4 class="fw-bold mb-3">Vision</h4>
                <p>To be the leading engineering and property development company in the region, recognized for excellence, integrity, and transformative projects.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mvv-card p-4 rounded shadow-sm h-100 text-center">
                <div class="mb-3"><i class="bi bi-stars fs-1"></i></div>
                <h4 class="fw-bold mb-3">Values</h4>
                <p>Integrity, professionalism, innovation, client satisfaction, and sustainable development guide every project we undertake.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
