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

$seriesId = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM series WHERE id = ?");
$stmt->bind_param("i", $seriesId);
$stmt->execute();
$series = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch reviews and average rating for the series
$stmt = $conn->prepare("SELECT reviews.*, AVG(reviews.rating) as avg_rating, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.series_id = ? GROUP BY reviews.id DESC");
$stmt->bind_param("i", $seriesId);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$avgRating = isset($reviews[0]) ? $reviews[0]['avg_rating'] : 0; // Check if there are reviews to avoid warnings
$stmt->close();

$pageTitle = $series['title'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rateflix Series Details - <?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/globals.css" />
    <link rel="stylesheet" href="css/styleguide.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <script defer src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Navigation bar and other components -->

    <div class="container mt-20">
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        <div class="flex flex-col items-center justify-center w-full">
            <div class="flex gap-20">
                <div class="flex flex-col gap-2 items-start">
                    <?php
                    $imagePath = 'images/series/' . $series['image'];
                    if (file_exists($imagePath)) {
                        echo '<img src="' . $imagePath . '" class="block w-[400px] h-[550px]" alt="' . htmlspecialchars($series['title']) . '">';
                    } else {
                        echo '<img src="images/series/placeholder.jpg" class="w-[400px]" alt="Placeholder">';
                    }
                    ?>
                    <button onclick="openDialog()" class="bg-red-500 px-3 py-2 text-lg rounded text-white w-full">
                        ► Watch Trailer
                    </button>
                    <button class="bg-yellow-500 w-full text-white rounded-md px-4 py-2 hover:bg-yellow-700 transition" onclick="openModal('modelConfirm')">
                        Write a Review
                    </button>
                </div>

                <!-- Trailer Dialog -->
                <dialog id="trailer_dialog" draggable="true">
                    <iframe width="560" height="315" src="<?= "https://www.youtube.com/embed/" . $series['trailer_link'] ?>" title="YouTube video player" frameborder="0" allowfullscreen></iframe>
                </dialog>

                <div class="flex flex-col gap-3 w-[700px]">
                    <h2 class="text-4xl font-bold capitalize"><?= htmlspecialchars($series['title']) ?></h2>
                    <h4 class="font-bold text-xl">Average Rating: <?= round($avgRating, 1) ?> / 5</h4>
                    <!-- More details and reviews -->
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for handling modals and dialogs -->
    <script>
        function openDialog() {
            document.getElementById('trailer_dialog').showModal();
        }

        function closeDialog() {
            document.getElementById('trailer_dialog').close();
        }
    </script>
</body>
</html>
