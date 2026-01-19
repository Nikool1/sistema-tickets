<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}
//solo tecnico(2) o admin(3)
if (!isset($_SESSION["id_rol"]) || ($_SESSION["id_rol"] != 2 && $_SESSION["id_rol"] != 3)) {
  header("Location: ../auth/login.php");
  exit;
}
$error = $_GET["error"] ?? "";
$ok = $_GET["ok"] ?? "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Usuario</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <h1>Crear Usuario</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if ($ok): ?>
    <p style="color:green;"><?= htmlspecialchars($ok) ?></p>
  <?php endif; ?>

  <form method="post" action="guardar_usuario.php">
    <label>Nombre</label><br>
    <input type="text" name="nombre_usuario" required><br><br>

    <label>RUT</label><br>
    <input type="text" name="rut" required><br><br>

    <label>Correo institucional</label><br>
    <input type="email" name="correo_institucional" required><br><br>

    <label>Contraseña</label><br>
    <input type="password" name="password" required><br><br>

    <label>Rol</label><br>
    <select name="id_rol" required>
      <option value="1">Funcionario</option>
      <option value="2">Tecnico</option>
      <option value="3">Administrador</option>
    </select><br><br>

    <button type="submit">Crear</button>
  </form>

  <p><a href="../dashboards/<?=$_SESSION["dashboard"]?>">Volver al panel</a></p>
</body>
</html>
