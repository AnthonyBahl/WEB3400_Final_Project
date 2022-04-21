<?php
require 'config.php';

// Start the session
session_start();

$pdo = pdo_connect_mysql();

// Page ID needs to exist, this is used to determine which reviews are for which page.
if (isset($_GET['page_id'])) {
    if (isset($_POST['name'], $_POST['rating'], $_POST['content'])) {
        // Insert a new review (user submitted form)
        $stmt = $pdo->prepare('INSERT INTO reviews (page_id, name, content, rating, submit_date) VALUES (?,?,?,?,NOW())');
        $stmt->execute([$_GET['page_id'], $_POST['name'], $_POST['content'], $_POST['rating']]);
        //exit('Your review has been submitted!');
        header('Location: blog-post.php?id=' . $_GET['page_id']);
    }
    // Get all reviews by the Page ID ordered by the submit date
    $stmt = $pdo->prepare('SELECT * FROM reviews WHERE page_id = ? ORDER BY submit_date DESC');
    $stmt->execute([$_GET['page_id']]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get the overall rating and total amount of reviews
    $stmt = $pdo->prepare('SELECT AVG(rating) AS overall_rating, COUNT(*) AS total_reviews FROM reviews WHERE page_id = ?');
    $stmt->execute([$_GET['page_id']]);
    $reviews_info = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    exit('Please provide the page ID.');
}

// Below function will convert datetime to time elapsed string.
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    $string = array('y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second');
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>

<!-- START REVIEW CONTENT -->
<div class="box">
    <p class="subtitle">Overall rating:</p>
    <span><?= number_format($reviews_info['overall_rating'], 1) ?></span>
    <span><?= str_repeat('&#9733;', round($reviews_info['overall_rating'])) ?></span>
    <span><?= $reviews_info['total_reviews'] ?> reviews</span>
</div>

<div class="box">
    <p class="subtitle">Write a review:</p>
    <form class="form" action="reviews.php?page_id=<?= $_GET['page_id'] ?>" method="post">
        <div class="field">
            <input class="input" name="name" type="text" placeholder="Your Name" required>
        </div>
        <div class="field">
            <input class="input" name="rating" type="number" min="1" max="5" placeholder="Rating (1-5)" required>
        </div>
        <div class="field">
            <textarea class="textarea" name="content" placeholder="Write your review here..." required></textarea>
        </div>
        <button class="button" type="submit">Submit Review</button>
    </form>
</div>
<hr>
<p class="subtitle">Read Reviews:</p>
<?php foreach ($reviews as $review) : ?>
    <div class="box">
        <h3><?= htmlspecialchars($review['name'], ENT_QUOTES) ?></h3>
        <div>
            <span><?= str_repeat('&#9733;', $review['rating']) ?></span>
            <span><?= time_elapsed_string($review['submit_date']) ?></span>
        </div>
        <p><?= htmlspecialchars($review['content'], ENT_QUOTES) ?></p>
    </div>
<?php endforeach ?>
<!-- END REVIEW CONTENT -->