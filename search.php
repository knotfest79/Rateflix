<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$pageTitle = "Search Results";
include 'header.php';

$query = $_GET['query'];
$stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ?");
$searchTerm = "%$query%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$movies = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<h2 class="mt-4">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
<div class="row">
    <?php if (count($movies) > 0): ?>
        <?php foreach ($movies as $movie): ?>
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
    <?php else: ?>
        <p>No movies found matching your search query.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
