<?php
$errors = [];
$success = false;

function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// Only handle POST submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? ''); // honeypot
    if ($address !== '') {
        // If honeypot filled, treat as spam silently
        $errors[] = 'Spam detected.';
    }

    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($fname === '') { $errors[] = 'First name is required.'; }
    if ($lname === '') { $errors[] = 'Last name is required.'; }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid email address is required.'; }
    if ($phone === '') { $errors[] = 'Phone number is required.'; }
    if ($message === '') { $errors[] = 'Message is required.'; }

    // Basic header injection prevention
    $injectPattern = '/[\r\n]|content-type:|bcc:|cc:/i';
    foreach ([$fname, $lname, $email, $phone] as $field) {
        if (preg_match($injectPattern, $field)) {
            $errors[] = 'Invalid input detected.';
            break;
        }
    }

    if (empty($errors)) {
        $to = 'dankasagan@gmail.com';
        $subject = 'Contact form: ' . $fname . ' ' . $lname;
        $body = "Name: " . $fname . " " . $lname . "\n";
        $body .= "Email: " . $email . "\n";
        $body .= "Phone: " . $phone . "\n\n";
        $body .= "Message:\n" . $message . "\n";

        $serverDomain = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $from = 'noreply@' . $serverDomain;
        $headers = [];
        $headers[] = 'From: ' . $from;
        // Add Reply-To so recipient can reply to user
        $headers[] = 'Reply-To: ' . $email;
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $ok = @mail($to, $subject, $body, implode("\r\n", $headers));
        if ($ok) {
            $success = true;
        } else {
            $errors[] = 'Failed to send email. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact | SCD</title>
        <link rel="stylesheet" href="contact.css">
        <link rel="shortcut icon" href="images/Logo.png" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    </head>
    <body>
        <header>
            <a class="logoA" href="index.html">
                <img src="images/Logo.png" alt="SCD Logo" class="logo">
                <h1>Specialized Concrete Designers</h1>
            </a>
            <div>
                <div class="list">
                    <a href="index.html">Home</a>
                    <a href="sevices.html">Services</a>
                    <a href="contact.html" class="buttonThing">Contact</a>
                </div>
            </div>
        </header>
        <main>
            <section id="php-confirm">
                <?php if ($success): ?>
                    <h3>
                        Thank you for contacting Specialized Concrete Designers. We have received your message and will get back to you shortly.
                    </h3>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div style="max-width:60vw;margin:4vh auto;padding:20px;border:1px solid #cc0000;border-radius:8px;background:#fff6f6;">
                            <strong>Please correct the following:</strong>
                            <ul>
                                <?php foreach ($errors as $e): ?>
                                    <li><?php echo h($e); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div style="text-align:center;margin-top:6vh;">
                        <p>If you were not redirected, you can <a href="contact.html">return to the form</a> and try again.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
        <footer>
            <div class="footer-text">
                &copy; 2025 SCD. All rights reserved.
            </div>
        </footer>
    </body>
</html>