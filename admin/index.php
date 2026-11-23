<?php
/**
 * FreshCart Store - Admin dashboard
 */
require_once __DIR__ . '/../includes/functions.php';
$admin = require_admin();
$admin_title = 'Dashboard';
require_once __DIR__ . '/layout-top.php';

$stats = [];
$stats['revenue'] = db()->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$stats['orders'] = db()->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$stats['products'] = db()->query('SELECT COUNT(*) FROM products')->fetchColumn();
$stats['customers'] = db()->query('SELECT COUNT(*) FROM users WHERE is_admin = 0')->fetchColumn();
$recent = db()->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 8')->fetchAll();
$low_stock = db()->query('SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 8')->fetchAll();
?>
<h2 class="section-title mb-4">Admin <span>Dashboard</span></h2>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body"><h3 class="text-danger"><?= money($stats['revenue']) ?></h3><p class="text-muted mb-0">Total Revenue</p></div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body"><h3><?= (int) $stats['orders'] ?></h3><p class="text-muted mb-0">Orders</p></div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body"><h3><?= (int) $stats['products'] ?></h3><p class="text-muted mb-0">Products</p></div></div></div>
  <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body"><h3><?= (int) $stats['customers'] ?></h3><p class="text-muted mb-0">Customers</p></div></div></div>
</div>

<div class="row g-4">
  <div class="col-md-8">
    <h3 class="h5 mb-3">Recent Orders</h3>
    <?php if (!$recent): ?>
      <div class="alert alert-info">No orders yet.</div>
    <?php else: ?>
      <div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($recent as $o): ?>
            <tr>
              <td><strong><?= e($o['order_number']) ?></strong><br><small class="text-muted"><?= e(date('d M Y', strtotime($o['created_at']))) ?></small></td>
              <td><?= e($o['customer_name']) ?></td>
              <td><?= money($o['total']) ?></td>
              <td><?= status_badge($o['status']) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="orders.php?view=<?= (int) $o['id'] ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table></div></div>
    <?php endif; ?>
  </div>
  <div class="col-md-4">
    <h3 class="h5 mb-3">Low Stock Alert</h3>
    <?php if (!$low_stock): ?>
      <div class="alert alert-success">All products are well stocked.</div>
    <?php else: ?>
      <div class="list-group">
        <?php foreach ($low_stock as $p): ?>
          <a href="product-form.php?id=<?= (int) $p['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between">
            <span><?= e($p['name']) ?></span><span class="badge bg-danger"><?= (int) $p['stock'] ?> left</span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <a href="product-form.php" class="btn btn-primary w-100 mt-3"><i class="bi bi-plus-lg"></i> Add New Product</a>
  </div>
</div>
<?php require_once __DIR__ . '/layout-bottom.php'; ?>
