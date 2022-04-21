<?php
// Database: https://icarus.cs.weber.edu/phpmyadmin/index.php
?>

<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'W01113559';
$DATABASE_PASS = 'Anthonycs!';
$DATABASE_NAME = 'W01113559';

$adminClass = 'test';
$profileClass = "";
$pollsClass = "";
$contactsClass = "";

$responses = [];

// Try to connect to our db
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

if (mysqli_connect_errno()) {
  // If there is an error with the connection, stop the script and display the error
  exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

function pdo_connect_mysql()
{
  // create the db connection variables
  $DATABASE_HOST = 'localhost';
  $DATABASE_USER = 'W01113559';
  $DATABASE_PASS = 'Anthonycs!';
  $DATABASE_NAME = 'W01113559';

  // db connection
  try {
    return new PDO(
      'mysql:host=' . $DATABASE_HOST .
        ';dbname=' . $DATABASE_NAME .
        ';charset=utf8',
      $DATABASE_USER,
      $DATABASE_PASS
    );
  } catch (PDOException $exception) {
    die('PDO Failed to connect to the database.');
  }
}

function getMyUrl()
{
  $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $regex_pattern = '/(.*)\/.*\.php/'; // remove file name
  return 'https://' . preg_replace($regex_pattern, '$1', $url);
}

function checkCurrentPage()
{
  $URL = $_SERVER['PHP_SELF'];
  return substr($URL, strrpos($URL, "/") + 1);
}

function template_header($title = "Most Amazing Blog Ever")
{
  echo <<<EOT
 <!DOCTYPE html>
  <html>

    <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>$title</title>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
     <script defer src="https://use.fontawesome.com/releases/v6.1.1/js/all.js"></script>
     <script defer src="js/bulma.js"></script>
    </head>

  <body>
EOT;
}

function admin_nav($fileName)
{
  $adminActive = '';
  $profileActive = '';
  $pollsActive = '';
  $contactsActive = '';
  $blogAdminActive = '';
  $reviewsAdminActive = '';

  switch ($fileName) {
    case 'admin.php':
      $adminActive = 'is-active';
      break;
    case 'profile.php':
      $profileActive = 'is-active';
      break;
    case 'polls.php':
      $pollsActive = 'is-active';
      break;
    case 'contacts.php':
      $contactsActive = 'is-active';
      break;
    case 'blog-admin.php':
      $blogAdminActive = 'is-active';
      break;
    case 'reviews-admin.php':
      $reviewsAdminActive = 'is-active';
      break;
  }

  echo <<<EOT
  <!-- START ADMIN NAV -->
  <aside class="menu">
      <p class="menu-label"> Admin menu </p>
      <ul class="menu-list">
          <li><a href="admin.php" class="$adminActive"> Admin </a></li>
          <li><a href="profile.php" class="$profileActive"> Profile </a></li>
          <li><a href="polls.php" class="$pollsActive"> Polls </a></li>
          <li><a href="contacts.php" class="$contactsActive"> Contacts </a></li>
          <li><a href="blog-admin.php" class="$blogAdminActive"> Blog Admin </a></li>
          <li><a href="reviews-admin.php" class="$reviewsAdminActive"> Reviews Admin </a></li>
      </ul>
  </aside>
  EOT;
}

function template_nav($siteTitle = "Most Amazing Blog Ever")
{
  $logInOutlink = '';
  $logInOutDisplay = '';
  if (isset($_SESSION['loggedin'])) {
    $logInOutlink = 'out';
    $logInOutDisplay = 'inline';
  } else {
    $logInOutlink = 'in';
    $logInOutDisplay = 'none';
  }
  echo <<<EOT
    <!-- START NAV -->
      <nav class="navbar is-light">
        <div class="container">
          <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
              <span class="icon is-large">
                <i class="fas fa-home"></i>
              </span>
              <span>$siteTitle</span>
            </a>
            <div class="navbar-burger burger" data-target="navMenu">
              <span></span>
              <span></span>
              <span></span>
            </div>
          </div>
          <div id="navMenu" class="navbar-menu">
            <div class="navbar-start">
              <!-- navbar link go here -->
            </div>
            <div class="navbar-end">
              <div class="navbar-item">
                <div class="buttons">
                  <a href="admin.php" class="button" style="display:$logInOutDisplay">
                    <span class="icon"><i class="fa-solid fa-user-lock"></i></span>
                    <span>Admin</span>
                  </a>
                  <a href="contact.php" class="button">
                    <span class="icon"><i class="fas fa-address-book"></i></span>
                    <span>Contact Us</span>
                  </a>
                  <a href="log$logInOutlink.php" class="button">
                    <span class="icon"><i class="fas fa-sign-$logInOutlink-alt"></i></span>
                    <span>Log$logInOutlink</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>
      <!-- END NAV -->

      <!-- START MAIN -->
      <section class="section">
          <div class="container">
  EOT;
}

function template_footer()
{
  echo <<<EOT
        </div>
    </section>
    <!-- END MAIN-->

    <!-- START FOOTER -->
    <footer class="footer">
        <div class="container">
            <p>Most Amazing Blog Ever &#124; Anthony Bahl &#124; &copy; Copyright 2022 All Rights Reserved</p>
        </div>
    </footer>
    <!-- END FOOTER -->
    </body>
  </html>
EOT;
}
