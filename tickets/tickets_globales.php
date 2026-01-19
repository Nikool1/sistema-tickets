<?php
session_start();
if (!isset($_SESSION["id_usuario"])) { header("Location: ../auth/login.php"); exit; }
if (!isset($_SESSION["id_rol"]) || ($_SESSION["id_rol"] != 2 && $_SESSION["id_rol"] != 3)) {
  header("Location: ../auth/login.php"); exit;
}
require_once("../config/db.php");

$idRol  = (int)$_SESSION["id_rol"];
$idUser = (int)$_SESSION["id_usuario"];

$sql = "SELECT t.id_ticket, t.prioridad, t.fecha_creacion, t.id_usuario2,
               c.nombre_categoria, e.nombre_estado,
               u.nombre_usuario AS creador,
               ut.nombre_usuario AS tecnico
        FROM tickets t
        JOIN categorias c ON c.id_categoria = t.id_categoria
        JOIN estados e ON e.id_estado = t.id_estado
        JOIN usuarios u ON u.id_usuario = t.id_usuario
        LEFT JOIN usuarios ut ON ut.id_usuario = t.id_usuario2
        ORDER BY t.id_ticket DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tickets Globales</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <h1>Tickets Globales</h1>

  <table border="1" cellpadding="6">
    <tr>
      <th>ID</th><th>Creador</th><th>Categoría</th><th>Estado</th><th>Prioridad</th><th>Técnico</th><th>Acciones</th>
    </tr>

    <?php while ($t = $res->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$t["id_ticket"] ?></td>
        <td><?= htmlspecialchars($t["creador"]) ?></td>
        <td><?= htmlspecialchars($t["nombre_categoria"]) ?></td>
        <td><?= htmlspecialchars($t["nombre_estado"]) ?></td>
        <td><?= htmlspecialchars($t["prioridad"]) ?></td>
        <td><?= htmlspecialchars($t["tecnico"] ?? "Sin asignar") ?></td>
        <td>
          <a href="ver_ticket.php?id_ticket=<?= (int)$t["id_ticket"] ?>">Ver</a>
          <?php if ($idRol === 3): ?>
          <a href="editar_ticket.php?id_ticket=<?= (int)$t['id_ticket'] ?>&return=globales">Editar</a>
          <?php endif; ?>

        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <p>
    <?php if ($idRol === 2): ?>
      <a href="tickets_asignados.php">Ver mis tickets asignados</a> 
    <?php endif; ?>
    <a href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">Volver al panel</a>
  </p>
</body>
</html>
