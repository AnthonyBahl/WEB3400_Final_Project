<?php
// Database: https://icarus.cs.weber.edu/phpmyadmin/index.php
?>

<?php
require 'config.php';

// Start the session
session_start();

// If the user is not logged in redirect them to the login page
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// use PDO to connect to our database
$pdo = pdo_connect_mysql();

$validID = true;

if (isset($_GET['id'])) {
    // Get page number for contacts.php for reditects
    $stmt = $pdo->prepare('WITH cte AS (SELECT `id`, ROW_NUMBER() OVER (ORDER BY `created` DESC) AS rn FROM `blog_post` WHERE `published`) SELECT rn FROM cte WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row_number = $stmt->fetchColumn();
    $return_page_number = floor($row_number / 10) + 1;

    $stmt = $pdo->prepare('SELECT `title` FROM `blog_post` WHERE `id` = ?');
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$post) {
        $validID = false;
        $responses[] = "A post did not exist with that ID.";
    } else {
        if (isset($_GET['confirm'])) {
            if ($_GET['confirm'] == 'yes') {
                // Delete Record
                $stmt = $pdo->prepare('DELETE FROM `blog_post` WHERE `id` = ?');
                $stmt->execute([$_GET['id']]);

            }
            header('Location: blog-admin.php?page=' . $return_page_number);
        }
    }
} else {
    $validID = false;
    $responses[] = "Please provide an ID.";
}


?>

<?= template_header('Delete Post') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Page Heading</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>
<?php if($validID) : ?>
<p>Are you sure you want to delete this post?</p>
<?= $post['title'] ?> ?

<div class="buttons">
    <a href="?id=<?= $_GET['id'] ?>&confirm=yes" class="button is-success">Yes</a>
    <a href="?id=<?= $_GET['id'] ?>&confirm=no" class="button is-danger">No</a>
</div>
<?php endif; ?>

<!-- END PAGE CONTENT -->

<?= template_footer() ?>