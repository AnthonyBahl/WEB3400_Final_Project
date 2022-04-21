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

if (!isset($_GET['page'])) {
    header('Location: admin.php?page=1');
} else {
    // use PDO to connect to our database
    $pdo = pdo_connect_mysql();

    // Get Total Page Count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `blog_post` WHERE `published`');
    $stmt->execute();
    $total_rows = $stmt->fetchColumn();
    $total_pages = ceil($total_rows / 10);

    // get 10 most recent blog posts
    $lowerLimit = ($_GET['page'] - 1) * 10;
    $stmt = $pdo->prepare("SELECT `id`, `title`, `author_name`, DATE_FORMAT(`created`, '%M %D %Y') AS date_created, LEFT(`content`, 100) AS content_preview
                     FROM `blog_post`
                     WHERE `published`
                     ORDER BY `created` DESC
                     LIMIT ?, 10");
    $stmt->bindParam(1, $lowerLimit, PDO::PARAM_INT);
    $stmt->execute();
    $recentBlogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?= template_header('Admin Dashboard') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<div class="columns">
    <!-- START LEFT NAV COLUMN -->
    <div class="column is-one-fifth">
        <?= admin_nav(basename(__FILE__)) ?>
    </div>
    <!-- END LEFT NAV COLUMN -->
    <!-- START RIGHT CONTENT COLUMN-->
    <div>
        <h1 class="title">Recent Blog Posts</h1>
        <?php if ($responses) : ?>
            <p class="notification is-danger is-light">
                <?php echo implode('<br>', $responses); ?>
            </p>
        <?php endif; ?>
        <?php foreach ($recentBlogPosts as $post) : ?>
            <div class="box">
                <h1 class="title is-4"><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h1>
                <h2 class="subtitle is-6"><?= htmlspecialchars($post['author_name'], ENT_QUOTES) ?>
                    -
                    <?= htmlspecialchars($post['date_created'], ENT_QUOTES) ?></h2>
                <p>
                    <?= htmlspecialchars($post['content_preview'], ENT_QUOTES) ?>
                    ... <a href="blog-post.php?id=<?= $post['id'] ?>">read more</a>
                </p>
            </div>
        <?php endforeach ?>
        <?php if ($total_pages > 1) : ?>
                <nav class="pagination" role="navigation" aria-label="pagination">
                    <?php if ($_GET['page'] > 1) : ?>
                        <a class="pagination-previous" href="admin.php?page=<?= $_GET['page'] - 1 ?>">Previous</a>
                    <?php else : ?>
                        <span class="pagination-previous" disabled>Previous</span>
                    <?php endif; ?>
                    <?php if ($total_pages > $_GET['page']) : ?>
                        <a class="pagination-next" href="admin.php?page=<?= $_GET['page'] + 1 ?>">Next page</a>
                    <?php else : ?>
                        <span class="pagination-next" disabled>Next page</span>
                    <?php endif; ?>
                    <ul class="pagination-list">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $_GET['page']) {
                                echo "<li><a href='admin.php?page=$i'><u>" . $i . "</u>&nbsp;</a></li>";
                            } else {
                                echo "<li><a href='admin.php?page=$i'>" . $i . "&nbsp;</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </nav>
            <?php endif; ?>
    </div>
    <!-- END RIGHT CONTENT COLUMN-->
</div>
<!-- END PAGE CONTENT -->
<?= template_footer() ?>