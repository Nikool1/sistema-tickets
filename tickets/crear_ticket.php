<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}
require_once("../config/db.php");

$page_title = "Crear Ticket";
require_once __DIR__ . '/../includes/header.php';

$cats = $conn->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria");
$error = $_GET["error"] ?? "";
$ok = $_GET["ok"] ?? "";
?>

<div class="card">
  <h1 class="title">Crear Ticket</h1>
  <p class="subtitle">Registra una solicitud a Informática</p>

  <?php if ($error): ?>
    <div class="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($ok): ?>
    <div class="alert" style="border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.12);">
      <?= htmlspecialchars($ok) ?>
    </div>
  <?php endif; ?>

  <form class="form" method="post" action="guardar_ticket.php">
    <div class="field">
      <label>Categoría</label>
      <select name="id_categoria" required>
        <option value="">-- Seleccione --</option>
        <?php while($c = $cats->fetch_assoc()): ?>
          <option value="<?= (int)$c["id_categoria"] ?>">
            <?= htmlspecialchars($c["nombre_categoria"]) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="field">
      <label>Descripción</label>
      <textarea name="descripcion" rows="6" required></textarea>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <button class="btn btn-primary" type="submit">Crear</button>
      <a class="btn" href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">Volver al panel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
