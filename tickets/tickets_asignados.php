<?php
session_start();
if (!isset($_SESSION["id_usuario"])) { header("Location: ../auth/login.php"); exit; }

if (!isset($_SESSION["id_rol"]) || !in_array((int)$_SESSION["id_rol"], [2,3], true)) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");
$idUser = (int)$_SESSION["id_usuario"];

$sql = "SELECT t.id_ticket, t.prioridad, t.fecha_creacion, t.id_usuario2,
               c.nombre_categoria, e.nombre_estado,
               u.nombre_usuario AS creador
        FROM tickets t
        JOIN categorias c ON c.id_categoria = t.id_categoria
        JOIN estados e ON e.id_estado = t.id_estado
        JOIN usuarios u ON u.id_usuario = t.id_usuario
        WHERE t.id_usuario2 = ?
        ORDER BY t.id_ticket DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mis Tickets Asignados</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <h1>Mis Tickets Asignados</h1>

  <table border="1" cellpadding="6">
    <tr>
      <th>ID</th><th>Creador</th><th>Categoría</th><th>Estado</th><th>Prioridad</th><th>Acciones</th>
    </tr>

    <?php while ($t = $res->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$t["id_ticket"] ?></td>
        <td><?= htmlspecialchars($t["creador"]) ?></td>
        <td><?= htmlspecialchars($t["nombre_categoria"]) ?></td>
        <td><?= htmlspecialchars($t["nombre_estado"]) ?></td>
        <td><?= htmlspecialchars($t["prioridad"]) ?></td>
        <td>
          <a href="ver_ticket.php?id_ticket=<?= (int)$t["id_ticket"] ?>&return=asignados">Ver</a>
          <a href="editar_ticket.php?id_ticket=<?= (int)$t["id_ticket"] ?>&return=asignados">Editar</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <p>
    <a href="tickets_globales.php">Volver a tickets globales</a> 
    <a href="../dashboards/<?= htmlspecialchars($_SESSION["dashboard"]) ?>">Volver al panel</a>
  </p>
</body>
</html>
