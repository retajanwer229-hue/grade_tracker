<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($username) || empty($password)) {
        echo "❌ Username and password are required.";
        exit();
    }

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION["user_id"] = $user["id"];
        header("Location: ../index.php");
        exit();
    } else {
        echo "❌ Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - GradeTracker</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <main class="auth-page">
    <section class="auth-box">
      <h2>Login</h2>
      <form method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn">Login</button>
      </form>
      <p>Don't have an account? <a href="register.php">Register</a></p>
    </section>
  </main>
</body>
</html>
