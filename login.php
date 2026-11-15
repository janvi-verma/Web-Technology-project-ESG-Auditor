<?php
require_once 'includes/auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    $st = db()->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $user = $st->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid email or password.';
}

$title = 'Login';
include 'includes/header.php';
?>
<main class="auth-wrap">
  <form method="post" class="card auth-card">
    <h2>Welcome back</h2>
    <?php if (isset($_GET['registered'])): ?><div class="alert ok">Account created. Please login.</div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button class="btn" type="submit">Login</button>
    <p class="muted">New here? <a href="register.php">Create an account</a></p>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
