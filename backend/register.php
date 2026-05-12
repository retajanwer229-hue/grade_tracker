<?php
require 'db.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($username) || empty($password)) {
        $message = "❌ All fields are required.";
    } elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $message = "⚠️ Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed])) {
                header("Location: login.php");
                exit();
            } else {
                $message = "❌ Error registering. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - GradeTracker</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <main class="auth-page">
    <section class="auth-box">
      <h2>Register</h2>
      <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <form method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6">

        <button type="submit" class="btn">Register</button>
      </form>
      <p>Already have an account? <a href="login.php">Login</a></p>
    </section>
  </main>
</body>
</html>
