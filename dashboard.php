<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$pageTitle = "Dashboard";

// Fetch random movies
$randomMovies = $conn->query("SELECT * FROM movies WHERE type='0'")->fetch_all(MYSQLI_ASSOC);
$allGenre = $conn->query("SELECT DISTINCT genre FROM movies WHERE type='0'")->fetch_all(MYSQLI_ASSOC);

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
                            <?php echo $_SESSION['username']; ?></p> <!-- // This line of code outputs the value stored in the 'username' key of the $_SESSION superglobal array.
// The $_SESSION superglobal array is used to store session variables, which are accessible throughout a user's session on the website.
// In this case, the 'username' session variable likely stores the username of the currently logged-in user. -->

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
                        <a class="nav-link" href="series.php">Series</a>
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

    <div id="carouselExampleControlsNoTouching" class="carousel slide" data-bs-touch="false" data-bs-interval="false">
        <ol class="carousel-indicators">
            <li data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide-to="0" class="active"></li>
            <li data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide-to="1"></li>
            <li data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/banner_1.png" class="d-block w-100 d-sm-block" alt="..." />
            </div>
            <div class="carousel-item">
                <img src="img/banner_2.png" class="d-block w-100 d-sm-block" alt="..." />
            </div>
            <div class="carousel-item">
                <img src="img/banner_2.png" class="d-block w-100 d-sm-block" alt="..." />
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <!-- Showcase -->
    <section class="banner">
        <div class="banner-content">
            <button class="btn btn-primary my-2">Fantasy</button>
            <button class="btn btn-danger my-2">Action</button>
            <h1>July 26th</h1>
            <h1>
                <span class="text-warning">Deadpool </span>
                <span class="text-danger">3</span>
            </h1>
        </div>
    </section>
    <div class="search-form">
        <form class="form" action="search.php" method="get">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="query" >
            <select name="genre">
                <option value="all">All</option>
                <?php foreach ($allGenre as $genre) : ?>
                    <option value="<?php echo $genre['genre']; ?>"><?php echo $genre['genre']; ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>

    <div class="row" id="movies">
        <?php foreach ($randomMovies as $movie) : ?>
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

    <footer>
        <div class="wrapper">
            <div class="links-container">
                <div class="links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li>
                            <a href="login.php">Login</a>
                        </li>

                        <li>
                            <a href="#">Movies</a>
                        </li>

                        <li>
                            <a href="dashboard.php">Home</a>
                        </li>

                        <li>
                            <a href="favorites.php">Favourites</a>
                        </li>
                    </ul>

                    <li>
                        <a href="register.php" class="btn light">Sign Up</a>
                    </li>


                </div>

                <div class="links">
                    <ul>
                        <li>
                            <a href="#">Privacy Policy</a>
                        </li>

                        <li>
                            <a href="#">Terms & Conditions</a>
                        </li>
                    </ul>

                </div>
                <p class="copyright"> © Copyright 2024. All Rights Reserved. Rateflix Pragesh Devbhandari | Mausam Dahal </p>
            </div>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>