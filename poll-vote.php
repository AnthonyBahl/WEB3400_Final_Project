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
    } else {
        $responses[] = "There was an error grabing poll with ID of " . $_GET['id'];
    }
} else {
    $responses[] = "You must provide a poll ID. <a href='polls.php?page=" .$return_page_number . "'>Click Here</a> to return to the polls list.";
}

// Vote on Poll
if (!empty($_POST) && isset($_GET['id'])) {

    $voteID = isset($_POST['poll_answer']) ? $_POST['poll_answer'] : '';

    // Update the votes in the database
    $stmt = $pdo->prepare('UPDATE `poll_answers` SET `votes` = `votes` + 1 WHERE id = ?');
    $stmt->execute([$voteID]);

    $responses[] = "Your vote has been cast.";

    $redirect = 'Location: poll-result.php?id=' . $_GET['id'];

    header($redirect);

    // Redirect 

}

?>

<?= template_header('Vote') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Vote for:</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>
<h2 class="subtitle"><?= $poll['title'] ?></h2>
<form method="post">
    <div class="field">
        <div class="control">
            <?php foreach ($poll_answers as $poll_answer) : ?>
                <label class="radio">
                    <input type="radio" name="poll_answer" value="<?= $poll_answer['id'] ?>" required> <?= $poll_answer['title'] ?>
                </label>
                <br />
            <?php endforeach; ?>
        </div>
    </div>
    <div class="field">
        <div class="control">
            <button class="button is-link">Vote</button>
        </div>
    </div>
    <a href="polls.php?page=<?= $return_page_number ?>">Return to polls page</a>
</form>
</div>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>