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

//ultma bitacora
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
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ticket #<?= (int)$ticket["id_ticket"] ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Ticket #<?= (int)$ticket["id_ticket"] ?></h1>

<?php if ($flash_ok): ?>
  <p style="color:green;"><?= htmlspecialchars($flash_ok) ?></p>
<?php endif; ?>

<p><b>Creador:</b> <?= htmlspecialchars($ticket["creador"]) ?></p>
<p><b>Categoria:</b> <?= htmlspecialchars($ticket["nombre_categoria"]) ?></p>
<p><b>Prioridad:</b> <?= htmlspecialchars($ticket["prioridad"]) ?></p>
<p><b>Estado:</b> <?= htmlspecialchars($ticket["nombre_estado"]) ?></p>
<p><b>Tecnico asignado:</b> <?= htmlspecialchars($ticket["tecnico_asignado"] ?? "Sin asignar") ?></p>
<p><b>Fecha creacion:</b> <?= htmlspecialchars($ticket["fecha_creacion"]) ?></p>
<p><b>Ultima actualizacion (ticket):</b> <?= htmlspecialchars($ticket["fecha_actualizacion"]) ?></p>

<hr>

<p><b>Descripcion:</b></p>
<textarea rows="6" cols="90" readonly><?= htmlspecialchars($ticket["descripcion"]) ?></textarea>

<hr>


<h2>Ultima actualizacion (bitacora)</h2>

<?php if ($bit): ?>
  <p><b>Accion:</b> <?= htmlspecialchars($bit["accion"]) ?></p>
  <p><b>Por:</b> <?= htmlspecialchars($bit["usuario_bitacora"]) ?></p>
  <p><b>Fecha:</b> <?= htmlspecialchars($bit["fecha_evento"]) ?></p>
  <p><b>Detalle:</b></p>
  <textarea rows="4" cols="90" readonly><?= htmlspecialchars($bit["detalle"]) ?></textarea>
<?php else: ?>
  <p>No hay registros de bitacora para este ticket.</p>
<?php endif; ?>

<hr>

<p>
  <?php if ($idRol === 3): ?>
    <a href="editar_ticket.php?id_ticket=<?= (int)$ticket["id_ticket"] ?>&return=<?= urlencode($return) ?>">Editar</a>
  <?php elseif ($idRol === 2 && (int)($ticket["id_usuario2"] ?? 0) === $idUser): ?>
    <a href="editar_ticket.php?id_ticket=<?= (int)$ticket["id_ticket"] ?>&return=<?= urlencode($return) ?>">Editar</a>
  <?php endif; ?>

  <a href="<?= htmlspecialchars($backUrl) ?>">Volver</a>
</p>

</body>
</html>
