</main>
<footer class="bg-dark text-light mt-5">
  <div class="container py-4">
    <div class="row">
      <div class="col-md-4">
        <h5 class="fw-bold">FreshCart <span class="text-danger">Store</span></h5>
        <p class="text-white-50 small"><?= e(SITE_TAGLINE) ?>. Cash on delivery available all over Pakistan.</p>
      </div>
      <div class="col-md-4">
        <h6>Quick Links</h6>
        <ul class="list-unstyled small">
          <li><a class="text-white-50 text-decoration-none" href="shop.php">Shop All Products</a></li>
          <li><a class="text-white-50 text-decoration-none" href="track.php">Track Your Order</a></li>
          <li><a class="text-white-50 text-decoration-none" href="cart.php">Your Cart</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6>Delivery</h6>
        <p class="text-white-50 small mb-1">Free delivery on orders over <?= money(FREE_DELIVERY_OVER) ?>.</p>
        <p class="text-white-50 small mb-0">Delivery fee: <?= money(DELIVERY_FEE) ?> otherwise. 2-4 working days.</p>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-center text-white-50 small mb-0">&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
