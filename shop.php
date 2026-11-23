<?php
/**
 * FreshCart Store - Shop page (filters: category, search, sort)
 */
$page_title = 'Shop';
require_once __DIR__ . '/includes/header.php';

$cat_id = (int) ($_GET['category'] ?? 0);
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$allowed_sorts = [
    'newest' => 'created_at DESC',
    'price_low' => 'price ASC',
    'price_high' => 'price DESC',
    'name' => 'name ASC',
];
$order_by = $allowed_sorts[$sort] ?? $allowed_sorts['newest'];

$where = [];
$params = [];
if ($cat_id > 0) {
    $where[] = 'category_id = ?';
    $params[] = $cat_id;
}
if ($q !== '') {
    $where[] = '(name LIKE ? OR description LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
$sql = 'SELECT * FROM products';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ' . $order_by;
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$cats = categories();
$cat_name = '';
foreach ($cats as $c) {
    if ((int) $c['id'] === $cat_id) {
        $cat_name = $c['name'];
    }
}
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
  <h2 class="section-title mb-0"><?= $cat_name ? 'Shop: <span>' . e($cat_name) . '</span>' : 'All <span>Products</span>' ?></h2>
  <form class="d-flex gap-2" method="get">
    <?php if ($cat_id): ?><input type="hidden" name="category" value="<?= $cat_id ?>"><?php endif; ?>
    <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
    <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()">
      <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option>
      <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
      <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
      <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
    </select>
  </form>
</div>

<?php if ($q !== ''): ?>
  <p class="text-muted">Showing results for "<strong><?= e($q) ?></strong>" — <?= count($products) ?> product(s) found. <a href="shop.php">Clear search</a></p>
<?php endif; ?>

<div class="row mb-3">
  <div class="col">
    <a href="shop.php" class="btn btn-sm <?= $cat_id === 0 ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
    <?php foreach ($cats as $c): ?>
      <a href="shop.php?category=<?= (int) $c['id'] ?>" class="btn btn-sm <?= $cat_id === (int) $c['id'] ? 'btn-primary' : 'btn-outline-primary' ?>"><?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (!$products): ?>
  <div class="alert alert-info">No products found. Try a different search or category.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($products as $p): ?>
      <div class="col-6 col-md-3">
        <?php include __DIR__ . '/includes/product-card.php'; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
