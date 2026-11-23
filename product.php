<?php
/**
 * FreshCart Store - Product detail page
 */
require_once __DIR__ . '/includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) {
    $page_title = 'Product not found';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-warning">Product not found. <a href="shop.php">Back to shop</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
$page_title = $p['name'];
require_once __DIR__ . '/includes/header.php';

$related = db()->prepare('SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY id LIMIT 4');
$related->execute([$p['category_id'], $p['id']]);
$related = $related->fetchAll();
$discount = (!empty($p['old_price']) && $p['old_price'] > $p['price'])
    ? (int) round((1 - $p['price'] / $p['old_price']) * 100)
    : 0;
$img = !empty($p['image']) ? $p['image'] : 'assets/images/products/placeholder.jpg';
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="shop.php?category=<?= (int) $p['category_id'] ?>"><?= e($p['category_name']) ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= e($p['name']) ?></li>
  </ol>
</nav>

<div class="row g-4">
  <div class="col-md-5">
    <div class="position-relative">
      <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" class="img-fluid rounded-4 border w-100" style="object-fit: cover; max-height: 420px;">
      <?php if ($discount > 0): ?><span class="badge-sale" style="font-size: .9rem;">-<?= $discount ?>% OFF</span><?php endif; ?>
    </div>
  </div>
  <div class="col-md-7">
    <span class="badge bg-light text-dark border"><?= e($p['category_name']) ?></span>
    <h1 class="h3 mt-2"><?= e($p['name']) ?></h1>
    <div class="d-flex align-items-baseline gap-2 my-2">
      <span class="h4 text-danger mb-0"><?= money($p['price']) ?></span>
      <?php if (!empty($p['old_price'])): ?><span class="text-muted text-decoration-line-through"><?= money($p['old_price']) ?></span><?php endif; ?>
    </div>
    <p class="text-muted"><?= nl2br(e($p['description'])) ?></p>
    <p>
      <?php if ($p['stock'] > 0): ?>
        <span class="badge bg-success">In Stock (<?= (int) $p['stock'] ?> available)</span>
      <?php else: ?>
        <span class="badge bg-danger">Out of Stock</span>
      <?php endif; ?>
      <span class="badge bg-light text-dark border ms-1">Cash on Delivery</span>
      <span class="badge bg-light text-dark border ms-1">2-4 Day Delivery</span>
    </p>
    <form action="cart.php" method="post" class="d-flex gap-2 align-items-center">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
      <label class="form-label mb-0" for="qty">Qty</label>
      <input type="number" id="qty" name="qty" value="1" min="1" max="<?= (int) $p['stock'] ?>" class="form-control" style="width: 80px;">
      <button class="btn btn-primary" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>><i class="bi bi-cart-plus"></i> Add to Cart</button>
      <a href="cart.php" class="btn btn-outline-primary">View Cart</a>
    </form>
    <hr>
    <ul class="small text-muted">
      <li>Free delivery on orders over <?= money(FREE_DELIVERY_OVER) ?> (otherwise <?= money(DELIVERY_FEE) ?>).</li>
      <li>Pay cash when your order arrives. 7-day easy return.</li>
    </ul>
  </div>
</div>

<?php if ($related): ?>
  <h3 class="section-title mt-5 mb-3">Related <span>Products</span></h3>
  <div class="row g-3">
    <?php foreach ($related as $p): ?>
      <div class="col-6 col-md-3">
        <?php include __DIR__ . '/includes/product-card.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
