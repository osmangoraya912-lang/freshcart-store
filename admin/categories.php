<?php
/**
 * FreshCart Store - Admin categories (add / delete)
 */
require_once __DIR__ . '/../includes/functions.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['action'] ?? '') === 'add') {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') {
            flash('danger', 'Category name is required.');
        } else {
            try {
                db()->prepare('INSERT INTO categories (name, description) VALUES (?, ?)')->execute([$name, $desc]);
                flash('success', 'Category added.');
            } catch (PDOException $ex) {
                flash('danger', 'That category already exists.');
            }
        }
    }
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $count = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $count->execute([$id]);
        if ((int) $count->fetchColumn() > 0) {
            flash('danger', 'This category still has products. Move or delete them first.');
        } else {
            db()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            flash('success', 'Category deleted.');
        }
    }
    redirect('categories.php');
}

$cats = categories();
$admin_title = 'Categories';
require_once __DIR__ . '/layout-top.php';
?>
<h2 class="section-title mb-4">Catego<span>ries</span></h2>
<div class="row g-4">
  <div class="col-md-5">
    <div class="card"><div class="card-body">
      <h5 class="card-title">Add Category</h5>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="mb-2"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
        <div class="mb-3"><label class="form-label">Description</label><input class="form-control" name="description"></div>
        <button class="btn btn-primary">Add Category</button>
      </form>
    </div></div>
  </div>
  <div class="col-md-7">
    <div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
      <thead><tr><th>Name</th><th>Description</th><th>Products</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($cats as $c):
          $n = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
          $n->execute([$c['id']]);
        ?>
          <tr>
            <td><strong><?= e($c['name']) ?></strong></td>
            <td><?= e($c['description']) ?></td>
            <td><?= (int) $n->fetchColumn() ?></td>
            <td class="text-end">
              <form action="categories.php" method="post" class="d-inline" onsubmit="return confirm('Delete this category?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div></div>
  </div>
</div>
<?php require_once __DIR__ . '/layout-bottom.php'; ?>
