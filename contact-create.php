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

if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['title'])) {
    //check if all fields are completed
    if (empty($_POST['name']) || empty($_POST['email']) ||  empty($_POST['phone']) || empty($_POST['title'])) {
        $responses[] = "Please complete all fields";
    } else {
        // get the contact from the database
        $stmt = $pdo->prepare('SELECT * FROM contacts WHERE name = ?');
        $stmt->execute([$_POST['name']]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($contact) {
            $responses[] = 'A contact already exists with that name.';
        }
        if (!empty($_POST)) {
            //PHP ternary operator
            // result = condition ? 'trueresult' : 'falseresult';
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $title = isset($_POST['title']) ? $_POST['title'] : '';

            $stmt = $pdo->prepare('INSERT INTO contacts(name, email, phone, title) VALUES (?,?,?,?)');
            $stmt->execute([$name, $email, $phone, $title]);
            //$responses[] = 'The record was updated.';
            header('Location: contacts.php');
        }
    }
}

?>

<?= template_header('Create new contact') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Create New Contact</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<form action="" method="post">
    <!-- Name -->
    <div class="field">
        <label class="label">Name</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="name" placeholder="John Doe" require>
            <span class="icon is-left">
                <i class="fas fa-user-ninja"></i>
            </span>
        </div>
    </div>
    <!-- Email -->
    <div class="field">
        <label class="label">Email</label>
        <div class="control has-icons-left">
            <input class="input" type="email" name="email" placeholder="jdoe@example.com" require>
            <span class="icon is-left">
                <i class="fas fa-at"></i>
            </span>
        </div>
    </div>
    <!-- Phone -->
    <div class="field">
        <label class="label">Phone</label>
        <div class="control has-icons-left">
            <input class="input" type="tel" name="phone" placeholder="000-867-5309" require>
            <span class="icon is-left">
                <i class="fas fa-phone"></i>
            </span>
        </div>
    </div>
    <!-- Title -->
    <div class="field">
        <label class="label">Title</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="title" placeholder="Ninja">
            <span class="icon is-left">
                <i class="fas fa-tag"></i>
            </span>
        </div>
    </div>
    <!-- Update Button -->
    <div class="field is-grouped is-grouped-left">
        <p class="control">
            <button class="button is-primary">
                Create
            </button>
        </p>
        <!-- Cancel Button -->
        <p class="control">
            <a href="contacts.php" class="button is-light">
                Cancel
            </a>
        </p>
    </div>
</form>

<?= template_footer() ?>