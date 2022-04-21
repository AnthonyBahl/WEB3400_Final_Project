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

    // Update the record after the form is submitted
    if (!empty($_POST)) {
        //PHP ternary operator
        // result = condition ? 'true result' : 'false result';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $title = isset($_POST['title']) ? $_POST['title'] : '';

        $stmt = $pdo->prepare('UPDATE `contacts` SET `name` = ?, `email` = ?, `phone`= ?, `title` = ? WHERE id = ?');
        $stmt->execute([$name, $email, $phone, $title, $_GET['id']]);
        $responses[] = 'The record was updated.';
        // Navigate back to contacts
        header('Location: contacts.php?page=' . $return_page_number);
    }
} else {
    $responses[] = 'No ID was found...';
}
?>

<?= template_header('Contact Update') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Contact Update</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<?php if ($validID) : ?>
    <form action="" method="post">
        <!-- Name -->
        <div class="field">
            <label class="label">Name</label>
            <div class="control has-icons-left">
                <input class="input" type="text" name="name" value="<?= $contact['name'] ?>" require>
                <span class="icon is-left">
                    <i class="fas fa-user-ninja"></i>
                </span>
            </div>
        </div>
        <!-- Email -->
        <div class="field">
            <label class="label">Email</label>
            <div class="control has-icons-left">
                <input class="input" type="email" name="email" value="<?= $contact['email'] ?>" require>
                <span class="icon is-left">
                    <i class="fas fa-at"></i>
                </span>
            </div>
        </div>
        <!-- Phone -->
        <div class="field">
            <label class="label">Phone</label>
            <div class="control has-icons-left">
                <input class="input" type="tel" name="phone" value="<?= $contact['phone'] ?>" require>
                <span class="icon is-left">
                    <i class="fas fa-phone"></i>
                </span>
            </div>
        </div>
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control has-icons-left">
                <input class="input" type="text" name="title" value="<?= $contact['title'] ?>">
                <span class="icon is-left">
                    <i class="fas fa-tag"></i>
                </span>
            </div>
        </div>
        <!-- Update Button -->
        <div class="field is-grouped is-grouped-left">
            <p class="control">
                <button class="button is-primary">
                    Update
                </button>
            </p>
            <!-- Cancel Button -->
            <p class="control">
                <a href="contacts.php?page=<?= $return_page_number ?>" class="button is-light">
                    Cancel
                </a>
            </p>
        </div>
    </form>
<?php endif; ?>

<!-- END PAGE CONTENT -->

<?= template_footer() ?>