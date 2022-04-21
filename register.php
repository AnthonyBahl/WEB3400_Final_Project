<?php
require 'config.php';

// Start the session
session_start();

if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {

    // We need to check to see if the username already exists.
    if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
        // Bind parameters (s = string, i = int, b = blob, ect)
        // hash the password using the PHP password_hash function.
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Username already exists
            $responses[] = 'Username already exists, please choose another.';
        } else {
            // Create the new user account
            if ($stmt = $con->prepare('INSERT INTO `accounts` (`username`, `password`, `email`, `activation_code`) VALUES (?,?,?,?)')) {
                // Do not ever expose passwords in your database
                // we will use the php password_hash function
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $uniqid = uniqid();
                $stmt->bind_param('ssss', $_POST['username'], $password, $_POST['email'], $uniqid);
                $stmt->execute();

                // This is where we would send an email if we could
                // insteaed we will display the activation link on the page
                $activation_link = getMyUrl() . '/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
                $responses[] = 'Please click the following link to activate your account: <a href="' . $activation_link . '">' . $activation_link . '</a>';
            } else {
                // Something went wrong with our insert statement
                $responses[] = 'Could not prepare the insert statement.';
            }
        }
        $stmt->close();
    } else {
        // Something went wrong with our insert statement
        $responses[] = 'Could not prepare the select statement.';
    }
    $con->close();
}

?>

<?= template_header('Register') ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Register</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<form action="" method="post">

    <!-- Username -->
    <div class="field">
        <label class="label">Username</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="username" placeholder="jsmith" require>
            <span class="icon is-left">
                <i class="fas fa-user-ninja"></i>
            </span>
        </div>
    </div>

    <!-- Password -->
    <div class="field">
        <label class="label">Password</label>
        <div class="control has-icons-left">
            <input class="input" type="password" name="password" placeholder="Password" require>
            <span class="icon is-left">
                <i class="fas fa-lock"></i>
            </span>
        </div>
    </div>

    <!-- Email -->
    <div class="field">
        <label class="label">Email</label>
        <div class="control has-icons-left">
            <input class="input" type="email" name="email" placeholder="jsmith@example.com" require>
            <span class="icon is-left">
                <i class="fas fa-at"></i>
            </span>
        </div>
    </div>

    <!-- Button -->
    <div class="field is-grouped">
        <p class="control">
            <button class="button is-success">
                Register
            </button>
        </p>
        <p class="buttons">
            <a class="button" href="login.php">
                <span>Cancel</span>
            </a>
        </p>
    </div>
</form>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>