<?php
/**
 * FreshCart Store - Shared product card (used by index.php and shop.php)
 * Expects $p (product row) to be set.
 */
$discount = (!empty($p['old_price']) && $p['old_price'] > $p['price'])
    ? (int) round((1 - $p['price'] / $p['old_price']) * 100)
    : 0;
$img = !empty($p['image']) ? $p['image'] : 'assets/images/products/placeholder.jpg';
?>
<div class="product-card">
  <div class="position-relative">
    <a href="product.php?id=<?= (int) $p['id'] ?>">
      <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>">
    </a>
    <?php if ($discount > 0): ?><span class="badge-sale">-<?= $discount ?>%</span><?php endif; ?>
  </div>
  <div class="p-3">
    <h6 class="mb-1"><a class="text-dark text-decoration-none" href="product.php?id=<?= (int) $p['id'] ?>"><?= e($p['name']) ?></a></h6>
    <p class="text-muted small mb-2"><?= e(mb_strimwidth($p['description'], 0, 80, '...')) ?></p>
    <div class="d-flex align-items-baseline gap-2">
      <span class="price"><?= money($p['price']) ?></span>
      <?php if (!empty($p['old_price'])): ?><span class="old-price"><?= money($p['old_price']) ?></span><?php endif; ?>
    </div>
    <div class="d-flex gap-2 mt-2">
      <form action="cart.php" method="post" class="flex-grow-1">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
        <input type="hidden" name="qty" value="1">
        <button class="btn btn-primary btn-sm w-100" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
          <i class="bi bi-cart-plus"></i> <?= $p['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
        </button>
      </form>
      <a href="product.php?id=<?= (int) $p['id'] ?>" class="btn btn-outline-primary btn-sm">View</a>
    </div>
    <?php if ($p['stock'] > 0 && $p['stock'] <= 5): ?>
      <p class="text-danger small mb-0 mt-2">Only <?= (int) $p['stock'] ?> left in stock</p>
    <?php endif; ?>
  </div>
</div>
