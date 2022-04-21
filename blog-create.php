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

$pdo = pdo_connect_mysql();

if (isset($_POST['title'], $_POST['name'], $_POST['content'])) {
    // Check to see if any fields are empty
    if (empty($_POST['title']) || empty($_POST['name']) || empty($_POST['content'])) {
        $responses[] = "Please complete all fields";
    } else {

        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $author = isset($_POST['name']) ? $_POST['name'] : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $published = isset($_POST['published']) ? 1 : 0;

        $stmt = $pdo->prepare('INSERT INTO `blog_post`(`author_name`, `title`, `content`, `published`, `created`) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$author, $title, $content, $published]);

        $post_id = $pdo->lastInsertId();

        $headerLocation = 'Location: blog-post.php?id=' . $post_id;
        header($headerLocation);
    }
}

?>

<?= template_header('Create Post') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Create Blog Post</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<!-- START BLOG POST FORM -->
<form action="" method="post">
    <!-- Title -->
    <div class="field">
        <label class="label">Title</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="title" placeholder="Blog Post Title..." require>
            <span class="icon is-left">
                <i class="fas fa-tag"></i>
            </span>
        </div>
    </div>
    <!-- Name -->
    <div class="field">
        <label class="label">Name</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="name" placeholder="John Doe" require>
            <span class="icon is-left">
                <i class="fas fa-user-ninja"></i>
            </span>
        </div>
    </div>
    <!-- Content -->
    <div class="field">
        <label class="label">Content</label>
        <div class="control">
            <textarea class="textarea" name="content" placeholder="Blog content goes here..." require></textarea>
        </div>
    </div>
    <!-- Publish Checkbox -->
    <div class="field">        
        <label class="label">Published?</label>
        <label class="checkbox">
            <input type="checkbox" name="published" checked>
            When checked, your post will be visible.
        </label>
    </div>
    <!-- Create Button -->
    <div class="field is-grouped is-grouped-left">
        <p class="control">
            <button class="button is-primary">
                Create
            </button>
        </p>
        <!-- Cancel Button -->
        <p class="control">
            <a href="blog-admin.php" class="button is-light">
                Cancel
            </a>
        </p>
    </div>
</form>
<!-- END BLOG POST FORM -->

<!-- END PAGE CONTENT -->

<?= template_footer() ?>