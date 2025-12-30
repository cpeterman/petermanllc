<?php
// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Configuration
$to_email = "your-email@petermanllc.com"; // Change this to your email
$subject_prefix = "Peterman LLC Contact Form";

// Sanitize and validate inputs
$name = strip_tags(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$subject = strip_tags(trim($_POST['subject'] ?? ''));
$message = strip_tags(trim($_POST['message'] ?? ''));

// Validate required fields
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    http_response_code(400);
    exit('Please fill in all required fields.');
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Please enter a valid email address.');
}

// Map subject values to readable names
$subject_map = [
    'home-services' => 'Home Services',
    'healthy-living' => 'Healthy Living',
    'lawn-care' => 'Lawn Care',
    'media-services' => 'Media Services',
    'other' => 'Other'
];
$subject_readable = $subject_map[$subject] ?? 'General Inquiry';

// Build email content
$email_subject = "$subject_prefix - $subject_readable";
$email_body = "New contact form submission from Peterman LLC website\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Service: $subject_readable\n\n";
$email_body .= "Message:\n$message\n";

// Email headers
$headers = [
    "From: noreply@" . $_SERVER['HTTP_HOST'],
    "Reply-To: $email",
    "X-Mailer: PHP/" . phpversion(),
    "Content-Type: text/plain; charset=UTF-8"
];

// Send email
if (mail($to_email, $email_subject, $email_body, implode("\r\n", $headers))) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}
?>