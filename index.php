<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rateflix</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="navbar-brand">
                <img src="images/mausam.png" alt="Rateflix" style="height: 40px;">
            </a>
            <nav>
                <a href="index.php" class="text-white mx-2">Home</a>
                <a href="movies.php" class="text-white mx-2">Movies</a>
                <a href="favorites.php" class="text-white mx-2">Favorites</a>
                <a href="login.php" class="text-white mx-2">Login</a>
                <a href="register.php" class="text-white btn btn-primary ml-2">Sign Up</a>
            </nav>
        </div>
    </header>

    <!-- Main Slider -->
    <div class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/mausam.png" class="d-block w-100" alt="Deadpool 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>July 26th - Deadpool 3</h5>
                    <p>Fantasy, Action</p>
                    <a href="#" class="btn btn-danger">Watch Trailer</a>
                    <a href="#" class="btn btn-light">Add to Favourites</a>
                    <a href="#" class="btn btn-secondary">Share</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Movies Section -->
    <section class="container my-5">
        <h2 class="text-center mb-4">Movies</h2>
        <div class="d-flex justify-content-between mb-4">
            <div>#Popular</div>
            <div>#Coming Soon</div>
            <div>#Top Rated</div>
            <div>#Most Reviewed</div>
            <div><a href="movies.php">View All ></a></div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <img src="mausam.png" class="img-fluid" alt="Deadpool">
            </div>
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="Avatar 3">
            </div>
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="Kingdom of the Planet of the Apes">
            </div>
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="Fall Guy">
            </div>
        </div>
    </section>

    <!-- TV Shows Section -->
    <section class="container my-5">
        <h2 class="text-center mb-4">TV Shows</h2>
        <div class="d-flex justify-content-between mb-4">
            <div>#Popular</div>
            <div>#Coming Soon</div>
            <div>#Top Rated</div>
            <div>#Most Reviewed</div>
            <div><a href="tvshows.php">View All ></a></div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="House of the Dragon">
            </div>
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="The Boys">
            </div>
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="Bridgerton">
            </div>
            <div class="col-md-3">
                <img src="images/mausam.png" class="img-fluid" alt="Stranger Things">
            </div>
        </div>
    </section>

    <!-- In Theatre Section -->
    <section class="container my-5">
        <h2 class="text-center mb-4">In Theatre</h2>
        <div class="row">
            <div class="col-md-8">
                <img src="images/mausam.png" class="img-fluid" alt="Smile 2">
                <div class="mt-2">
                    <a href="#" class="btn btn-primary">Watch the Trailer</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">Stranger Things 4 Trailer</a>
                    <a href="#" class="list-group-item list-group-item-action">Stranger Things 4 Trailer</a>
                    <a href="#" class="list-group-item list-group-item-action">Stranger Things 4 Trailer</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white p-3">
        <div class="container d-flex justify-content-between">
            <div>
                <a href="index.php" class="text-white mx-2">Home</a>
                <a href="movies.php" class="text-white mx-2">Movies</a>
                <a href="favorites.php" class="text-white mx-2">Favorites</a>
                <a href="login.php" class="text-white mx-2">Login</a>
            </div>
            <div class="text-white">
                <p>&copy; 2024 All Rights Reserved. Rateflix</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
