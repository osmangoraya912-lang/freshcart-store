<?php
/**
 * FreshCart Store - Admin add / edit product (with image upload)
 */
require_once __DIR__ . '/../includes/functions.php';
$admin = require_admin();

$id = (int) ($_GET['id'] ?? 0);
$product = null;
if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        flash('danger', 'Product not found.');
        redirect('products.php');
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $old_price = trim($_POST['old_price'] ?? '') === '' ? null : (float) $_POST['old_price'];
    $stock = (int) ($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $image = $product['image'] ?? '';

    if ($name === '' || $description === '' || $price <= 0 || $category_id <= 0) {
        $errors[] = 'Please fill in the name, category, description and a price above zero.';
    }

    // Image upload (optional on edit, required on add if no image yet)
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed. Please try again.';
        } elseif (!isset($allowed[$ext]) || $file['type'] !== $allowed[$ext]) {
            $errors[] = 'Image must be JPG, PNG or WebP.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image must be smaller than 2 MB.';
        } else {
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            $new_name = 'product-' . bin2hex(random_bytes(8)) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $new_name)) {
                if (!empty($image) && str_starts_with($image, 'uploads/')) {
                    @unlink(__DIR__ . '/../' . $image);
                }
                $image = 'uploads/' . $new_name;
            } else {
                $errors[] = 'Could not save the uploaded image.';
            }
        }
    }

    if (!$errors) {
        if ($product) {
            $stmt = db()->prepare('UPDATE products SET category_id=?, name=?, description=?, price=?, old_price=?, stock=?, image=?, featured=? WHERE id=?');
            $stmt->execute([$category_id, $name, $description, $price, $old_price, $stock, $image, $featured, $product['id']]);
            flash('success', 'Product updated.');
        } else {
            if ($image === '') {
                $image = 'assets/images/products/placeholder.jpg';
            }
            $stmt = db()->prepare('INSERT INTO products (category_id, name, description, price, old_price, stock, image, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$category_id, $name, $description, $price, $old_price, $stock, $image, $featured]);
            flash('success', 'Product added.');
        }
        redirect('products.php');
    }
}

$cats = categories();
$admin_title = $product ? 'Edit Product' : 'Add Product';
require_once __DIR__ . '/layout-top.php';
?>
<h2 class="section-title mb-4"><?= $product ? 'Edit <span>Product</span>' : 'Add <span>Product</span>' ?></h2>
<?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
<div class="card"><div class="card-body">
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="mb-2"><label class="form-label">Product Name</label><input class="form-control" name="name" value="<?= e($_POST['name'] ?? $product['name'] ?? '') ?>" required></div>
    <div class="row g-2">
      <div class="col-md-6 mb-2"><label class="form-label">Category</label>
        <select class="form-select" name="category_id" required>
          <option value="">Choose...</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= (int) ($_POST['category_id'] ?? $product['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 mb-2"><label class="form-label">Price (Rs.)</label><input type="number" step="0.01" min="0" class="form-control" name="price" value="<?= e($_POST['price'] ?? $product['price'] ?? '') ?>" required></div>
      <div class="col-md-3 mb-2"><label class="form-label">Old Price (optional)</label><input type="number" step="0.01" min="0" class="form-control" name="old_price" value="<?= e($_POST['old_price'] ?? $product['old_price'] ?? '') ?>"></div>
    </div>
    <div class="row g-2">
      <div class="col-md-6 mb-2"><label class="form-label">Stock</label><input type="number" min="0" class="form-control" name="stock" value="<?= e($_POST['stock'] ?? $product['stock'] ?? 0) ?>" required></div>
      <div class="col-md-6 mb-2"><label class="form-label">Product Image (JPG/PNG/WebP, max 2 MB)</label><input type="file" class="form-control" name="image" accept=".jpg,.jpeg,.png,.webp"></div>
    </div>
    <div class="mb-2"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3" required><?= e($_POST['description'] ?? $product['description'] ?? '') ?></textarea></div>
    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="featured" id="featured" <?= !empty($_POST['featured']) || !empty($product['featured']) ? 'checked' : '' ?>><label class="form-check-label" for="featured">Featured product (show on homepage)</label></div>
    <?php if (!empty($product['image'])): ?>
      <p class="text-muted small">Current image: <img src="<?= e(str_starts_with($product['image'], 'assets/') ? '../' . $product['image'] : '../' . $product['image']) ?>" alt="" style="height: 60px;" class="rounded"></p>
    <?php endif; ?>
    <button class="btn btn-primary"><?= $product ? 'Save Changes' : 'Add Product' ?></button>
    <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
  </form>
</div></div>
<?php require_once __DIR__ . '/layout-bottom.php'; ?>
