<?php
require 'config.php';

// Start a user session
session_start();

// Now we check if the data from the login form was submitted,
// isset() will check if the data exists.
if (isset($_POST['username'], $_POST['password'])) {
    // Prepare our SQL, preparing the SQL  statement will prevent SQL injection.
    if ($stmt = $con->prepare('SELECT id, password, activation_code, email FROM accounts WHERE username = ?')) {
        // Bind parameters (s = string, i = int, b = blob, ect) to the string above,
        // in our case the username is a string so we use "s".
        $stmt->bind_param('s', $_POST['username']);
        // Execute SQL statement
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();
        // Check if any records were returned from the accounts table that match the username
        if ($stmt->num_rows() > 0) {
            // Bind the results returned from the database to our variables
            $stmt->bind_result($id, $password, $activation_code, $email);
            // Fetch the results into the bound variables
            $stmt->fetch();
            // Make sure account has been activated
            if ($activation_code != "activated") {
                if ($stmt = $con->prepare('UPDATE `accounts` SET `activation_code` = ? WHERE `username` = ?')) {
                    // Creating a new Activation Code
                    $uniqid = uniqid();
                    // Bind the Parameters
                    $stmt->bind_param('ss', $uniqid, $_POST['username']);
                    $stmt->execute();
                    // Create new activation link
                    $activation_link = getMyUrl() . '/activate.php?email=' . $email . '&code=' . $uniqid;
                    // Provide activation link again.
                    $responses[] = "Oh no! It looks like you haven't activated your account yet. Click <a href='" . $activation_link . "'>here</a> to activate your account.";
                } else {
                    $responses[] = "There was an error generating a new activation link.";
                }
            } else {
                // Account exists, now we verify the password.
                // password_verify(string $password, string $hash)
                if (password_verify($_POST['password'], $password)) {
                    // Verify success! User has logged-in!
                    // Create session variables, so we know the user is logged in,
                    // session variables act like cookies but keep the data on the server.
                    session_regenerate_id();
                    $_SESSION['loggedin'] = TRUE;
                    $_SESSION['name'] = $_POST['username'];
                    $_SESSION['id'] = $id;
                    //Create a welcome message
                    $responses[] = "Welcome " . $_SESSION['name'] . "!";
                    // Redirect to the profile page
                    header('Location: admin.php');
                } else {
                    // Incorrect password
                    $responses[] = 'Incorrect password!';
                }
            }
        } else {
            // Incorrect username
            $responses[] = 'Incorrect username!';
        }
    }
}

?>

<?= template_header() ?>
<?= template_nav() ?>

<!-- START PAGE CONTENT -->
<h1 class="title">Login</h1>
<!-- Responses -->
<?php if ($responses) : ?>
    <p class="notification is-danger is-light">
        <?php echo implode('<br>', $responses); ?>
    </p>
<?php endif; ?>

<form action="" method="post">

    <div class="field">
        <label class="label">Username</label>
        <div class="control has-icons-left">
            <input class="input" type="text" name="username" placeholder="jsmith" require>
            <span class="icon is-left">
                <i class="fas fa-user-ninja"></i>
            </span>
        </div>
    </div>

    <div class="field">
        <label class="label">Password</label>
        <div class="control has-icons-left">
            <input class="input" type="password" name="password" placeholder="Password" require>
            <span class="icon is-left">
                <i class="fas fa-lock"></i>
            </span>
        </div>
    </div>

    <!-- Button -->
    <div class="field is-grouped">
        <p class="control">
            <button class="button is-success">
                Login
            </button>
        </p>
        <p class="buttons">
            <a class="button" href="register.php">
                <span class="icon">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span>Sign Up</span>
            </a>
        </p>
    </div>
</form>
<!-- END PAGE CONTENT -->

<?= template_footer() ?>