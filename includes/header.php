<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title ?? "Sistema de Tickets") ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <div class="brand">
      <span>Sistema de Tickets</span>
      <?php if (!empty($_SESSION["nombre_rol"])): ?>
        <span class="badge"><?= htmlspecialchars($_SESSION["nombre_rol"]) ?></span>
      <?php endif; ?>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <?php if (!empty($_SESSION["nombre"])): ?>
        <span class="badge">👤 <?= htmlspecialchars($_SESSION["nombre"]) ?></span>
      <?php endif; ?>
      <a class="btn" href="../auth/logout.php">Cerrar sesión</a>
    </div>
  </div>
</div>

<div class="container">
