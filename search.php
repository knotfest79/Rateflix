<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$pageTitle = "Search Results";

$query = $_GET['query'];
$genre = $_GET['genre'] == 'all' ? '' : $_GET['genre'];
$stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? AND genre LIKE ?");
$searchTerm = "%$query%";
$genreTerm = "%$genre%";
$stmt->bind_param('ss', $searchTerm, $genreTerm);
$stmt->execute();
$result = $stmt->get_result();
$movies = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Rateflix movie Review</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Saira+Condensed:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="css/globals.css" />
    <link rel="stylesheet" href="css/styleguide.css" />
    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><img src="img/logo.png" alt="Logo" /></a>
            <p class="text-white text-xl font-bold" style="text-transform: capitalize; font-size: larger;">
                Welcome Back <br>
                <?php echo $_SESSION['username']; ?></p>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#movies">Movies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="favorites.php">Favourites</a>
                    </li>
                    <li class="nav-item">
                        <div style="border-radius: 50px; margin-left: 10px;">
                            <a href="profile.php" class=""><img style="border-radius: 50px;" src=<?php echo "https://i.pravatar.cc/50?u=m" . $_SESSION['username']; ?>> </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div style="margin-top: 100px;"></div>

<div style="padding: 0px 100px;">
<h2 style="font-size: x-large; text-align: center; width: 100%; padding: 20px 0px;">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
<div class="row" id="movies">
        <?php foreach ($movies as $movie) : ?>
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
                    <h3 class="card-title"><?php echo $movie['title']; ?></h3>
                    <p class="card-text"><?php echo substr($movie['description'], 0, 100); ?>...</p>
                    <a href="movie_detail.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>
    

  
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php include 'footer.php'; ?>