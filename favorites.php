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
?>

<head>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/globals.css" />
    <link rel="stylesheet" href="css/styleguide.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <script defer src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><img src="img/logo.png" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class=" navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php#movies">Movies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="favorites.php">Favourites</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <br>
    <br>
    <br>
    <br>
    <h2 class="mt-4 text-center text-3xl font-bold my-5">Favourite Shows</h2>
    <div class="row">
        <?php if (count($favoriteMovies) > 0) : ?>
            <?php foreach ($favoriteMovies as $movie) : ?>
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
                            <form action="remove_favorite.php" method="post" style="display:flex; flex-direction: column; align-items: start;">
                                <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                <button type="submit" class="btn btn-danger">Remove from Favorites</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>You have no favorite movies.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>