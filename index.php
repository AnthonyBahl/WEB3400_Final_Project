<?php
// Database: https://icarus.cs.weber.edu/phpmyadmin/index.php
?>

<?php
require 'config.php';

// Start the session
session_start();

if (!isset($_GET['page'])) {
    header('Location: ?page=1');
} else {
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

<?= template_header() ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<!-- Responses -->
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
                        <a class="pagination-previous" href="?page=<?= $_GET['page'] - 1 ?>">Previous</a>
                    <?php else : ?>
                        <span class="pagination-previous" disabled>Previous</span>
                    <?php endif; ?>
                    <?php if ($total_pages > $_GET['page']) : ?>
                        <a class="pagination-next" href="?page=<?= $_GET['page'] + 1 ?>">Next page</a>
                    <?php else : ?>
                        <span class="pagination-next" disabled>Next page</span>
                    <?php endif; ?>
                    <ul class="pagination-list">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $_GET['page']) {
                                echo "<li><a href='?page=$i'><u>" . $i . "</u>&nbsp;</a></li>";
                            } else {
                                echo "<li><a href='?page=$i'>" . $i . "&nbsp;</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </nav>
            <?php endif; ?>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>