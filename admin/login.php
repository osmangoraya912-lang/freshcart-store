<?php
/**
 * FreshCart Store - Admin login
 */
require_once __DIR__ . '/../includes/functions.php';
$user = current_user();
if ($user && !empty($user['is_admin'])) {
    redirect('index.php');
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? AND is_admin = 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($pass, $admin['password'])) {
        login_user($admin);
        flash('success', 'Welcome back, admin!');
        redirect('index.php');
    }
    $errors[] = 'Wrong admin email or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login - FreshCart Store</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container py-5" style="max-width: 440px;">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h4 mb-3">Admin Login</h1>
      <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
      <form method="post">
        <?= csrf_field() ?>
        <div class="mb-2"><label class="form-label">Admin Email</label><input type="email" class="form-control" name="email" required></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
        <button class="btn btn-primary w-100">Log in</button>
      </form>
      <p class="text-center mt-3 mb-0"><a href="../index.php">Back to store</a></p>
    </div>
  </div>
</div>
</body>
</html>
