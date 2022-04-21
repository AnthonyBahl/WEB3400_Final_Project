<?php
require 'config.php';

// Start the session
session_start();

//check to see if the form was submitted
if (isset($_POST['name'], $_POST['email'], $_POST['subject'], $_POST['message'])) {
    // Validate the email address
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $responses[] = "Email is not valid";
    }
    // Make sure that the form fields are not empty
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        $responses[] = "Please complete all fields";
    }
    // If there are no errors
    if (!$responses) {
        // Send the email
        $to = 'gtuck@weber.edu';
        $from = 'noreply@weber.edu';
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $_POST['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();

        // Attempt to send message
        if (mail($to, $subject, $message, $headers)) {
            $responses[] = 'Message sent!';
        } else {
            $responses[] = 'There was an error sending the message!';
        }
    }
}
?>

<?= template_header() ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Contact us</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>
<!-- contact form using the bulma.io syntax goes here -->
<form action="" method="post">

    <!-- Name -->
    <div class="field">
        <label class="label">Name</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="name" placeholder="Anthony Bahl" require>
            <span class="icon is-left">
                <i class="fas fa-user-ninja"></i>
            </span>
        </div>
    </div>

    <!-- Email -->
    <div class="field">
        <label class="label">Email</label>
        <div class="control has-icons-left">
            <input class="input" type="email" name="email" placeholder="ex. abahl@example.com" require>
            <span class="icon is-left">
                <i class="fas fa-at"></i>
            </span>
        </div>
    </div>

    <!-- Subject -->
    <div class="field">
        <label class="label">Subject</label>
        <div class="control">
            <input class="input" type="text" name="subject" placeholder="Enter subject here" require>
        </div>
    </div>

    <!-- Message -->
    <div class="field">
        <label class="label">Message</label>
        <div class="control">
            <textarea class="textarea" name="message" placeholder="Let us know what's on your mind..."></textarea>
        </div>
    </div>

    <!-- Button -->
    <div class="field">
        <div class="control">
            <button class="button is-primary">
                <span class="icon">
                    <i class="fas fa-paper-plane"></i>
                </span>
                <span>Send Message</span>
            </button>
        </div>
    </div>

</form>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>