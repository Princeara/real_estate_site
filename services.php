<?php

define('ACCESS', true); // Prevent direct script access
include 'includes/db.php';

// Fetch active theme securely
$stmt_theme = $conn->prepare("SELECT site_theme FROM settings LIMIT 1");
$stmt_theme->execute();
$result_theme = $stmt_theme->get_result();
$theme_row = $result_theme->fetch_assoc();

// Theme mapping
$theme_map = [
    'green'   => 'theme-olive.css',
    'elegant' => 'theme-bright.css',
    'premium' => 'theme-deep.css',
    'teal'    => 'theme-teal.css',
    'dark'    => 'theme-dark.css'
];

$theme_file = 'assets/themes/' . ($theme_map[$theme_row['site_theme']] ?? 'theme-olive.css');

// Fetch latest 3 properties with first image in a single query (optimized)
$stmt_props = $conn->prepare("
    SELECT p.id, p.title, p.location, pi.image_path
    FROM properties p
    LEFT JOIN property_images pi ON p.id = pi.property_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 3
");
$stmt_props->execute();
$property_result = $stmt_props->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Services - David&Family Engineering Services</title>
<meta name="description" content="Explore the professional services offered by David&Family Engineering Services, including property development, construction, and civil engineering solutions.">
<meta name="keywords" content="Civil Engineering, Construction, Property Development, Real Estate, Engineering Services">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $theme_file; ?>">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* Service Cards Hover Effect */
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

/* Featured Properties Card Hover */
.featured-card:hover .btn-primary {
    background-color: #0056b3;
    transition: background-color 0.3s ease;
}

.card-img-top {
    width: 100%;
    object-fit: cover;
    height: 180px; /* mobile */
}

@media (min-width: 768px) {
    .card-img-top {
        height: 220px; /* tablet and desktop */
    }
}

@media (min-width: 1200px) {
    .card-img-top {
        height: 250px; /* large screens */
    }
}

</style>

</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="container-fluid p-0 position-relative">
    <img src="assets/images/banner1.jpg" class="img-fluid w-100" alt="Our Services">
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white px-3" style="background: rgba(0,0,0,0.4); border-radius:10px;">
        <h1 class="fw-bold">Our Services</h1>
        <p class="lead">High-quality engineering and property solutions delivered with excellence</p>
    </div>
</section>

<!-- Services Section -->
<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">What We Offer</h2>
        <p class="lead">Delivering professional services with innovation, safety, and quality at the core.</p>
    </div>

    <div class="row g-4">
        <?php
        // Example services array (populate dynamically if needed)
        $services = [
            ['title'=>'Construction','desc'=>'High-quality residential and commercial construction projects delivered safely and on time.','img'=>'assets/images/services1.jpg'],
            ['title'=>'Civil Engineering','desc'=>'Structural design, site analysis, and project management for optimal results.','img'=>'assets/images/services2.jpg'],
            ['title'=>'Land Development','desc'=>'Planning, surveying, and developing land for residential and commercial use.','img'=>'assets/images/services3.jpg'],
            ['title'=>'Project Management','desc'=>'Ensuring projects are delivered on time, within budget, and to the highest quality standards.','img'=>'assets/images/project.jpg'],
            ['title'=>'Consulting','desc'=>'Professional advice on construction, real estate, and civil engineering projects.','img'=>'assets/images/consulting.jpg'],
            ['title'=>'Sustainable Solutions','desc'=>'Eco-friendly construction and land development solutions that prioritize sustainability.','img'=>'assets/images/sustain.jpg']
        ];

        foreach($services as $service):
        ?>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card h-100 service-card">
                <img src="<?php echo $service['img']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['title']); ?>" loading="lazy">
                <div class="card-body text-center">
                    <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($service['title']); ?></h4>
                    <p><?php echo htmlspecialchars($service['desc']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Properties / Media Section -->
<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Our Projects & Media</h2>
        <p class="lead">A glimpse of some of our recent work and properties developed.</p>
    </div>

    <div class="row g-4">
        <?php while($property = $property_result->fetch_assoc()): ?>
            <?php $img_path = $property['image_path'] ?: 'assets/images/no-image.jpg'; ?>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card h-100 featured-card">
                    <img src="<?php echo $img_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($property['title']); ?>" loading="lazy">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($property['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($property['location']); ?></p>
                        <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- TESTIMONIALS CAROUSEL SECTION -->
<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">What Our Clients Say</h2>
        <p class="lead">Trusted by clients across Nigeria for quality, professionalism, and reliability.</p>
    </div>

    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

            <?php
            // Example testimonials (replace with database fetch if needed)
            $testimonials = [
                [
                    'name' => 'Mr. Adeola',
                    'position' => 'Property Investor, Lagos',
                    'text' => 'D&F Engineering delivered my residential project on time and exceeded expectations. Highly recommended!',
                    'img' => 'assets/images/testimonial1.jpg'
                ],
                [
                    'name' => 'Mrs. Chukwu',
                    'position' => 'Entrepreneur, Abuja',
                    'text' => 'Professional, reliable, and innovative team. Their engineering solutions are top-notch.',
                    'img' => 'assets/images/testimonial2.jpg'
                ],
                [
                    'name' => 'Engr. Bello',
                    'position' => 'Construction Consultant, Port Harcourt',
                    'text' => 'Their attention to detail and project management skills are unmatched. A pleasure to work with!',
                    'img' => 'assets/images/testimonial3.jpg'
                ]
            ];

            foreach($testimonials as $index => $t):
            ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="d-flex flex-column align-items-center text-center">
                    <img src="<?php echo $t['img']; ?>" class="rounded-circle mb-3" alt="<?php echo $t['name']; ?>" style="width:100px; height:100px; object-fit:cover;">
                    <p class="text-muted px-3 px-md-5"><?php echo $t['text']; ?></p>
                    <h6 class="fw-bold mb-0"><?php echo $t['name']; ?></h6>
                    <small class="text-muted"><?php echo $t['position']; ?></small>
                </div>
            </div>
            <?php endforeach; ?>

        </div>

        <!-- Carousel controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>

        <!-- Carousel indicators -->
        <div class="carousel-indicators mt-3">
            <?php foreach($testimonials as $index => $t): ?>
                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
