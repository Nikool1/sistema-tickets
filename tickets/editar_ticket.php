<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}

$idRol  = (int)($_SESSION["id_rol"] ?? 0);
$idUser = (int)$_SESSION["id_usuario"];

//solo admin(3) y tecnico(2)
if ($idRol !== 2 && $idRol !== 3) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

$idTicket = (int)($_GET["id_ticket"] ?? 0);
if ($idTicket <= 0) {
  header("Location: tickets_globales.php?error=Ticket+invalido");
  exit;
}

$return = $_GET["return"] ?? "globales";

$backUrl = ($return === "asignados")
  ? "tickets_asignados.php"
  : "tickets_globales.php";


$sql = "SELECT t.id_ticket, t.descripcion, t.prioridad, t.fecha_creacion, t.fecha_actualizacion,
               t.id_usuario, t.id_usuario2, t.id_categoria, t.id_estado,
               c.nombre_categoria,
               e.nombre_estado,
               u.nombre_usuario AS creador,
               ut.nombre_usuario AS tecnico_asignado
        FROM tickets t
        JOIN categorias c ON c.id_categoria = t.id_categoria
        JOIN estados e ON e.id_estado = t.id_estado
        JOIN usuarios u ON u.id_usuario = t.id_usuario
        LEFT JOIN usuarios ut ON ut.id_usuario = t.id_usuario2
        WHERE t.id_ticket = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idTicket);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
  header("Location: tickets_globales.php?error=Ticket+no+existe");
  exit;
}

$ticket = $res->fetch_assoc();

//solo si es tecnico
if ($idRol === 2) {
  $asignado = $ticket["id_usuario2"];
  if ($asignado === null || (int)$asignado !== $idUser) {
    header("Location: tickets_asignados.php?error=No+tienes+permiso+para+editar+este+ticket");
    exit;
  }
}

$estados = [];
$rEstados = $conn->query("SELECT id_estado, nombre_estado FROM estados ORDER BY id_estado ASC");
while ($row = $rEstados->fetch_assoc()) $estados[] = $row;

$prioridades = ["En Proceso", "Baja", "Media", "Alta"];

//solo para admin, asign tecnico
$tecnicos = [];
if ($idRol === 3) {
  $sqlTec = "SELECT id_usuario, nombre_usuario
             FROM usuarios
             WHERE id_rol IN (2,3) AND activo = 1
             ORDER BY nombre_usuario ASC";
  $rTec = $conn->query($sqlTec);
  while ($row = $rTec->fetch_assoc()) $tecnicos[] = $row;
}

$error = $_GET["error"] ?? "";
$ok    = $_GET["ok"] ?? "";
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar Ticket #<?= (int)$ticket["id_ticket"] ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Editar Ticket #<?= (int)$ticket["id_ticket"] ?></h1>

<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($ok): ?>
  <p style="color:green;"><?= htmlspecialchars($ok) ?></p>
<?php endif; ?>

<!-- aatos del ticket-->
<p><b>Creador:</b> <?= htmlspecialchars($ticket["creador"]) ?></p>
<p><b>Categoria:</b> <?= htmlspecialchars($ticket["nombre_categoria"]) ?></p>
<p><b>Estado actual:</b> <?= htmlspecialchars($ticket["nombre_estado"]) ?></p>
<p><b>Tecnico asignado:</b> <?= htmlspecialchars($ticket["tecnico_asignado"] ?? "Sin asignar") ?></p>

<p><b>Descripcion:</b><br>
<textarea rows="6" cols="80" readonly><?= htmlspecialchars($ticket["descripcion"]) ?></textarea></p>

<hr>

<form method="post" action="actualizar_ticket.php">
  <input type="hidden" name="id_ticket" value="<?= (int)$ticket["id_ticket"] ?>">
  <input type="hidden" name="return" value="<?= htmlspecialchars($return) ?>">

  <label>Prioridad</label><br>
  <select name="prioridad" required>
    <?php foreach ($prioridades as $p): ?>
      <option value="<?= htmlspecialchars($p) ?>" <?= ($ticket["prioridad"] === $p ? "selected" : "") ?>>
        <?= htmlspecialchars($p) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <br><br>

  <label>Estado</label><br>
  <select name="id_estado" required>
    <?php foreach ($estados as $e): ?>
      <option value="<?= (int)$e["id_estado"] ?>" <?= ((int)$ticket["id_estado"] === (int)$e["id_estado"] ? "selected" : "") ?>>
        <?= htmlspecialchars($e["nombre_estado"]) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <br><br>

  <!--solo admin ve esto-->
  <?php if ($idRol === 3): ?>
    <label>Asignar tecnico</label><br>
    <select name="id_usuario2">
      <option value="">-- Sin asignar --</option>
      <?php foreach ($tecnicos as $t): ?>
        <option value="<?= (int)$t["id_usuario"] ?>" <?= ((int)($ticket["id_usuario2"] ?? 0) === (int)$t["id_usuario"] ? "selected" : "") ?>>
          <?= htmlspecialchars($t["nombre_usuario"]) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br><br>
  <?php endif; ?>

  <label>Detalle / comentario (bitacora)</label><br>
  <textarea name="detalle" rows="4" cols="80" required></textarea>
  <br><br>

  <button type="submit">Guardar cambios</button>
</form>

<p>
  <a href="ver_ticket.php?id_ticket=<?= $idTicket ?>&return=<?= urlencode($return) ?>">Ver ticket</a>
  <a href="<?= $backUrl ?>">Volver</a>

</p>

</body>
</html>
