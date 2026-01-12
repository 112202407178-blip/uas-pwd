<?php
require_once 'config.php';

// Connect without specifying DB to create it if not exists
try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `" . DB_NAME . "` created or already exists.<br>";

    // Use the newly created DB
    $pdo->exec("USE `" . DB_NAME . "`");

    // Create tables - mirror of setup_database.sql
    $pdo->exec(file_get_contents(__DIR__ . '/setup_database.sql'));
    echo "Tabel dibuat (jika belum ada).<br>";

    // Tambah admin default jika belum ada
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $ins->execute(['admin', $password, 'admin']);
        echo "Admin default dibuat: username=admin password=admin123<br>";
    } else {
        echo "Admin sudah ada, tidak menambah user admin baru.<br>";
    }

    // Tambah contoh studio
    $stmt = $pdo->query('SELECT COUNT(*) FROM studios');
    if ($stmt->fetchColumn() == 0) {
        $s = $pdo->prepare('INSERT INTO studios (name, description, price_per_hour) VALUES (?, ?, ?)');
        $s->execute(['Studio A', 'Studio rekaman kecil, cocok untuk band kecil.', 100000.00]);
        $s->execute(['Studio B', 'Studio dengan peralatan live dan mixing.', 150000.00]);
        echo "Contoh data studio ditambahkan.<br>";
    }

    echo "Setup selesai. Silakan hapus atau proteksi `setup_db.php` setelah selesai untuk keamanan.";
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>