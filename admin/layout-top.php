<?php
/**
 * FreshCart Store - Admin layout (top)
 * Expects $admin_title to be set. Admin auth is checked by each page.
 */
require_once __DIR__ . '/../includes/functions.php';
$admin = current_user();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(($admin_title ?? 'Admin') . ' - FreshCart Admin') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">FreshCart <span class="text-danger">Admin</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#anav" aria-controls="anav" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="anav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="categories.php">Categories</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="../index.php" target="_blank">View Store</a></li>
      </ul>
      <span class="navbar-text text-white-50 me-3"><?= e($admin['name'] ?? '') ?></span>
      <a class="btn btn-outline-light btn-sm" href="../logout.php">Log out</a>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php flash_show(); ?>
