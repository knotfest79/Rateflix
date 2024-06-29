<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$movieId = $_POST['movie_id'];

// Check if movie is already in favorites
$stmt = $conn->prepare("SELECT * FROM favorites WHERE user_id = ? AND movie_id = ?");
$stmt->bind_param("ii", $userId, $movieId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['message'] = "This movie is already in your favorites!";
    header("location: movie_detail.php?id=$movieId");
    exit();
} else {
    // Add movie to favorites
    $stmt = $conn->prepare("INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $movieId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Movie added to favorites!";
    } else {
        $_SESSION['message'] = "Error adding movie to favorites: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("location: movie_detail.php?id=$movieId");
    exit();
}
?>
