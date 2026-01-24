<?php
require_once("../includes/auth.php");
require_role("Tecnico");

$page_title = "Panel Técnico";
require_once __DIR__ . '/../includes/header.php';

$flash_ok = $_SESSION["flash_ok"] ?? "";
unset($_SESSION["flash_ok"]);
?>

<div class="card">
  <p class="subtitle">
    Bienvenido,
    <strong><?= htmlspecialchars($_SESSION["nombre"]) ?></strong>
  </p>


  <?php if ($flash_ok): ?>
    <div class="alert" style="border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.12);">
      <?= htmlspecialchars($flash_ok) ?>
    </div>
  <?php endif; ?>

  <div class="navlinks">
    <a href="../usuarios/crear_usuario.php">Crear Usuario</a>
    <a href="../tickets/crear_ticket.php">Crear Ticket</a>
    <a href="../tickets/listar_tickets.php">Mis Tickets</a>
    <a href="../tickets/tickets_globales.php">Tickets Globales</a>
    <a href="../tickets/tickets_asignados.php">Tickets Asignados</a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
