<?php
/**
 * FreshCart Store - Shopping cart (add / update / remove)
 */
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    $pid = (int) ($_POST['product_id'] ?? 0);
    if ($action === 'add' && $pid > 0) {
        $stmt = db()->prepare('SELECT id, stock, name FROM products WHERE id = ?');
        $stmt->execute([$pid]);
        $prod = $stmt->fetch();
        if (!$prod) {
            flash('danger', 'Product not found.');
        } elseif ($prod['stock'] <= 0) {
            flash('danger', 'Sorry, this product is out of stock.');
        } else {
            $qty = min((int) ($_POST['qty'] ?? 1), (int) $prod['stock']);
            cart_add($pid, max(1, $qty));
            flash('success', '"' . $prod['name'] . '" added to your cart.');
        }
        $back = $_POST['back'] ?? 'cart.php';
        redirect($back === 'product.php' && $pid ? "product.php?id=$pid" : 'cart.php');
    }
    if ($action === 'update' && $pid > 0) {
        cart_set($pid, (int) ($_POST['qty'] ?? 0));
        flash('success', 'Cart updated.');
        redirect('cart.php');
    }
    if ($action === 'remove' && $pid > 0) {
        cart_remove($pid);
        flash('success', 'Item removed from cart.');
        redirect('cart.php');
    }
    if ($action === 'clear') {
        cart_clear();
        flash('success', 'Cart cleared.');
        redirect('cart.php');
    }
}

$page_title = 'Your Cart';
require_once __DIR__ . '/includes/header.php';
$cart = cart_details();
?>
<h2 class="section-title mb-4">Your <span>Cart</span></h2>
<?php if (!$cart['lines']): ?>
  <div class="alert alert-info">Your cart is empty. <a href="shop.php">Continue shopping</a></div>
<?php else: ?>
  <div class="row g-4">
    <div class="col-md-8">
      <div class="card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($cart['lines'] as $line):
                $p = $line['product'];
                $img = !empty($p['image']) ? $p['image'] : 'assets/images/products/placeholder.jpg';
              ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" style="width: 56px; height: 56px; object-fit: cover;" class="rounded">
                      <a href="product.php?id=<?= (int) $p['id'] ?>" class="text-dark text-decoration-none"><?= e($p['name']) ?></a>
                    </div>
                  </td>
                  <td><?= money($p['price']) ?></td>
                  <td>
                    <form action="cart.php" method="post" class="d-flex gap-1">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                      <input type="number" name="qty" value="<?= (int) $line['qty'] ?>" min="0" max="<?= (int) $p['stock'] ?>" class="form-control form-control-sm" style="width: 70px;">
                      <button class="btn btn-sm btn-outline-primary">Update</button>
                    </form>
                  </td>
                  <td><strong><?= money($line['line_total']) ?></strong></td>
                  <td>
                    <form action="cart.php" method="post">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="remove">
                      <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                      <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <form action="cart.php" method="post" class="mt-2">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="clear">
        <button class="btn btn-link text-danger p-0">Clear cart</button>
      </form>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Order Summary</h5>
          <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= money($cart['subtotal']) ?></span></div>
          <div class="d-flex justify-content-between"><span>Delivery</span><span><?= $cart['delivery'] ? money($cart['delivery']) : 'FREE' ?></span></div>
          <hr>
          <div class="d-flex justify-content-between fw-bold"><span>Total</span><span><?= money($cart['total']) ?></span></div>
          <?php if ($cart['delivery'] > 0): ?>
            <p class="text-muted small mt-2">Add <?= money(FREE_DELIVERY_OVER - $cart['subtotal']) ?> more for FREE delivery.</p>
          <?php endif; ?>
          <a href="checkout.php" class="btn btn-primary w-100 mt-2">Proceed to Checkout</a>
          <a href="shop.php" class="btn btn-outline-primary w-100 mt-2">Continue Shopping</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
