<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

$page_title = "Mis Tickets";
require_once __DIR__ . '/../includes/header.php';

$idUsuario = intval($_SESSION["id_usuario"]);

$sql = "
  SELECT
    t.id_ticket,
    t.prioridad,
    t.fecha_creacion,
    t.fecha_actualizacion,
    t.descripcion,
    c.nombre_categoria,
    e.nombre_estado
  FROM tickets t
  JOIN categorias c ON c.id_categoria = t.id_categoria
  JOIN estados e ON e.id_estado = t.id_estado
  WHERE t.id_usuario = ?
  ORDER BY t.id_ticket DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

$tickets = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
  }
}
?>

<div class="card">
  <h1 class="title">Mis Tickets</h1>
  <p class="subtitle">Listado de tus tickets creados</p>

  <?php if (count($tickets) === 0): ?>
    <p class="muted">No tienes tickets registrados.</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Creación</th>
            <th>Actualización</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($tickets as $t): ?>
            <tr>
              <td><?= (int)$t["id_ticket"] ?></td>
              <td><?= htmlspecialchars($t["nombre_categoria"]) ?></td>
              <td><?= htmlspecialchars($t["nombre_estado"]) ?></td>
              <td><?= htmlspecialchars($t["prioridad"]) ?></td>
              <td><?= htmlspecialchars($t["fecha_creacion"]) ?></td>
              <td><?= htmlspecialchars($t["fecha_actualizacion"]) ?></td>
              <td><?= htmlspecialchars($t["descripcion"]) ?></td>
              <td>
                <a class="btn btn-primary" href="ver_ticket.php?id_ticket=<?= (int)$t["id_ticket"] ?>&return=mis">
                  Ver
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div style="margin-top:14px;">
    <a class="btn" href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">Volver al panel</a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
