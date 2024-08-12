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
    }
}

// Fetch user information
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$pageTitle = "My Account";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <style>
    body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 500px;
        }

        .container h2 {
            font-size: 28px;
            margin-bottom: 25px;
            color: #333;
            text-align: center;
        }

        .container .form-group {
            margin-bottom: 20px;
        }

        .container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }

        .container input[type="text"],
        .container input[type="email"],
        .container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .container input[type="text"]:focus,
        .container input[type="email"]:focus,
        .container input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php">Go to home</a>
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
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank to keep current password)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" name="update_account" class="btn btn-primary">Update Account</button>
        </form>
    </div>
</body>
</html>
