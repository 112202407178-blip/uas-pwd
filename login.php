<?php
session_start();
require_once 'koneksi.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        // set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: daftarStudio.php');
        exit;
    } else {
        $errors[] = 'Username atau password salah.';
    }
}
?>
<?php $page_title = 'Login - Sistem Reservasi Studio'; require 'inc/header.php'; ?>

<div class="auth-page">
<div class="page-header"><h1>Login</h1></div>
<div class="card auth-card">
<?php if ($errors): ?>  
    <p style="color:red"><?php echo htmlspecialchars($errors[0]); ?></p>
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
    <button class="btn" type="submit">Login</button>
</form>
</div>
<p>Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
<?php require 'inc/footer.php'; ?>