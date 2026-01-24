<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

$idUser = (int)$_SESSION["id_usuario"];
$idRol  = (int)($_SESSION["id_rol"] ?? 0);

$idTicket = (int)($_GET["id_ticket"] ?? 0);
if ($idTicket <= 0) {
  header("Location: ../dashboards/" . $_SESSION["dashboard"]);
  exit;
}

$return = $_GET["return"] ?? "globales";
$return = in_array($return, ["globales", "asignados", "mis"], true) ? $return : "globales";

$backUrl = "tickets_globales.php";
if ($return === "asignados") $backUrl = "tickets_asignados.php";
if ($return === "mis")       $backUrl = "listar_tickets.php";

$sql = "SELECT
          t.id_ticket, t.descripcion, t.prioridad,
          t.fecha_creacion, t.fecha_actualizacion,
          t.id_usuario, t.id_usuario2, t.id_categoria, t.id_estado,
          c.nombre_categoria,
          e.nombre_estado,
          u.nombre_usuario  AS creador,
          ut.nombre_usuario AS tecnico_asignado
        FROM tickets t
        JOIN categorias c ON c.id_categoria = t.id_categoria
        JOIN estados e    ON e.id_estado = t.id_estado
        JOIN usuarios u   ON u.id_usuario = t.id_usuario
        LEFT JOIN usuarios ut ON ut.id_usuario = t.id_usuario2
        WHERE t.id_ticket = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idTicket);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
  header("Location: {$backUrl}?error=Ticket+no+existe");
  exit;
}

$ticket = $res->fetch_assoc();

if ($idRol === 1 && (int)$ticket["id_usuario"] !== $idUser) {
  header("Location: {$backUrl}?error=No+tienes+permiso+para+ver+este+ticket");
  exit;
}

$bit = null;
$sqlBit = "SELECT
             b.accion, b.detalle, b.fecha_evento,
             ub.nombre_usuario AS usuario_bitacora
           FROM bitacora_ticket b
           JOIN usuarios ub ON ub.id_usuario = b.id_usuario
           WHERE b.id_ticket = ?
           ORDER BY b.id_bitacora DESC
           LIMIT 1";
$stmtB = $conn->prepare($sqlBit);
$stmtB->bind_param("i", $idTicket);
$stmtB->execute();
$resB = $stmtB->get_result();
if ($resB->num_rows === 1) $bit = $resB->fetch_assoc();

$flash_ok = $_SESSION["flash_ok"] ?? "";
unset($_SESSION["flash_ok"]);

$page_title = "Ticket #" . (int)$ticket["id_ticket"];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card">
  <h1 class="title">Ticket #<?= (int)$ticket["id_ticket"] ?></h1>
  <p class="subtitle">Detalle del ticket y última actualización</p>

  <?php if ($flash_ok): ?>
    <div class="alert" style="border-color: rgba(34,197,94,.35); background: rgba(34,197,94,.12);">
      <?= htmlspecialchars($flash_ok) ?>
    </div>
  <?php endif; ?>


  <div class="card" style="padding:14px; background: rgba(255,255,255,0.04); border-radius: 14px; border: 1px solid rgba(255,255,255,0.10); box-shadow:none;">
    <p><b>Creador:</b> <?= htmlspecialchars($ticket["creador"]) ?></p>
    <p><b>Categoría:</b> <?= htmlspecialchars($ticket["nombre_categoria"]) ?></p>
    <p><b>Prioridad:</b> <?= htmlspecialchars($ticket["prioridad"]) ?></p>
    <p><b>Estado:</b> <?= htmlspecialchars($ticket["nombre_estado"]) ?></p>
    <p><b>Técnico asignado:</b> <?= htmlspecialchars($ticket["tecnico_asignado"] ?? "Sin asignar") ?></p>
    <p><b>Fecha creación:</b> <?= htmlspecialchars($ticket["fecha_creacion"]) ?></p>
    <p><b>Última actualización (ticket):</b> <?= htmlspecialchars($ticket["fecha_actualizacion"]) ?></p>
  </div>

  <div style="margin-top:14px;">
    <h2 class="title" style="font-size:18px; margin-bottom:10px;">Descripción</h2>
    <textarea readonly rows="6" style="width:100%;"><?= htmlspecialchars($ticket["descripcion"]) ?></textarea>
  </div>

  <div style="margin-top:14px;">
    <h2 class="title" style="font-size:18px; margin-bottom:10px;">Última actualización (bitácora)</h2>

    <?php if ($bit): ?>
      <div class="card" style="padding:14px; background: rgba(255,255,255,0.04); border-radius: 14px; border: 1px solid rgba(255,255,255,0.10); box-shadow:none;">
        <p><b>Acción:</b> <?= htmlspecialchars($bit["accion"]) ?></p>
        <p><b>Por:</b> <?= htmlspecialchars($bit["usuario_bitacora"]) ?></p>
        <p><b>Fecha:</b> <?= htmlspecialchars($bit["fecha_evento"]) ?></p>
        <p class="muted" style="margin-top:10px;"><b>Detalle:</b></p>
        <textarea readonly rows="4" style="width:100%;"><?= htmlspecialchars($bit["detalle"]) ?></textarea>
      </div>
    <?php else: ?>
      <p class="muted">No hay registros de bitácora para este ticket.</p>
    <?php endif; ?>
  </div>

  <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
    <?php if ($idRol === 3): ?>
      <a class="btn btn-primary"
         href="editar_ticket.php?id_ticket=<?= (int)$ticket["id_ticket"] ?>&return=<?= urlencode($return) ?>">
        Editar
      </a>
    <?php elseif ($idRol === 2 && (int)($ticket["id_usuario2"] ?? 0) === $idUser): ?>
      <a class="btn btn-primary"
         href="editar_ticket.php?id_ticket=<?= (int)$ticket["id_ticket"] ?>&return=<?= urlencode($return) ?>">
        Editar
      </a>
    <?php endif; ?>

    <a class="btn" href="<?= htmlspecialchars($backUrl) ?>">Volver</a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
