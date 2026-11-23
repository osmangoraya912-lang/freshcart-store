<?php
/**
 * FreshCart Store - Shared helpers
 * Output escaping, money formatting, CSRF protection, auth guards,
 * flash messages and the session cart.
 */
require_once __DIR__ . '/db.php';

/* ---------- Output / formatting ---------- */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($amount): string
{
    return CURRENCY . number_format((float) $amount, 0);
}

/* ---------- Flash messages ---------- */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_show(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show" role="alert">'
            . e($f['message'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(403);
        exit('Invalid request. Please go back and try again.');
    }
}

/* ---------- Auth ---------- */
function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, name, email, phone, is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        flash('warning', 'Please log in first.');
        redirect('login.php');
    }
    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if (empty($user['is_admin'])) {
        http_response_code(403);
        exit('Admins only.');
    }
    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
}

/* ---------- Cart (stored in the session) ---------- */
function cart(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_add(int $product_id, int $qty = 1): void
{
    $qty = max(1, $qty);
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $qty;
}

function cart_set(int $product_id, int $qty): void
{
    if ($qty <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function cart_remove(int $product_id): void
{
    unset($_SESSION['cart'][$product_id]);
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}

function cart_count(): int
{
    return array_sum(cart());
}

/** Full cart lines with product data + totals. */
function cart_details(): array
{
    $items = cart();
    $lines = [];
    $subtotal = 0;
    if ($items) {
        $ids = array_keys($items);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = db()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        foreach ($stmt->fetchAll() as $p) {
            $qty = $items[$p['id']];
            $line_total = $p['price'] * $qty;
            $subtotal += $line_total;
            $lines[] = ['product' => $p, 'qty' => $qty, 'line_total' => $line_total];
        }
    }
    $delivery = ($subtotal <= 0 || $subtotal >= FREE_DELIVERY_OVER) ? 0 : DELIVERY_FEE;
    return ['lines' => $lines, 'subtotal' => $subtotal, 'delivery' => $delivery, 'total' => $subtotal + $delivery];
}

/* ---------- Shop data ---------- */
function categories(): array
{
    return db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
}

function order_statuses(): array
{
    return ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
}

function status_badge(string $status): string
{
    $map = ['pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
    $label = order_statuses()[$status] ?? $status;
    $class = $map[$status] ?? 'secondary';
    return '<span class="badge bg-' . $class . '">' . e($label) . '</span>';
}
