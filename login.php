<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        header("location: dashboard.php");
    } else {
        $_SESSION['message'] = "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rateflix - Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style11.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="text-center mb-4">
                <img src="images/logologin.png" alt="Rateflix" style="height: 50px;">
                <h2 class="mt-2">Login</h2>
                <p>Sign in to your account</p>
            </div>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="post">
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
                    <label for="password" class="sr-only">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
                <div class="text-right mt-2">
                    <a href="#" class="text-secondary">Forgot my password?</a>
                </div>
                <button type="button" class="btn btn-secondary btn-block mt-3" onclick="window.location.href='register.php'">Register</button>
            </form>
        </div>
    </div>
</body>
</html>
