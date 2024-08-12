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
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?" );
$stmt->bind_param("i", $movieId);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch reviews and average rating
$stmt = $conn->prepare("SELECT reviews.*, AVG(reviews.rating) as avg_rating, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.movie_id = ? GROUP BY reviews.id DESC");
$stmt->bind_param("i", $movieId);
$randomMovies = $conn->query("SELECT * FROM movies")->fetch_all(MYSQLI_ASSOC);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$avgRating = isset($reviews[0]) ? $reviews[0]['avg_rating'] : 0; // Check if there are reviews to avoid warnings
$stmt->close();

// Fetch movie details
$movieId = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch related movies based on the same genre
$genre = $movie['genre'];
$stmt = $conn->prepare("SELECT * FROM movies WHERE genre = ? AND id != ? LIMIT 5");
$stmt->bind_param("si", $genre, $movieId);
$stmt->execute();
$relatedMovies = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

$pageTitle = $movie['title'];

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
    <div class="mt-20">
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="px-10">
                <p class="alert alert-success px-10">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']); ?>
                </p>

            </div>
        <?php endif; ?>
        <div class="flex flex-col items-center justify-center w-full  mb-10">
            <div class="flex gap-20 ">
                <div class="flex flex-col gap-2 items-start">
                    <?php
                    $imagePath = 'images/' . $movie['image'];
                    if (file_exists($imagePath)) {
                        echo '<img src="' . $imagePath . '" class="block w-[400px] h-[550px]" alt="' . $movie['title'] . '">';
                    } else {
                        echo '<img src="images/placeholder.jpg" class="w-[400px]" alt="Placeholder">';
                    }
                    ?>
                    <button onclick="openDialog()" class="bg-red-500 px-3 py-2 text-lg rounded text-white w-full">
                    ►   Watch Trailer
                    </button>
                    <button class="bg-yellow-500 w-full text-white rounded-md px-4 py-2 hover:bg-yellow-700 transition" onclick="openModal('modelConfirm')">
                        Write a Review
                    </button>
                </div>
                <dialog  id="trailer_dialog" draggable="true">
                    <div class="w-full flex items-center gap-5">
                    <button class="aspect-square   p-3 bg-red-500 text-white text-2xl duration-200 hover:px-5" onclick="closeDialog()">X</button>
                    <h3 class="text-xl font-bold"><?php echo "Watch ".$movie['title']." Trailer";?></h3>
                    </div>
                <iframe width="560" height="315" src=<?php echo"https://www.youtube.com/embed/".$movie['trailer_link'];?> title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </dialog>

                <div class="flex flex-col gap-3 w-[700px]">
                    <h2 class="text-4xl font-bold capitalize"><?php echo $movie['title']; ?></h2>
                    <h4 class="font-bold text-xl">Average Rating: <?php echo round($avgRating, 1); ?> / 5</h4>
                    <form action="add_favorite.php" method="post" onsubmit="return confirmFavorite();">
                        <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">
                        <button type="submit" class="flex gap-2 items-center text-red-500 text-2xl font-bold">
                            <div class="h-14 text-center flex items-center justify-center  aspect-square border-solid border-2 rounded-full border-red-500">&hearts;</div> Add to Favorites
                        </button>
                    </form>
                    <!-- Tabs -->
                    <div data-controller="tabs" data-tabs-index-value="0" class="mx-6 mt-6">
                        <ul class="flex flex-wrap w-full gap-10 border-b border-gray-200 dark:border-gray-700">
                            <li class="mr-2 border-solid border-b-4 border-yellow-500" data-action="click->tabs#change" data-tabs-target="tab">
                                <a href="#" class="inline-block text-red-500 text-xl font-bold">#Overview</a>
                            </li>
                            <li class="mr-2 inactive border-solid border-yellow-500" data-action="click->tabs#change" data-tabs-target="tab">
                                <a href="#" class="inline-block text-red-500 text-xl font-bold ">#Reviews</a>
                            </li>
                            <li class="mr-2 inactive border-solid border-yellow-500" data-action="click->tabs#change" data-tabs-target="tab">
                                <a href="#" class="inline-block text-red-500 text-xl font-bold">Related Movies</a>
                            </li>
                        </ul>
                        <div class="hidden px-1 py-2" data-tabs-target="panel">
                            <h3 class="text-lg font-semibold">
                                <p><?php echo $movie['description']; ?></p>
                            </h3>
                        </div>
                        <div class="hidden px-1 py-2" data-tabs-target="panel">
                            <h3 class="text-lg font-semibold">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p>Related Movies To</p>
                                        <h3 class="text-2xl font-bold"><?php echo $movie['title']; ?></h3>
                                    </div>
                                    <button class="bg-red-500 text-white rounded-md px-4 py-2 hover:bg-rose-700 transition" onclick="openModal('modelConfirm')">
                                        Write a Review
                                    </button>
                                </div>
                                <div class="my-3">
                                    <p class="text-gray-400">Found <?php echo count($reviews); ?> reviews in total</p>
                                </div>
                                <div class="max-h-[400px] overflow-auto">
                                    <?php if ($reviews) : ?>
                                        <?php foreach ($reviews as $review) : ?>
                                            <div class="p-3">
                                                <div class="">
                                                    <div class="flex gap-3">
                                                        <img src=<?php echo "https://i.pravatar.cc/100?u=m" . $review["username"]; ?> class="w-20 aspect-square" alt="profile" />
                                                        <div class="flex flex-col">
                                                            <h5 class="card-title"><?php echo $review['username']; ?></h5>
                                                            <div class="star-rating">
                                                                <?php for ($i = $review['rating']; $i >= 1; $i--) : ?>
                                                                    <label for="star<?php echo $i; ?>_<?php echo $review['id'];  ?>" class="text-yellow-500 text-xl">&#9733;</label>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="card-text"><?php echo $review['review']; ?></p>
                                            </div>
                                            <hr>

                                        <?php endforeach; ?>

                                    <?php else : ?>
                                        <p>No reviews yet.</p>
                                    <?php endif; ?>
                                </div>
                            </h3>
                        </div>
                       <div class="flex items-center gap-2">
    <button class="arrow-left bg-gray-300 text-black px-2 py-1 rounded-md">◀</button>
    <div class="related-movies flex overflow-x-auto">
        <?php 
        $limitedMovies = array_slice($randomMovies, 0, 8);
        foreach ($limitedMovies as $movie) : ?>
            <div class="flex-none mb-4">
                <a href="movie_detail.php?id=<?php echo $movie['id']; ?>">
                    <?php
                    $imagePath = 'images/' . $movie['image'];
                    if (file_exists($imagePath)) {
                        echo '<img src="' . $imagePath . '" class="w-[150px] h-[200px] " alt="' . $movie['title'] . '">';
                    } else {
                        echo '<img src="images/placeholder.jpg" class="card-img-top" alt="Placeholder">';
                    }
                    ?>
                </a>
            </div> 
        <?php endforeach; ?>
    </div>
    <button class="arrow-right bg-gray-300 text-black px-2 py-1 rounded-md">▶</button>
