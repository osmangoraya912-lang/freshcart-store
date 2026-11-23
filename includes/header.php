<?php
/**
 * FreshCart Store - Shared page header + navbar
 * Expects $page_title to be set by the page (optional).
 */
require_once __DIR__ . '/functions.php';
$user = current_user();
$cart_n = cart_count();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(($page_title ?? SITE_NAME) . ' - ' . SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><span class="brand-mark">F</span> FreshCart <span class="text-danger">Store</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
          <ul class="dropdown-menu">
            <?php foreach (categories() as $c): ?>
              <li><a class="dropdown-item" href="shop.php?category=<?= (int) $c['id'] ?>"><?= e($c['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="track.php">Track Order</a></li>
      </ul>
      <form class="d-flex me-2" action="shop.php" method="get" role="search">
        <input class="form-control form-control-sm" type="search" name="q" placeholder="Search products..." aria-label="Search" value="<?= e($_GET['q'] ?? '') ?>">
      </form>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link position-relative" href="cart.php"><i class="bi bi-cart3"></i> Cart
            <?php if ($cart_n > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $cart_n ?></span><?php endif; ?>
          </a>
        </li>
        <?php if ($user): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle"></i> <?= e($user['name']) ?></a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="account.php">My Orders</a></li>
              <?php if (!empty($user['is_admin'])): ?><li><a class="dropdown-item" href="admin/index.php">Admin Panel</a></li><?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">Log out</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Log in</a></li>
          <li class="nav-item"><a class="btn btn-primary btn-sm mt-1" href="register.php">Sign up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php flash_show(); ?>
