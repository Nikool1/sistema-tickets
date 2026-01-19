<?php
require_once("../includes/auth.php");

require_role("Tecnico");

$flash_ok = $_SESSION["flash_ok"] ?? "";
unset($_SESSION["flash_ok"]);
?>

<?php if ($flash_ok): ?>
  <p style="color:green;"><?= htmlspecialchars($flash_ok) ?></p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Tecnico</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Panel de Perfil Tecnico</h1>

<p>
    Bienvenido,
    <strong><?= htmlspecialchars($_SESSION["nombre"]) ?></strong>
</p>

<p>
    Rol detectado por el sistema:
    <strong><?= htmlspecialchars($_SESSION["nombre_rol"]) ?></strong>
</p>

<p>
    <a href="../usuarios/crear_usuario.php">Crear Usuario</a>
    <a href="../tickets/crear_ticket.php">Crear Ticket</a>
    <a href="../tickets/listar_tickets.php">Tickets Creados</a>
    <a href="../tickets/tickets_globales.php">Tickets Globales</a>
    <a href="../tickets/tickets_asignados.php">Tickets Asignados</a>

</p>

<a href="../auth/logout.php">Cerrar sesion</a>

</body>
</html>
