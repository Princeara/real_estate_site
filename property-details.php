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

// Get property ID
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch property details
$stmt_prop = $conn->prepare("SELECT * FROM properties WHERE id=? LIMIT 1");
$stmt_prop->bind_param("i", $property_id);
$stmt_prop->execute();
$result_prop = $stmt_prop->get_result();
$property = $result_prop->fetch_assoc();

if (!$property) {
    die("Property not found.");
}

// Fetch property images
$stmt_img = $conn->prepare("SELECT * FROM property_images WHERE property_id=?");
$stmt_img->bind_param("i", $property_id);
$stmt_img->execute();
$result_imgs = $stmt_img->get_result();
$images = [];
while ($row = $result_imgs->fetch_assoc()) {
    $images[] = $row['image_path'];
}

// Ensure number_format works for price
$price_num = floatval(str_replace(['N',','],'',$property['price']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($property['title']); ?> - David&Family Engineering Services</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $theme_file; ?>">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
/* Hover card overlay not used here but consistent style */
.property-carousel img {
    width: 100%;           /* full width */
    max-height: 600px;     /* adjust height as needed */
    object-fit: cover;     /* crop without distortion */
    border-radius: 8px;    /* optional for styling */
}

.carousel-inner img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

</style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <!-- Images Carousel -->
    <?php if(count($images) > 0): ?>
    <div id="propertyCarousel" class="carousel slide mb-5 property-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach($images as $index => $img_path): ?>
            <div class="carousel-item <?php if($index==0) echo 'active'; ?>">
                <img src="<?php echo $img_path; ?>" class="d-block w-100" alt="Property Image <?php echo $index+1; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <?php if(count($images) > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        <?php endif; ?>
    </div>

    <!-- Property Title & Price -->
    <div class="text-center mb-4">
        <h1 class="fw-bold"><?php echo htmlspecialchars($property['title']); ?></h1>
        <p class="lead text-primary fw-bold">N<?php echo number_format($price_num); ?></p>
        <p class="text-muted"><?php echo htmlspecialchars($property['location']); ?></p>
    </div>

    <?php endif; ?>

    <!-- Description -->
    <div class="text-center mb-4">
        <h3 class="fw-bold">Property Description</h3>
        <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
    </div>

    <div>
        <p></p>
        <p></p>
    </div>

    <!-- Inquiry Form -->
    <div class="text-center mb-4 mt-5">
        <h3 class="fw-bold">Inquire About This Property</h3>
        <?php
        // Handle form submission
        $form_success = false;
        $form_message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (!$name || !$email || !$message) {
                $form_message = "Please fill all fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $form_message = "Please enter a valid email.";
            } else {
                $stmt_inq = $conn->prepare("INSERT INTO inquiries (name,email,message,property_id) VALUES (?,?,?,?)");
                $stmt_inq->bind_param("sssi", $name,$email,$message,$property_id);
                if ($stmt_inq->execute()) {
                    $form_success = true;
                    $form_message = "Thank you! Your inquiry has been sent.";
                } else {
                    $form_message = "An error occurred. Please try again.";
                }
            }
        }
        ?>
        <?php if($form_message): ?>
            <div class="alert <?php echo $form_success ? 'alert-success':'alert-danger'; ?>">
                <?php echo $form_message; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="mx-auto" style="max-width:600px;">
            <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Your Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" required></textarea>
            </div>
            <button class="btn btn-primary w-100" type="submit">Send Inquiry</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
