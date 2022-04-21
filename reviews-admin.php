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
    header('Location: reviews-admin.php?page=1');
} else {
    // use PDO to connect to our database
    $pdo = pdo_connect_mysql();
    
    // Get total Page Count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `reviews`');
    $stmt->execute();
    $total_rows = $stmt->fetchColumn();
    $total_pages = ceil($total_rows / 10);

    $lowerLimit = ($_GET['page'] - 1) * 10;
    $stmt = $pdo->prepare("SELECT `id` AS review_id, `page_id` AS post_id, `name` AS reviewer, LEFT(`content`, 100) AS review_content, `rating`, DATE_FORMAT(`submit_date`, '%M %D %Y') AS review_date
                     FROM `reviews`
                     ORDER BY `submit_date` DESC
                     LIMIT ?, 10");
    $stmt->bindParam(1, $lowerLimit, PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="title">Reviews</h1>
        <!-- Responses -->
        <?php if ($responses) : ?>
            <p class="notification is-danger is-light">
                <?php echo implode('<br>', $responses); ?>
            </p>
        <?php endif; ?>
        <div class="reviews">
            <table class="table is-striped is-narrow is-hoverable is-fullwidth">
                <thead style="background-color: #D3D3D3">
                    <tr>
                        <th><abbr title="Blog that the review is associated to.">Blog</abbr></th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Author</th>
                        <th>Review Date</th>
                        <th colspan="3"></th>
                    </tr>
                </thead>
                <?php foreach ($reviews as $review) : ?>
                    <tr>
                        <td><?= $review['post_id'] ?></td>
                        <td><?= str_repeat('&#9733;', $review['rating']) ?></td>
                        <td><?= $review['review_content'] ?></td>
                        <td><?= $review['reviewer'] ?></td>
                        <td><?= $review['review_date'] ?></td>
                        <td>
                            <a href="blog-post.php?id=<?= $review['post_id'] ?>" class='button is-info'>
                                <span class='icon'>
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="reviews-update.php?id=<?= $review['review_id'] ?>" class='button is-primary'>
                                <span class='icon'>
                                    <i class='fas fa-edit'></i>
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="reviews-delete.php?id=<?= $review['review_id'] ?>" class='button is-danger'>
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
                        <a class="pagination-previous" href="reviews-admin.php?page=<?= $_GET['page'] - 1 ?>">Previous</a>
                    <?php else : ?>
                        <span class="pagination-previous" disabled>Previous</span>
                    <?php endif; ?>
                    <?php if ($total_pages > $_GET['page']) : ?>
                        <a class="pagination-next" href="reviews-admin.php?page=<?= $_GET['page'] + 1 ?>">Next page</a>
                    <?php else : ?>
                        <span class="pagination-next" disabled>Next page</span>
                    <?php endif; ?>
                    <ul class="pagination-list">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $_GET['page']) {
                                echo "<li><a href='reviews-admin.php?page=$i'><u>" . $i . "</u>&nbsp;</a></li>";
                            } else {
                                echo "<li><a href='reviews-admin.php?page=$i'>" . $i . "&nbsp;</a></li>";
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