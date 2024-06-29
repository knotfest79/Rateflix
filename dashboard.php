<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$pageTitle = "Dashboard";

// Fetch random movies
$randomMovies = $conn->query("SELECT * FROM movies ORDER BY RAND() LIMIT 5")->fetch_all(MYSQLI_ASSOC);
include 'header.php';
?>

<h2 class="mt-4">Welcome, <?php echo $_SESSION['username']; ?>!</h2>
<h3 class="mt-4">Random Movies</h3>
<div class="row">
    <?php foreach ($randomMovies as $movie): ?>
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
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'footer.php'; ?>
