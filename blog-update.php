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

// Get current information from Database
if (isset($_GET['id'])) {
    // Get page number for contacts.php for reditects
    $stmt = $pdo->prepare('WITH cte AS (SELECT `id`, ROW_NUMBER() OVER (ORDER BY `created` DESC) AS rn FROM `blog_post` WHERE `published`) SELECT rn FROM cte WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row_number = $stmt->fetchColumn();
    $return_page_number = floor($row_number / 10) + 1;

    // get post from the database
    $stmt = $pdo->prepare('SELECT * FROM `blog_post` WHERE `id` = ?');
    $stmt->execute([$_GET['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$post) {
        $validID = false;
        $responses[] = "A blog post did not exist with an ID of " . $_GET['id'] . ".";
    }
} else {
    $validID = false;
    $responses[] = "Please Provide a post ID.";
}

// Update the blog post after the form is submitted
if (isset($_POST['title'], $_POST['name'], $_POST['content'])) {
    // Check to see if any fields are empty
    if (empty($_POST['title']) || empty($_POST['name']) || empty($_POST['content'])) {
        $responses[] = "Please complete all fields";
    } else {

        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $author = isset($_POST['name']) ? $_POST['name'] : '';
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        $published = isset($_POST['published']) ? 1 : 0;

        $stmt = $pdo->prepare('UPDATE `blog_post` SET `author_name` = ?, `title` = ?, `content` = ?, `published` = ? WHERE `id` = ?');
        $stmt->execute([$author, $title, $content, $published, $_GET['id']]);

        header('Location: blog-admin.php?page=' . $return_page_number);
    }
}


?>

<?= template_header('Update Post') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Update Post</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<!-- START BLOG POST FORM -->
<?php if ($validID) : ?>
    <form action="" method="post">
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control has-icons-left">
                <input class="input" type="text" name="title" value="<?= $post['title'] ?>" require>
                <span class="icon is-left">
                    <i class="fas fa-tag"></i>
                </span>
            </div>
        </div>
        <!-- Name -->
        <div class="field">
            <label class="label">Name</label>
            <div class="control has-icons-left">
                <input class="input" type="text" name="name" value="<?= $post['author_name'] ?>" require>
                <span class="icon is-left">
                    <i class="fas fa-user-ninja"></i>
                </span>
            </div>
        </div>
        <!-- Content -->
        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" name="content" require><?= $post['content'] ?></textarea>
            </div>
        </div>
        <!-- Publish Checkbox -->
        <div class="field">
            <label class="label">Published?</label>
            <label class="checkbox">
                <?php if ($post['published'] == 1) : ?>
                    <input type="checkbox" name="published" checked>
                <?php else : ?>
                    <input type="checkbox" name="published">
                <?php endif ?>
                When checked, your post will be visible.
            </label>
        </div>
        <!-- Create Button -->
        <div class="field is-grouped is-grouped-left">
            <p class="control">
                <button class="button is-primary">
                    Update
                </button>
            </p>
            <!-- Cancel Button -->
            <p class="control">
                <a href="blog-admin.php?page=<?= $return_page_number ?>" class="button is-light">
                    Cancel
                </a>
            </p>
        </div>
    </form>
<?php endif; ?>
<!-- END BLOG POST FORM -->

<!-- END PAGE CONTENT -->

<?= template_footer() ?>