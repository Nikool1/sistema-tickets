<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Tickets</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Mis Tickets</h1>

<?php if (count($tickets) === 0): ?>
  <p>No tienes tickets registrados.</p>
<?php else: ?>
  <table border="1" cellpadding="8" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Categoria</th>
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
            <a href="ver_ticket.php?id_ticket=<?= (int)$t["id_ticket"] ?>&return=mis">
              Ver
            </a>
          </td>
        </tr>

      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<p><a href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">Volver al panel</a></p>

</body>
</html>