</div>


                                    <script>
    document.querySelector('.arrow-left').addEventListener('click', function() {
        document.querySelector('.related-movies').scrollBy({
            left: -150, // Adjust this value for the scroll distance
            behavior: 'smooth'
        });
    });

    document.querySelector('.arrow-right').addEventListener('click', function() {
        document.querySelector('.related-movies').scrollBy({
            left: 150, // Adjust this value for the scroll distance
            behavior: 'smooth'
        });
    });
</script>

                                </div>
                                            </div>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>






    <div id="modelConfirm" class="fixed hidden z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 ">
        <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-md p-3">
            <div class="w-fulls">
                <h3 class="mt-4 font-bold text-3xl mb-2">Add a Review</h3>

                <form action="add_review.php" method="post">
                    <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">
                    <div class="form-group">
                        <label for="rating">Your Rating</label>
                        <div class="star-rating">
                            <?php for ($i = 1; $i < 6; $i++) : ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>" class="text-yellow-500 text-xl">&#9733;</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="review">Your Review</label>
                        <textarea name="review" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Submit Review</button>
                </form>
            </div>

        </div>
    </div>




    <?php include 'footer.php'; ?>
</body>
<script type="text/javascript">
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        document.getElementById(modalId).style.display = 'block'
        document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden')

        const closModalOnClickOutside = function(event){
            if(event.target === modal) {
                window.closeModal(modalId);
                document.removeEventListner('click', closModalOnClickOutside);

            }
        };
        document.addEventListener('click', closModalOnClickOutside);
    }

    window.closeModal = function(modalId) {
        document.getElementById(modalId).style.display = 'none'
        document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
    }

    // Close all modals when press ESC
    document.onkeydown = function(event) {
        event = event || window.event;
        if (event.keyCode === 27) {
            document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
            let modals = document.getElementsByClassName('modal');
            Array.prototype.slice.call(modals).forEach(i => {
                i.style.display = 'none'
            })
        }
    };
</script>
<script type="module">
    import {
        Application,
        Controller
    } from "https://unpkg.com/@hotwired/stimulus/dist/stimulus.js"
    window.Stimulus = Application.start()

    Stimulus.register("tabs", class extends Controller {
        static targets = ["tab", "panel"]
        static values = {
            index: Number
        }

        initialize() {
            this.showTab()
        }

        change(event) {
            event.preventDefault()
            this.indexValue = this.tabTargets.indexOf(event.currentTarget)
            this.showTab()
        }

        showTab() {
            this.panelTargets.forEach((el, index) => {
                index == this.indexValue ? el.classList.remove("hidden") : el.classList.add("hidden")
            })
            this.tabTargets.forEach((el, index) => {
                index == this.indexValue ? el.classList.add("border-b-4") : el.classList.remove("border-b-4")
            })
        }
    })
</script>
<script>
    function openDialog(){
        document.getElementById('trailer_dialog').show();
    }
    function closeDialog(){
        document.getElementById('trailer_dialog').close();
    }
    function confirmFavorite() {
        return confirm("Are you sure you want to add this movie to your favorites?");
    }
</script>