<?php
/**
 * FreshCart Store - Homepage
 */
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

$featured = db()->query('SELECT * FROM products WHERE featured = 1 ORDER BY id LIMIT 8')->fetchAll();
$cats = categories();
?>
<section class="hero p-4 p-md-5 mb-5">
  <div class="row align-items-center">
    <div class="col-md-7">
      <h1 class="display-5 fw-bold">Shop Quality Products,<br><span class="text-danger">Delivered to You.</span></h1>
      <p class="lead text-muted">Electronics, fashion and home essentials at honest prices. Cash on delivery all over Pakistan.</p>
      <a href="shop.php" class="btn btn-primary btn-lg me-2">Shop Now</a>
      <a href="track.php" class="btn btn-outline-primary btn-lg">Track Order</a>
      <div class="d-flex gap-4 mt-4 small text-muted">
        <span><i class="bi bi-truck"></i> Fast Delivery</span>
        <span><i class="bi bi-cash-coin"></i> Cash on Delivery</span>
        <span><i class="bi bi-arrow-repeat"></i> Easy Returns</span>
      </div>
    </div>
    <div class="col-md-5 text-center d-none d-md-block">
      <img src="assets/images/hero.jpg" alt="FreshCart Store" class="img-fluid rounded-4 shadow-sm" style="max-height: 320px; object-fit: cover;">
    </div>
  </div>
</section>

<h2 class="section-title mb-3">Shop by <span>Category</span></h2>
<div class="row g-3 mb-5">
  <?php
  $icons = ['Electronics' => 'bi-phone', 'Fashion' => 'bi-bag', 'Home & Kitchen' => 'bi-house'];
  foreach ($cats as $c):
  ?>
    <div class="col-6 col-md-4">
      <a class="cat-card" href="shop.php?category=<?= (int) $c['id'] ?>">
        <i class="bi <?= e($icons[$c['name']] ?? 'bi-grid') ?>"></i>
        <h5 class="mt-2 mb-1"><?= e($c['name']) ?></h5>
        <p class="text-muted small mb-0"><?= e($c['description']) ?></p>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<h2 class="section-title mb-3">Featured <span>Products</span></h2>
<div class="row g-3">
  <?php foreach ($featured as $p): ?>
    <div class="col-6 col-md-3">
      <?php include __DIR__ . '/includes/product-card.php'; ?>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
