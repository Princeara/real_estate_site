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

// Handle normal form submission
$success = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $msg_content = trim($_POST['message']);
    $property_id = isset($_POST['property_id']) && intval($_POST['property_id']) > 0 ? intval($_POST['property_id']) : null;

    if (!$name || !$email || !$msg_content) {
        $message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } else {
        if ($property_id === null) {
            $stmt = $conn->prepare("INSERT INTO inquiries (name, email, message, property_id) VALUES (?, ?, ?, NULL)");
            $stmt->bind_param("sss", $name, $email, $msg_content);
        } else {
            $stmt = $conn->prepare("INSERT INTO inquiries (name, email, message, property_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $email, $msg_content, $property_id);
        }

        if ($stmt->execute()) {
            $success = true;
            $message = 'Thank you! Your inquiry has been sent.';
        } else {
            $message = 'An error occurred. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - David&Family Engineering Services</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $theme_file; ?>">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="container py-5">
    <h2 class="text-center mb-4">Contact Us</h2>

    <?php if($message): ?>
        <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> text-center">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="mx-auto" style="max-width:600px;" novalidate>
        <input type="hidden" name="property_id" value="<?php echo intval($_GET['property'] ?? 0); ?>">
        <input type="hidden" name="action" value="contact">

        <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Your Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Your Message</label>
            <textarea class="form-control" name="message" id="message" rows="5" required></textarea>
        </div>

        <button class="btn btn-primary w-100" type="submit">Send Message</button>
    </form>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
