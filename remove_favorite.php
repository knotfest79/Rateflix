<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$userId = $_SESSION['user_id']; // Make sure to set user_id in session during login

// Only process the deletion if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['movie_id'])) {
    $movieId = $_POST['movie_id'];

    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND movie_id = ?");
    $stmt->bind_param("ii", $userId, $movieId);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: favorites.php");
    exit();
}
?>
