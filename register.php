<?php
/**
 * FreshCart Store - Customer registration
 */
require_once __DIR__ . '/includes/functions.php';
if (current_user()) {
    redirect('account.php');
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($name === '' || $email === '' || $phone === '') {
        $errors[] = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    } elseif ($pass !== $pass2) {
        $errors[] = 'Passwords do not match.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists. Please log in.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = db()->prepare('INSERT INTO users (name, email, phone, password, is_admin) VALUES (?, ?, ?, ?, 0)');
            $stmt->execute([$name, $email, $phone, $hash]);
            $user = db()->query("SELECT * FROM users WHERE id = " . (int) db()->lastInsertId())->fetch();
            login_user($user);
            flash('success', 'Welcome to FreshCart Store, ' . $name . '!');
            redirect('account.php');
        }
    }
}
$page_title = 'Sign up';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body p-4">
        <h2 class="h4 mb-3">Create your account</h2>
        <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-2"><label class="form-label">Full Name</label><input class="form-control" name="name" value="<?= e($_POST['name'] ?? '') ?>" required></div>
          <div class="mb-2"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= e($_POST['email'] ?? '') ?>" required></div>
          <div class="mb-2"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required></div>
          <div class="mb-2"><label class="form-label">Password (min 6 characters)</label><input type="password" class="form-control" name="password" required></div>
          <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" class="form-control" name="password2" required></div>
          <button class="btn btn-primary w-100">Sign up</button>
        </form>
        <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Log in</a></p>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
