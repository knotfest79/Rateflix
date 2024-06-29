<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$movieId = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch reviews and average rating
$stmt = $conn->prepare("SELECT reviews.*, AVG(reviews.rating) as avg_rating, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.movie_id = ? GROUP BY reviews.id");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$avgRating = isset($reviews[0]) ? $reviews[0]['avg_rating'] : 0; // Check if there are reviews to avoid warnings
$stmt->close();

$pageTitle = $movie['title'];
include 'header.php';
?>

<h2 class="mt-4"><?php echo $movie['title']; ?></h2>
<div class="row">
    <div class="col-md-6">
        <?php 
            $imagePath = 'images/' . $movie['image'];
            if (file_exists($imagePath)) {
                echo '<img src="' . $imagePath . '" class="img-fluid" alt="' . $movie['title'] . '">';
            } else {
                echo '<img src="images/placeholder.jpg" class="img-fluid" alt="Placeholder">';
            }
        ?>
    </div>
    <div class="col-md-6">
        <p><?php echo $movie['description']; ?></p>
        <h4>Average Rating: <?php echo round($avgRating, 1); ?> / 5</h4>
        <form action="add_favorite.php" method="post" onsubmit="return confirmFavorite();">
            <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">
            <button type="submit" class="btn btn-success">Add to Favorites</button>
        </form>
    </div>
</div>

<h3 class="mt-4">Reviews</h3>
<?php if ($reviews): ?>
    <?php foreach ($reviews as $review): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo $review['username']; ?></h5>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?php echo $i; ?>_<?php echo $review['id']; ?>" name="rating<?php echo $review['id']; ?>" value="<?php echo $i; ?>" <?php if ($review['rating'] == $i) echo 'checked'; ?> disabled>
                        <label for="star<?php echo $i; ?>_<?php echo $review['id']; ?>">&#9733;</label>
                    <?php endfor; ?>
                </div>
                <p class="card-text"><?php echo $review['review']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No reviews yet.</p>
<?php endif; ?>

<h3 class="mt-4">Add a Review</h3>
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>
<form action="add_review.php" method="post">
    <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">
    <div class="form-group">
        <label for="rating">Your Rating</label>
        <div class="star-rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                <label for="star<?php echo $i; ?>">&#9733;</label>
            <?php endfor; ?>
        </div>
    </div>
    <div class="form-group">
        <label for="review">Your Review</label>
        <textarea name="review" class="form-control" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Review</button>
</form>

<?php include 'footer.php'; ?>

<script>
    function confirmFavorite() {
        return confirm("Are you sure you want to add this movie to your favorites?");
    }
</script>
