<?php
/**
 * FreshCart Store - Admin orders (list, view items, change status)
 */
require_once __DIR__ . '/../includes/functions.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (array_key_exists($status, order_statuses())) {
        db()->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash('success', 'Order status updated.');
    }
    redirect('orders.php' . ($id ? "?view=$id" : ''));
}

$view_id = (int) ($_GET['view'] ?? 0);
$view_order = null;
if ($view_id > 0) {
    $stmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$view_id]);
    $view_order = $stmt->fetch();
    if ($view_order) {
        $items = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $items->execute([$view_id]);
        $view_order['items'] = $items->fetchAll();
    }
}
$orders = db()->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
$admin_title = 'Orders';
require_once __DIR__ . '/layout-top.php';
?>
<h2 class="section-title mb-4">Ord<span>ers</span></h2>
<?php if ($view_order): ?>
  <div class="card mb-4"><div class="card-body">
    <h5 class="card-title">Order <?= e($view_order['order_number']) ?> <?= status_badge($view_order['status']) ?></h5>
    <p class="mb-1"><?= e($view_order['customer_name']) ?> — <?= e($view_order['customer_phone']) ?> — <?= e($view_order['customer_email']) ?></p>
    <p class="mb-1 text-muted"><?= e($view_order['address']) ?>, <?= e($view_order['city']) ?></p>
    <p class="text-muted small">Placed <?= e(date('d M Y, h:i A', strtotime($view_order['created_at']))) ?> — <?= $view_order['payment_method'] === 'cod' ? 'Cash on Delivery' : e($view_order['payment_method']) ?></p>
    <table class="table table-sm">
      <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($view_order['items'] as $item): ?>
          <tr><td><?= e($item['product_name']) ?></td><td><?= money($item['price']) ?></td><td><?= (int) $item['qty'] ?></td><td><?= money($item['price'] * $item['qty']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p class="mb-1">Subtotal: <?= money($view_order['subtotal']) ?> | Delivery: <?= money($view_order['delivery_fee']) ?> | <strong>Total: <?= money($view_order['total']) ?></strong></p>
    <form action="orders.php" method="post" class="d-flex gap-2 mt-2">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="status">
      <input type="hidden" name="id" value="<?= (int) $view_order['id'] ?>">
      <select class="form-select" name="status" style="max-width: 220px;">
        <?php foreach (order_statuses() as $key => $label): ?>
          <option value="<?= e($key) ?>" <?= $view_order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary">Update Status</button>
    </form>
  </div></div>
<?php endif; ?>

<?php if (!$orders): ?>
  <div class="alert alert-info">No orders yet.</div>
<?php else: ?>
  <div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><strong><?= e($o['order_number']) ?></strong><br><small class="text-muted"><?= e(date('d M Y', strtotime($o['created_at']))) ?></small></td>
          <td><?= e($o['customer_name']) ?><br><small class="text-muted"><?= e($o['city']) ?></small></td>
          <td><?= money($o['total']) ?></td>
          <td><?= status_badge($o['status']) ?></td>
          <td><a class="btn btn-sm btn-outline-primary" href="orders.php?view=<?= (int) $o['id'] ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table></div></div>
<?php endif; ?>
<?php require_once __DIR__ . '/layout-bottom.php'; ?>
