<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$movieId = $_POST['movie_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];

// Validate the rating
if ($rating < 1 || $rating > 5) {
    $_SESSION['message'] = "Invalid rating value.";
    header("Location: movie_detail.php?id=$movieId");
    exit();
}

// Insert review into the database
$stmt = $conn->prepare("INSERT INTO reviews (movie_id, user_id, username, review, rating) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iissi", $movieId, $userId, $username, $review, $rating);

if ($stmt->execute()) {
    $_SESSION['message'] = "Review added successfully!";
} else {
    $_SESSION['message'] = "Error adding review: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: movie_detail.php?id=$movieId");
exit();
?>
