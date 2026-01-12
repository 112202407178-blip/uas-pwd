<?php
session_start();
require_once 'koneksi.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3) $errors[] = 'Username minimal 3 karakter.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $ins->execute([$username, $hash, 'user']);
            header('Location: login.php');
            exit;
        }
    }
}
?>
<?php $page_title = 'Register - Sistem Reservasi Studio'; require 'inc/header.php'; ?>

<div class="auth-page">
<div class="page-header"><h1>Register</h1></div>
<div class="card auth-card">
<?php if ($errors): ?>  
    <div class="errors">
        <?php foreach ($errors as $e) echo '<p style="color:red">' . htmlspecialchars($e) . '</p>'; ?>
    </div>
<?php endif; ?>
<form method="post">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button class="btn" type="submit">Daftar</button>
</form>
</div>
<p>Sudah punya akun? <a href="login.php">Login</a></p>
</div>
<?php require 'inc/footer.php'; ?>