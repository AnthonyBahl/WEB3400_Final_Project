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
    header('Location: blog-admin.php?page=1');
} else {

    $pdo = pdo_connect_mysql();

    // Get total Page Count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `blog_post` WHERE `published`');
    $stmt->execute();
    $total_rows = $stmt->fetchColumn();
    $total_pages = ceil($total_rows / 10);

    $lowerLimit = ($_GET['page'] - 1) * 10;
    $stmt = $pdo->prepare("SELECT `id`, `title`, `author_name`, `published`, DATE_FORMAT(`created`, '%M %D %Y') AS date_created
                     FROM `blog_post`
                     WHERE `published`
                     ORDER BY `created` DESC
                     LIMIT ?, 10");
    $stmt->bindParam(1, $lowerLimit, PDO::PARAM_INT);
    $stmt->execute();
    $blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?= template_header('Contacts') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<div class="columns">
    <!-- START LEFT NAV COLUMN -->
    <div class="column is-one-fifth">
        <?= admin_nav(basename(__FILE__)) ?>
    </div>
    <!-- END LEFT NAV COLUMN -->
    <!-- START RIGHT CONTENT COLUMN-->
    <div class="column">
        <h1 class="title">Blog Posts</h1>
        <!-- Responses -->
        <?php if ($responses) : ?>
            <p class="notification is-danger is-light">
                <?php echo implode('<br>', $responses); ?>
            </p>
        <?php endif; ?>
        <div class="blog-posts">
            <a href="blog-create.php" class="button is-success">Create a New Post</a>
            <br /><br />
            <table class="table is-striped is-narrow is-hoverable is-fullwidth">
                <thead style="background-color: #D3D3D3">
                    <tr>
                        <th><abbr title="Number">#</abbr></th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Creation Date</th>
                        <th>Published?</th>
                        <th colspan="3"></th>
                    </tr>
                </thead>
                <?php foreach ($blogPosts as $post) : ?>
                    <tr>
                        <th><?= $post['id'] ?></th>
                        <td><?= $post['title'] ?></td>
                        <td><?= $post['author_name'] ?></td>
                        <td><?= $post['date_created'] ?></td>
                        <td style='text-align: center'>
                            <?php if ($post['published'] == 1) : ?>
                                <i class="fa-solid fa-check"></i>
                            <?php else : ?>
                                <i class="fa-solid fa-xmark"></i>
                            <?php endif ?>
                        </td>
                        <td>
                            <a href="blog-post.php?id=<?= $post['id'] ?>" class='button is-info'>
                                <span class='icon'>
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="blog-update.php?id=<?= $post['id'] ?>" class='button is-primary'>
                                <span class='icon'>
                                    <i class='fas fa-edit'></i>
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="blog-delete.php?id=<?= $post['id'] ?>" class='button is-danger'>
                                <span class='icon'>
                                    <i class='fas fa-trash-alt'></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            <?php if ($total_pages > 1) : ?>
                <nav class="pagination" role="navigation" aria-label="pagination">
                    <?php if ($_GET['page'] > 1) : ?>
                        <a class="pagination-previous" href="blog-admin.php?page=<?= $_GET['page'] - 1 ?>">Previous</a>
                    <?php else : ?>
                        <span class="pagination-previous" disabled>Previous</span>
                    <?php endif; ?>
                    <?php if ($total_pages > $_GET['page']) : ?>
                        <a class="pagination-next" href="blog-admin.php?page=<?= $_GET['page'] + 1 ?>">Next page</a>
                    <?php else : ?>
                        <span class="pagination-next" disabled>Next page</span>
                    <?php endif; ?>
                    <ul class="pagination-list">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $_GET['page']) {
                                echo "<li><a href='blog-admin.php?page=$i'><u>" . $i . "</u>&nbsp;</a></li>";
                            } else {
                                echo "<li><a href='blog-admin.php?page=$i'>" . $i . "&nbsp;</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <!-- END RIGHT CONTENT COLUMN-->
</div>
<!-- END PAGE CONTENT -->
<?= template_footer() ?>