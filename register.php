<?php
require_once 'includes/auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }

$errors = [];
$name = $email = $company = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = strtolower(trim($_POST['email'] ?? ''));
    $company = trim($_POST['company'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($pass !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $st = db()->prepare('SELECT id FROM users WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) {
            $errors[] = 'This email is already registered.';
        } else {
            $ins = db()->prepare('INSERT INTO users (name, email, password, company) VALUES (?, ?, ?, ?)');
            $ins->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $company]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}

$title = 'Register';
include 'includes/header.php';
?>
<main class="auth-wrap">
  <form method="post" class="card auth-card" id="regForm" novalidate>
    <h2>Create your account</h2>
    <?php foreach ($errors as $err): ?><div class="alert error"><?= e($err) ?></div><?php endforeach; ?>

    <label>Full name</label>
    <input type="text" name="name" value="<?= e($name) ?>" required>

    <label>Company / Organisation</label>
    <input type="text" name="company" value="<?= e($company) ?>">

    <label>Email</label>
    <input type="email" name="email" value="<?= e($email) ?>" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Confirm password</label>
    <input type="password" name="confirm" required>

    <button class="btn" type="submit">Register</button>
    <p class="muted">Already have an account? <a href="login.php">Login</a></p>
  </form>
</main>
<?php include 'includes/footer.php'; ?>
