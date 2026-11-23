<?php
/**
 * FreshCart Store - Admin products list (+ delete)
 */
require_once __DIR__ . '/../includes/functions.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('SELECT image FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        // Do not delete seed/demo images in assets/, only uploaded files
        if (!empty($row['image']) && str_starts_with($row['image'], 'uploads/')) {
            @unlink(__DIR__ . '/../' . $row['image']);
        }
        db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        flash('success', 'Product deleted.');
    }
    redirect('products.php');
}

$products = db()->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC')->fetchAll();
$admin_title = 'Products';
require_once __DIR__ . '/layout-top.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="section-title mb-0">Prod<span>ucts</span></h2>
  <a href="product-form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
</div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th></th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead>
  <tbody>
    <?php foreach ($products as $p):
      $img = !empty($p['image']) ? $p['image'] : '../assets/images/products/placeholder.jpg';
      $img_url = str_starts_with($img, 'assets/') ? '../' . $img : '../' . $img;
    ?>
      <tr>
        <td><img src="<?= e($img_url) ?>" alt="" style="width: 52px; height: 52px; object-fit: cover;" class="rounded"></td>
        <td><strong><?= e($p['name']) ?></strong><?= $p['featured'] ? ' <span class="badge bg-warning text-dark">Featured</span>' : '' ?></td>
        <td><?= e($p['category_name']) ?></td>
        <td><?= money($p['price']) ?></td>
        <td><?= (int) $p['stock'] ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="product-form.php?id=<?= (int) $p['id'] ?>">Edit</a>
          <form action="products.php" method="post" class="d-inline" onsubmit="return confirm('Delete this product?');">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table></div></div>
<?php require_once __DIR__ . '/layout-bottom.php'; ?>
