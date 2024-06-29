<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email or username already exists
    $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $check_stmt->bind_param("ss", $email, $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['message'] = "Username or Email already exists!";
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $stmt->insert_id;
            header("location: login.php");
        } else {
            $_SESSION['message'] = "Registration failed: " . $stmt->error;
        }

        $stmt->close();
    }
    
    $check_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rateflix - Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style11.css">
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("repassword").value;
            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="text-center mb-4">
                <img src="images/logologin.png" alt="Rateflix" style="height: 50px;">
                <h2 class="mt-2">Register</h2>
                <p>Sign in to your account</p>
            </div>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <form action="register.php" method="post" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="username" class="sr-only">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="sr-only">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Your Email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="sr-only">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="repassword" class="sr-only">Re-Type Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" id="repassword" name="repassword" class="form-control" placeholder="Re-Type Password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">SIGN UP</button>
            </form>
            <div class="mt-3">
                <p>Already have an account? <a href="login.php" class="text-secondary">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
