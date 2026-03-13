<?php
define('ACCESS', true);
include 'includes/db.php';
include 'includes/functions.php';

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
    'dark'    => 'theme-dark.css',
    'blue'    => 'theme-blue.css' // Added blue theme
];

$theme_file = 'assets/themes/' . ($theme_map[$theme_row['site_theme']] ?? 'theme-olive.css');

// Fetch featured properties with first image
$stmt_props = $conn->prepare("
    SELECT p.*, pi.image_path
    FROM properties p
    LEFT JOIN property_images pi ON p.id = pi.property_id
    WHERE p.is_featured = 1
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
<title>David&Family Engineering Services - Engineering Excellence, Delivered</title>

<!-- SEO -->
<meta name="description" content="David&Family Engineering Services offers professional property development, land sales, and real estate solutions across Nigeria.">
<meta name="keywords" content="Engineering, Real Estate, Property Development, Construction, Land Sales, Nigeria">

<!-- Open Graph -->
<meta property="og:title" content="David&Family Engineering Services">
<meta property="og:description" content="Professional property development, land sales, and engineering solutions in Nigeria.">
<meta property="og:image" content="assets/images/og-image.jpg">
<meta property="og:url" content="https://yourdomain.com/">
<meta property="og:type" content="website">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Main Styles -->
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="<?php echo $theme_file; ?>">

</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        $slides = [
            ['img'=>'assets/images/slider1.jpg','title'=>'Engineering Excellence, Delivered.','desc'=>'Property Development, Land Sales & Real Estate Solutions','cta1'=>'properties.php','cta1_text'=>'Explore Properties','cta2'=>'services.php','cta2_text'=>'Our Services'],
            ['img'=>'assets/images/slider2.jpg','title'=>'Quality You Can Trust','desc'=>'Professional Project Handling from Start to Finish','cta1'=>'services.php','cta1_text'=>'Our Services'],
            ['img'=>'assets/images/slider3.jpg','title'=>'Land. Development. Engineering.','desc'=>'Innovative Solutions for Homes, Estates & Infrastructure','cta1'=>'contact.php','cta1_text'=>'Contact Us']
        ];

        foreach($slides as $index => $slide):
        ?>
        <div class="carousel-item <?php echo $index===0?'active':''; ?>">
            <img src="<?php echo $slide['img']; ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($slide['title']); ?>">
            <div class="carousel-caption d-block text-center">
                <h1 class="fw-bold"><?php echo htmlspecialchars($slide['title']); ?></h1>
                <p class="lead"><?php echo htmlspecialchars($slide['desc']); ?></p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <?php if(isset($slide['cta1'])): ?>
                        <a href="<?php echo $slide['cta1']; ?>" class="btn btn-primary btn-lg"><?php echo $slide['cta1_text']; ?></a>
                    <?php endif; ?>
                    <?php if(isset($slide['cta2'])): ?>
                        <a href="<?php echo $slide['cta2']; ?>" class="btn btn-outline-light btn-lg"><?php echo $slide['cta2_text']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- ABOUT SECTION -->
<section class="container py-5 text-center">
    <h2 class="fw-bold mb-3">About Us</h2>
    <p class="lead">
        At D&F Engineering, we specialize in delivering world-class engineering and property solutions across Nigeria. 
        From residential and commercial property development to land acquisition, estate management, and professional engineering services, our team ensures quality, safety, and sustainability in every project.
        We specialize in property development, land acquisition, estate management, and professional engineering services across Nigeria.
    </p>
</section>

<!-- SERVICES SECTION -->
<section class="container py-5 services-section">
    <h2 class="text-center fw-bold mb-5">Our Services</h2>
    <div class="row g-4">
        <?php
        $example_services = [
            ['icon'=>'bi-building','title'=>'Property Development','desc'=>'Designing and constructing modern residential and commercial properties.'],
            ['icon'=>'bi-map','title'=>'Land Acquisition','desc'=>'Acquiring prime land locations across Nigeria for development.'],
            ['icon'=>'bi-house','title'=>'Estate Management','desc'=>'Comprehensive estate and property management services.'],
            ['icon'=>'bi-tools','title'=>'Engineering Solutions','desc'=>'Professional engineering services for construction and infrastructure.']
        ];
        foreach($example_services as $service):
        ?>
        <div class="col-md-3 col-sm-6">
            <div class="card text-center p-4 h-100">
                <i class="bi <?php echo $service['icon']; ?> mb-3" style="font-size:2rem;color:var(--primary-color);"></i>
                <h5 class="fw-bold"><?php echo $service['title']; ?></h5>
                <p><?php echo $service['desc']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- FEATURED PROPERTIES -->
<section class="container py-5 featured-properties">
    <h2 class="text-center fw-bold mb-4">Featured Properties</h2>
    <div class="row g-4 justify-content-center">
        <?php while($property = $property_result->fetch_assoc()): ?>
            <?php $img_path = $property['image_path'] ?: 'assets/images/no-image.jpg'; ?>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card h-100 shadow-sm position-relative">
                    <img src="<?php echo htmlspecialchars($img_path); ?>" class="card-img-top" loading="lazy" alt="<?php echo htmlspecialchars($property['title']); ?>">
                    <?php if($property['is_featured']): ?>
                        <span class="property-badge">Featured</span>
                    <?php endif; ?>
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($property['title']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($property['location']); ?></p>
                        <p class="card-text"><?php echo htmlspecialchars(excerpt($property['description'],110)); ?></p>
                        <p class="card-text fw-bold text-primary"><?php echo format_price($property['price']); ?></p>
                        <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="text-center mt-4">
        <a href="properties.php" class="btn btn-outline-primary btn-lg">View All Properties</a>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="container py-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold">Why Choose Us</h3>
            <p class="text-muted">We combine technical engineering expertise with local market knowledge to deliver high-quality developments across Nigeria.</p>
            <div class="row gy-3 mt-4">
                <?php
                $features = [
                    ['title'=>'Quality Construction','desc'=>'Durability & standards-focused delivery'],
                    ['title'=>'Professional Design','desc'=>'Functional layouts & modern finishes'],
                    ['title'=>'Prime Locations','desc'=>'Carefully selected sites across major cities'],
                    ['title'=>'Verified Listings','desc'=>'Transparent transactions & verified titles']
                ];
                foreach($features as $feature):
                ?>
                <div class="col-6">
                    <div class="p-3 border rounded">
                        <h6 class="fw-bold mb-1"><?php echo $feature['title']; ?></h6>
                        <small class="text-muted"><?php echo $feature['desc']; ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="assets/images/property.jpg" alt="Why choose us" class="img-fluid rounded shadow-sm" style="max-height:360px;object-fit:cover;">
        </div>
    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="container py-5 testimonials-section">
    <h2 class="text-center fw-bold mb-5">What Our Clients Say</h2>

    <div class="row g-4 justify-content-center">

        <?php
        // Example testimonials
        $testimonials = [
            [
                'quote' => 'D&F Engineering delivered our residential project on time and within budget. Professional team and excellent communication!',
                'name'  => 'Mr. Chukwuma, Lagos',
                'role'  => 'Homeowner'
            ],
            [
                'quote' => 'Their engineering solutions helped our commercial estate meet the highest standards. Highly recommend!',
                'name'  => 'Mrs. Adebayo, Abuja',
                'role'  => 'Property Developer'
            ],
            [
                'quote' => 'Reliable and transparent. We partnered on multiple land acquisitions with D&F Engineering and were impressed by their expertise.',
                'name'  => 'Engr. Musa, Port Harcourt',
                'role'  => 'Civil Engineer'
            ]
        ];
        ?>

        <?php foreach($testimonials as $testi): ?>
        <div class="col-md-4">
            <div class="card shadow-sm p-4 h-100 text-center">
                <i class="bi bi-chat-quote-fill text-primary fs-1 mb-3"></i>
                <p class="testimonial-quote">"<?php echo htmlspecialchars($testi['quote']); ?>"</p>
                <h6 class="fw-bold mt-3 mb-0"><?php echo htmlspecialchars($testi['name']); ?></h6>
                <small class="text-muted"><?php echo htmlspecialchars($testi['role']); ?></small>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<!-- CTA SECTION -->
<section class="section-cta">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Start Your Real Estate Journey Today</h2>
        <p class="lead mb-4">Get in touch with us for property inquiries, site inspections, or partnership opportunities.</p>
        <a href="contact.php" class="btn btn-contact btn-lg px-5">Contact Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- WhatsApp floating button -->
<div class="whatsapp-float">
    <a href="https://wa.me/2348103349503?text=Hello%20D%26F%20Engineering%21%20I%20am%20interested%20in%20a%20property." target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <span style="font-size:18px;">💬</span>
        <span class="d-none d-sm-inline">Chat Us</span>
    </a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
