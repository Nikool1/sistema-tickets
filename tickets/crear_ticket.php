<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}
require_once("../config/db.php");

$cats = $conn->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria");
$error = $_GET["error"] ?? "";
$ok = $_GET["ok"] ?? "";

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Ticket</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <h1>Crear Ticket</h1>

  <?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <?php if ($ok): ?>
    <p style="color:green;"><?= htmlspecialchars($ok) ?></p>
  <?php endif; ?>

  <form method="post" action="guardar_ticket.php">

    <label>Categoría</label><br>
    <select name="id_categoria" required>
      <option value="">-- Seleccione --</option>
      <?php while($c = $cats->fetch_assoc()): ?>
        <option value="<?= (int)$c["id_categoria"] ?>">
          <?= htmlspecialchars($c["nombre_categoria"]) ?>
        </option>
      <?php endwhile; ?>
    </select><br><br>

    <label>Descripción</label><br>
    <textarea name="descripcion" rows="6" cols="80" required></textarea><br><br>

    <button type="submit">Crear</button>
  </form>

  <p><a href="../dashboards/<?=$_SESSION["dashboard"]?>">Volver al panel</a></p>
</body>
</html>
