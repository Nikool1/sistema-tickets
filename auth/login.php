<?php
session_start();
if (isset($_SESSION["id_usuario"])) {
  header("Location: ../dashboards/" . $_SESSION["dashboard"]);
  exit;
}
$error = $_GET["error"] ?? "";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Tickets</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="page-center">
  <div class="card">
    <h1>Sistema de Tickets</h1>
    <p class="muted">Ingreso de usuarios</p>

    <?php if ($error): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="validar_login.php">
      <label>Correo institucional</label>
      <input type="email" name="correo" required>

      <label>Contraseña</label>
      <input type="password" name="password" required>

      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
