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

// Check if there was an ID passed
if (isset($_GET['id'])) {
    // Get page number for contacts.php for reditects
    $stmt = $pdo->prepare('WITH cte AS (SELECT `id`, ROW_NUMBER() OVER (ORDER BY `id` DESC) AS rn FROM `polls`) SELECT rn FROM cte WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row_number = $stmt->fetchColumn();
    $return_page_number = floor($row_number / 10) + 1;

    // Get the poll answers for the poll that matches the id
    $stmt = $pdo->prepare('SELECT * FROM `polls` WHERE `id` = ?');
    $stmt->execute([$_GET['id']]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the poll exists
    if ($poll) {

        // Get the responses
        $stmt = $pdo->prepare('SELECT * FROM `poll_answers` WHERE `poll_id` = ?');
        $stmt->execute([$_GET['id']]);
        $poll_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total number of votes
        $total_votes = 0;
        foreach ($poll_answers as $poll_answer) {
            $total_votes += $poll_answer['votes'];
        }
    } else {
        $responses[] = "There was an error grabing poll with ID of " . $_GET['id'];
    }
} else {
    $responses[] = "You must provide a poll ID. <a href='polls.php?page=" . $return_page_number . "'>Click Here</a> to return to the polls list.";
}

?>

<?= template_header('Poll Results') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Poll Results</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>
<h2 class="subtitle"><?= $poll['title'] ?> (Total votes: <?= $total_votes ?>)</h2>

<div class="container">
    <?php foreach ($poll_answers as $poll_answer) : ?>
        <p><?= $poll_answer['title'] ?> (<?= $poll_answer['votes'] ?>)</p>
        <progress class="progress is-info is-large" value="<?= $poll_answer['votes'] ?>" max="<?= $total_votes ?>"></progress>
    <?php endforeach; ?>
    <a href="polls.php?page=<?= $return_page_number ?>">Return to polls page</a>
</div>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>