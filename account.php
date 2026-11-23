<?php
/**
 * FreshCart Store - Customer account (my orders)
 */
require_once __DIR__ . '/includes/functions.php';
$user = require_login();
$stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
$page_title = 'My Orders';
require_once __DIR__ . '/includes/header.php';
?>
<h2 class="section-title mb-1">Hello, <span><?= e($user['name']) ?></span></h2>
<p class="text-muted"><?= e($user['email']) ?> — <?= e($user['phone']) ?></p>
<h3 class="h5 mt-4 mb-3">My Orders</h3>
<?php if (!$orders): ?>
  <div class="alert alert-info">You have no orders yet. <a href="shop.php">Start shopping</a></div>
<?php else: ?>
  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><strong><?= e($o['order_number']) ?></strong></td>
              <td><?= e(date('d M Y', strtotime($o['created_at']))) ?></td>
              <td><?= money($o['total']) ?></td>
              <td><?= status_badge($o['status']) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="track.php?order=<?= e($o['order_number']) ?>">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
