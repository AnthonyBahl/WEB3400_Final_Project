<?php
// Database: https://icarus.cs.weber.edu/phpmyadmin/index.php
?>

<?php
require 'config.php';

// Start the session
session_start();

//additional php code for this page goes here

?>

<?= template_header() ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Page Heading</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<!-- END PAGE CONTENT -->

<?= template_footer() ?>