<?php
/**
 * FreshCart Store - Track an order by number (+ phone for safety)
 */
require_once __DIR__ . '/includes/functions.php';
$order = null;
$searched = false;
if (isset($_GET['order']) && trim($_GET['order']) !== '') {
    $searched = true;
    $number = trim($_GET['order']);
    $phone = trim($_GET['phone'] ?? '');
    if ($phone !== '') {
        $stmt = db()->prepare('SELECT * FROM orders WHERE order_number = ? AND customer_phone = ?');
        $stmt->execute([$number, $phone]);
    } else {
        $stmt = db()->prepare('SELECT * FROM orders WHERE order_number = ?');
        $stmt->execute([$number]);
    }
    $order = $stmt->fetch();
    if ($order) {
        $items = db()->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $items->execute([$order['id']]);
        $order['items'] = $items->fetchAll();
    }
}
$page_title = 'Track Order';
require_once __DIR__ . '/includes/header.php';
?>
<h2 class="section-title mb-4">Track Your <span>Order</span></h2>
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <form method="get">
          <div class="mb-2"><label class="form-label">Order Number (e.g. FC20261123ABC123)</label><input class="form-control" name="order" value="<?= e($_GET['order'] ?? '') ?>" required></div>
          <div class="mb-3"><label class="form-label">Phone Number (optional, for verification)</label><input class="form-control" name="phone" value="<?= e($_GET['phone'] ?? '') ?>"></div>
          <button class="btn btn-primary">Track Order</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <?php if ($searched && !$order): ?>
      <div class="alert alert-warning">No order found with that number. Please check the order number and try again.</div>
    <?php elseif ($order): ?>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Order <?= e($order['order_number']) ?></h5>
          <p class="mb-1">Status: <?= status_badge($order['status']) ?></p>
          <p class="mb-1 text-muted small">Placed on <?= e(date('d M Y, h:i A', strtotime($order['created_at']))) ?> — <?= e($order['customer_name']) ?>, <?= e($order['city']) ?></p>
          <hr>
          <?php foreach ($order['items'] as $item): ?>
            <div class="d-flex justify-content-between small">
              <span><?= e($item['product_name']) ?> x <?= (int) $item['qty'] ?></span>
              <span><?= money($item['price'] * $item['qty']) ?></span>
            </div>
          <?php endforeach; ?>
          <hr>
          <div class="d-flex justify-content-between fw-bold"><span>Total</span><span><?= money($order['total']) ?></span></div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
