<?php
require_once("../includes/auth.php");

require_role("Funcionario");

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
    <title>Panel Funcionario</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h1>Panel de Perfil Funcionario</h1>

<p>
    Bienvenido,
    <strong><?= htmlspecialchars($_SESSION["nombre"]) ?></strong>
</p>

<p>
    Rol detectado por el sistema:
    <strong><?= htmlspecialchars($_SESSION["nombre_rol"]) ?></strong>
</p>

<p>
    <a href="../tickets/crear_ticket.php">Crear Ticket</a>
    <a href="../tickets/listar_tickets.php">Tickets Creados</a>

</p>

<a href="../auth/logout.php">Cerrar sesion</a>

</body>
</html>
