<?php
session_start();
require 'db.php';

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - GradeTracker</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <main class="auth-page">
    <section class="auth-box">
      <h2>Register</h2>
      <?php if ($message): ?>
        <p style="color:var(--fail)"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <form method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required minlength="6">
        </div>
        <button type="submit" class="btn" style="width:100%">Register</button>
      </form>
      <p style="margin-top:16px;text-align:center">Already have an account? <a href="login.php">Login</a></p>
    </section>
  </main>
</body>
</html>