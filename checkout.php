<?php
/**
 * FreshCart Store - Checkout (Cash on Delivery)
 * Places the order, reduces stock, clears the cart.
 */
require_once __DIR__ . '/includes/functions.php';

$cart = cart_details();
if (!$cart['lines']) {
    flash('warning', 'Your cart is empty.');
    redirect('shop.php');
}
$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $payment = 'cod';

    if ($name === '' || $phone === '' || $address === '' || $city === '') {
        $errors[] = 'Please fill in your name, phone, address and city.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        // Re-check stock before placing the order
        foreach ($cart['lines'] as $line) {
            if ($line['qty'] > (int) $line['product']['stock']) {
                $errors[] = '"' . $line['product']['name'] . '" only has ' . (int) $line['product']['stock'] . ' left in stock.';
            }
        }
    }

    if (!$errors) {
        $pdo = db();
        $order_number = 'FC' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare(
                'INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, address, city, payment_method, subtotal, delivery_fee, total, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $order_number, $user['id'] ?? null, $name, $email, $phone, $address, $city, $payment,
                $cart['subtotal'], $cart['delivery'], $cart['total'], 'pending',
            ]);
            $order_id = (int) $pdo->lastInsertId();
            $item_stmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, price, qty) VALUES (?, ?, ?, ?, ?)'
            );
            $stock_stmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');
            foreach ($cart['lines'] as $line) {
                $item_stmt->execute([$order_id, $line['product']['id'], $line['product']['name'], $line['product']['price'], $line['qty']]);
                $stock_stmt->execute([$line['qty'], $line['product']['id'], $line['qty']]);
            }
            $pdo->commit();
            cart_clear();
            redirect('order-success.php?order=' . urlencode($order_number));
        } catch (Throwable $ex) {
            $pdo->rollBack();
            $errors[] = 'Could not place your order. Please try again.';
        }
    }
}

$page_title = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>
<h2 class="section-title mb-4">Check<span>out</span></h2>
<?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
<div class="row g-4">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Delivery Details</h5>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-2"><label class="form-label">Full Name</label><input class="form-control" name="name" value="<?= e($_POST['name'] ?? $user['name'] ?? '') ?>" required></div>
          <div class="row g-2">
            <div class="col-md-6 mb-2"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= e($_POST['email'] ?? $user['email'] ?? '') ?>" required></div>
            <div class="col-md-6 mb-2"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?= e($_POST['phone'] ?? $user['phone'] ?? '') ?>" required></div>
          </div>
          <div class="mb-2"><label class="form-label">Full Address</label><textarea class="form-control" name="address" rows="2" required><?= e($_POST['address'] ?? '') ?></textarea></div>
          <div class="mb-3"><label class="form-label">City</label><input class="form-control" name="city" value="<?= e($_POST['city'] ?? '') ?>" required></div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <div class="form-check"><input class="form-check-input" type="radio" name="payment" id="cod" checked><label class="form-check-label" for="cod">Cash on Delivery (pay when your order arrives)</label></div>
          </div>
          <button class="btn btn-primary btn-lg w-100">Place Order — <?= money($cart['total']) ?></button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Your Order</h5>
        <?php foreach ($cart['lines'] as $line): ?>
          <div class="d-flex justify-content-between small">
            <span><?= e($line['product']['name']) ?> x <?= (int) $line['qty'] ?></span>
            <span><?= money($line['line_total']) ?></span>
          </div>
        <?php endforeach; ?>
        <hr>
        <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= money($cart['subtotal']) ?></span></div>
        <div class="d-flex justify-content-between"><span>Delivery</span><span><?= $cart['delivery'] ? money($cart['delivery']) : 'FREE' ?></span></div>
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span><?= money($cart['total']) ?></span></div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
