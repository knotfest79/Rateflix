<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$pageTitle = "My Favorite Movies";
$userId = $_SESSION['user_id']; // Make sure to set user_id in session during login

// Fetch user's favorite movies
$stmt = $conn->prepare("SELECT movies.* FROM movies JOIN favorites ON movies.id = favorites.movie_id WHERE favorites.user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$favoriteMovies = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
include 'header.php';
?>

<h2 class="mt-4">My Favorite Movies</h2>
<div class="row">
    <?php if (count($favoriteMovies) > 0): ?>
        <?php foreach ($favoriteMovies as $movie): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <?php 
                        $imagePath = 'images/' . $movie['image'];
                        if (file_exists($imagePath)) {
                            echo '<img src="' . $imagePath . '" class="card-img-top" alt="' . $movie['title'] . '">';
                        } else {
                            echo '<img src="images/placeholder.jpg" class="card-img-top" alt="Placeholder">';
                        }
                    ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $movie['title']; ?></h5>
                        <p class="card-text"><?php echo substr($movie['description'], 0, 100); ?>...</p>
                        <a href="movie_detail.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary">View Details</a>
                        <form action="remove_favorite.php" method="post" style="display:inline;">
                            <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                            <button type="submit" class="btn btn-danger">Remove from Favorites</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have no favorite movies.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
