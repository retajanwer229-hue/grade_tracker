<?php
session_start();
require 'db.php';

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - GradeTracker</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <main class="auth-page">
    <section class="auth-box">
      <h2>Login</h2>
      <?php if ($error): ?>
        <p style="color:var(--fail)">❌ <?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <form method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn" style="width:100%">Login</button>
      </form>
      <p style="margin-top:16px;text-align:center">Don't have an account? <a href="register.php">Register</a></p>
    </section>
  </main>
</body>
</html>