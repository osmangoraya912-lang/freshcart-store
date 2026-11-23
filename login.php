<?php
/**
 * FreshCart Store - Customer login
 */
require_once __DIR__ . '/includes/functions.php';
if (current_user()) {
    redirect('account.php');
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($pass, $user['password'])) {
        login_user($user);
        flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect(!empty($user['is_admin']) ? 'admin/index.php' : 'account.php');
    }
    $errors[] = 'Wrong email or password.';
}
$page_title = 'Log in';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body p-4">
        <h2 class="h4 mb-3">Log in</h2>
        <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-2"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
          <button class="btn btn-primary w-100">Log in</button>
        </form>
        <p class="text-center mt-3 mb-0">No account yet? <a href="register.php">Sign up</a></p>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
