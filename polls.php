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
    header('Location: polls.php?page=1');
} else {
    // use PDO to connect to our database
    $pdo = pdo_connect_mysql();

    // Get Total Page Count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `polls`');
    $stmt->execute();
    $total_rows = $stmt->fetchColumn();
    $total_pages = ceil($total_rows / 10);

    // get 10 most recent blog posts
    $lowerLimit = ($_GET['page'] - 1) * 10;
    $stmt = $pdo->prepare("SELECT p.`id`, p.`title`, p.`desc`, GROUP_CONCAT(pa.`title` ORDER BY pa.`id`) AS answers
                           FROM polls p
                                LEFT JOIN poll_answers pa ON pa.`poll_id` = p.`id`
                           GROUP BY p.`id`
                           ORDER BY p.`id` DESC
                           LIMIT ?, 10");
    $stmt->bindParam(1, $lowerLimit, PDO::PARAM_INT);
    $stmt->execute();
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?= template_header('Polls') ?>
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
        <h1 class="title">Polls</h1>
        <!-- Responses -->
        <?php if ($responses) : ?>
            <p class="notification is-danger is-light">
                <?php echo implode('<br>', $responses); ?>
            </p>
        <?php endif; ?>
        <a href="poll-create.php" class="button is-success">Create a New Poll</a>
        <table class="table">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Title</td>
                    <td>Answers</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($polls as $poll) : ?>
                    <tr>
                        <td><?= $poll['id'] ?></td>
                        <td><?= $poll['title'] ?></td>
                        <td><?= $poll['answers'] ?></td>
                        <td>
                            <a href="poll-vote.php?id=<?= $poll['id'] ?>" class="button is-success"><i class="fa-solid fa-check-to-slot"></i></a>
                            <a href="poll-result.php?id=<?= $poll['id'] ?>" class="button is-info"><i class="fa-solid fa-square-poll-horizontal"></i></a>
                            <a href="poll-delete.php?id=<?= $poll['id'] ?>" class="button is-danger"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php if ($total_pages > 1) : ?>
                <nav class="pagination" role="navigation" aria-label="pagination">
                    <?php if ($_GET['page'] > 1) : ?>
                        <a class="pagination-previous" href="polls.php?page=<?= $_GET['page'] - 1 ?>">Previous</a>
                    <?php else : ?>
                        <span class="pagination-previous" disabled>Previous</span>
                    <?php endif; ?>
                    <?php if ($total_pages > $_GET['page']) : ?>
                        <a class="pagination-next" href="polls.php?page=<?= $_GET['page'] + 1 ?>">Next page</a>
                    <?php else : ?>
                        <span class="pagination-next" disabled>Next page</span>
                    <?php endif; ?>
                    <ul class="pagination-list">
                        <?php
                        for ($i = 1; $i <= $total_pages; $i++) {
                            if ($i == $_GET['page']) {
                                echo "<li><a href='polls.php?page=$i'><u>" . $i . "</u>&nbsp;</a></li>";
                            } else {
                                echo "<li><a href='polls.php?page=$i'>" . $i . "&nbsp;</a></li>";
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