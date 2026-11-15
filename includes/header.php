<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'ESG Auditor') ?> | ESG Auditor</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="nav">
  <a href="index.php" class="brand">🌿 ESG Auditor</a>
  <div class="links">
    <?php if (is_logged_in()): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="audit.php">New Audit</a>
      <span class="hello">Hi, <?= e($_SESSION['user_name']) ?></span>
      <a href="logout.php" class="btn btn-small">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php" class="btn btn-small">Register</a>
    <?php endif; ?>
  </div>
</nav>
