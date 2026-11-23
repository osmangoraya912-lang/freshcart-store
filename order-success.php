<?php
/**
 * FreshCart Store - Order success page
 */
require_once __DIR__ . '/includes/functions.php';
$number = trim($_GET['order'] ?? '');
$stmt = db()->prepare('SELECT * FROM orders WHERE order_number = ?');
$stmt->execute([$number]);
$order = $stmt->fetch();
$page_title = 'Order Placed';
require_once __DIR__ . '/includes/header.php';
?>
<?php if (!$order): ?>
  <div class="alert alert-warning">Order not found. <a href="shop.php">Continue shopping</a></div>
<?php else: ?>
  <div class="text-center py-4">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
    <h2 class="mt-3">Thank you! Your order is placed.</h2>
    <p class="lead">Order Number: <strong><?= e($order['order_number']) ?></strong></p>
    <p class="text-muted">We will call you to confirm your order. Pay cash when it arrives.<br>
    Delivery in 2-4 working days to <?= e($order['city']) ?>.</p>
    <p>Total: <strong><?= money($order['total']) ?></strong> (<?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : e($order['payment_method']) ?>)</p>
    <a href="track.php?order=<?= e($order['order_number']) ?>" class="btn btn-primary">Track Your Order</a>
    <a href="shop.php" class="btn btn-outline-primary">Continue Shopping</a>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
