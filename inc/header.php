<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($page_title ?? 'Sistem Musik'); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/theme.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand"><a href="daftarStudio.php">Sistem<span class="accent"> Penyewaan Studio Musik</span></a></div>
    <nav class="main-nav">
      <button class="mobile-toggle" aria-expanded="false" aria-label="Toggle menu"><i class="fa fa-bars"></i></button>
      <ul>
        <?php if(!empty($_SESSION['user_id'])): ?>
          <li><a href="daftarStudio.php"><i class="fa fa-music"></i> Studio</a></li>
          <li><a href="riwayatBooking.php"><i class="fa fa-clock-rotate-left"></i> Riwayat</a></li>
          <?php if(($_SESSION['role'] ?? '') === 'admin'): ?>
            <li><a href="tampilStudio.php"><i class="fa fa-cog"></i> Admin</a></li>
            <li><a href="tampilReservasi.php"><i class="fa fa-calendar-check"></i> Reservasi</a></li>
          <?php endif; ?>
          <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
        <?php else: ?>
          <li><a href="login.php"><i class="fa fa-sign-in-alt"></i> Login</a></li>
          <li><a href="register.php"><i class="fa fa-user-plus"></i> Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>
<main class="container">
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash">
    <p><?php echo htmlspecialchars($_SESSION['flash']); ?></p>
  </div>
<?php unset($_SESSION['flash']); endif; ?>
