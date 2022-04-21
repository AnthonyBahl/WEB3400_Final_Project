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

// If there is a query string value for 'id'
if (isset($_GET['id'])) {
    // Get page number for contacts.php for reditects
    $stmt = $pdo->prepare('WITH cte AS (SELECT `id`, ROW_NUMBER() OVER (ORDER BY id) AS rn FROM contacts) SELECT rn FROM cte WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row_number = $stmt->fetchColumn();
    $return_page_number = floor($row_number / 10) + 1;

    // get the contact from the DB
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$contact) {
        $validID = false;
        $responses[] = 'A contact did not exist with an ID of ' . $_GET['id'] . '.';
    }

    // Delete the record if the user clicked yes.
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            // Delete the record
            $stmt = $pdo->prepare('DELETE FROM `contacts` WHERE `id` = ?');
            $stmt->execute([$_GET['id']]);
        } 
        header('Location: contacts.php?page=' . $return_page_number);
    }
} else {
    $responses[] = "No id Found.";
}


?>

<?= template_header('Delete Contact') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Delete Contact</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<?php if ($validID) : ?>
<h2 class="subtitle">Are you sure you want to delete contact #
    <?= $contact['id'] ?> - <?= $contact['name'] ?>?
</h2>

<div class="buttons">
    <a href="?id=<?= $contact['id'] ?>&confirm=yes" class="button is-success">Yes</a>
    <a href="?id=<?= $contact['id'] ?>&confirm=no" class="button is-danger">No</a>
</div>
<?php endif; ?>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>