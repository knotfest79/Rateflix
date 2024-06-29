<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = '';

// Handle account updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_account'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            if (!empty($password)) {
                $password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
                $stmt->bind_param("sssi", $username, $email, $password, $userId);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
                $stmt->bind_param("ssi", $username, $email, $userId);
            }

            if ($stmt->execute()) {
                $_SESSION['username'] = $username; // Update session username
                $message = "Account updated successfully!";
            } else {
                $message = "Error updating account: " . $stmt->error;
            }

            $stmt->close();
        }
    } elseif (isset($_POST['delete_review'])) {
        $reviewId = $_POST['review_id'];
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $reviewId, $userId);
        $stmt->execute();
        $stmt->close();
        $message = "Review deleted successfully!";
    }
}

// Fetch user information
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch user reviews
$stmt = $conn->prepare("SELECT reviews.id, reviews.review, reviews.rating, movies.title FROM reviews JOIN movies ON reviews.movie_id = movies.id WHERE reviews.user_id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = "My Account";
include 'header.php';
?>

<h2 class="mt-4">My Account</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<h3 class="mt-4">Update Account Details</h3>
<form action="profile.php" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
    </div>
    <div class="form-group">
        <label for="password">New Password (leave blank to keep current password)</label>
        <input type="password" name="password" class="form-control">
    </div>
    <button type="submit" name="update_account" class="btn btn-primary">Update Account</button>
</form>

<h3 class="mt-4">My Reviews</h3>
<?php if (count($reviews) > 0): ?>
    <?php foreach ($reviews as $review): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo $review['title']; ?></h5>
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?php echo $i; ?>_<?php echo $review['id']; ?>" name="rating<?php echo $review['id']; ?>" value="<?php echo $i; ?>" <?php if ($review['rating'] == $i) echo 'checked'; ?> disabled>
                        <label for="star<?php echo $i; ?>_<?php echo $review['id']; ?>">&#9733;</label>
                    <?php endfor; ?>
                </div>
                <p class="card-text"><?php echo $review['review']; ?></p>
                <form action="profile.php" method="post" style="display:inline;">
                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                    <button type="submit" name="delete_review" class="btn btn-danger">Delete Review</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>You have no reviews.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
