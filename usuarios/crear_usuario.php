<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}
// solo tecnico(2) o admin(3)
if (!isset($_SESSION["id_rol"]) || ($_SESSION["id_rol"] != 2 && $_SESSION["id_rol"] != 3)) {
  header("Location: ../auth/login.php");
  exit;
}

$error = $_GET["error"] ?? "";
$ok = $_GET["ok"] ?? "";

$page_title = "Crear Usuario";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card">
  <h1 class="title">Crear Usuario</h1>
  <p class="subtitle">Registro de usuarios del sistema</p>

  <?php if ($error): ?>
    <div class="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($ok): ?>
    <div class="alert" style="border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.12);">
      <?= htmlspecialchars($ok) ?>
    </div>
  <?php endif; ?>

  <form class="form" method="post" action="guardar_usuario.php">
    <div class="field">
      <label>Nombre</label>
      <input class="input" type="text" name="nombre_usuario" required>
    </div>

    <div class="field">
      <label>RUT</label>
      <input class="input" type="text" name="rut" required>
    </div>

    <div class="field">
      <label>Correo institucional</label>
      <input class="input" type="email" name="correo_institucional" required>
    </div>

    <div class="field">
      <label>Contraseña</label>
      <input class="input" type="password" name="password" required>
    </div>

    <div class="field">
      <label>Rol</label>
      <select name="id_rol" required>
        <option value="1">Funcionario</option>
        <option value="2">Tecnico</option>
        <option value="3">Administrador</option>
      </select>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <button class="btn btn-primary" type="submit">Crear</button>
      <a class="btn" href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">Volver al panel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
