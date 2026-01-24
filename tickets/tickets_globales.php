<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}
if (!isset($_SESSION["id_rol"]) || ($_SESSION["id_rol"] != 2 && $_SESSION["id_rol"] != 3)) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

$idRol  = (int)$_SESSION["id_rol"];
$idUser = (int)$_SESSION["id_usuario"];

$page_title = "Tickets Globales";
require_once __DIR__ . '/../includes/header.php';

$sql = "
  SELECT
    t.id_ticket,
    t.prioridad,
    t.fecha_creacion,
    t.id_usuario2,
    c.nombre_categoria,
    e.nombre_estado,
    u.nombre_usuario AS creador,
    ut.nombre_usuario AS tecnico
  FROM tickets t
  JOIN categorias c ON c.id_categoria = t.id_categoria
  JOIN estados e ON e.id_estado = t.id_estado
  JOIN usuarios u ON u.id_usuario = t.id_usuario
  LEFT JOIN usuarios ut ON ut.id_usuario = t.id_usuario2
  ORDER BY t.id_ticket DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="card">
  <h1 class="title">Tickets Globales</h1>
  <p class="subtitle">Listado general de tickets del sistema</p>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Creador</th>
          <th>Categoría</th>
          <th>Estado</th>
          <th>Prioridad</th>
          <th>Técnico</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($t = $res->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$t["id_ticket"] ?></td>
            <td><?= htmlspecialchars($t["creador"]) ?></td>
            <td><?= htmlspecialchars($t["nombre_categoria"]) ?></td>
            <td><?= htmlspecialchars($t["nombre_estado"]) ?></td>
            <td><?= htmlspecialchars($t["prioridad"]) ?></td>
            <td><?= htmlspecialchars($t["tecnico"] ?? "Sin asignar") ?></td>
            <td style="display:flex; gap:8px; flex-wrap:wrap;">
              <a class="btn btn-primary"
                 href="ver_ticket.php?id_ticket=<?= (int)$t["id_ticket"] ?>">
                Ver
              </a>

              <?php if ($idRol === 3): ?>
                <a class="btn"
                   href="editar_ticket.php?id_ticket=<?= (int)$t['id_ticket'] ?>&return=globales">
                  Editar
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
    <?php if ($idRol === 2): ?>
      <a class="btn" href="tickets_asignados.php">Ver mis tickets asignados</a>
    <?php endif; ?>

    <a class="btn" href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">
      Volver al panel
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
