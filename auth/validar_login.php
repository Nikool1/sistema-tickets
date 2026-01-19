<?php
session_start();
require_once("../config/db.php");
$correo   = $_POST["correo"] ?? "";
$password = $_POST["password"] ?? "";
if ($correo === "" || $password === "") {
  header("Location: login.php?error=Completa+los+campos");
  exit;
}
$sql = "
SELECT u.id_usuario, u.nombre_usuario, u.id_rol, r.nombre_rol
FROM usuarios u
JOIN roles r ON r.id_rol = u.id_rol
WHERE u.correo_institucional = ?
  AND u.password = ?
  AND u.activo = 1
LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $correo, $password);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
  $u = $res->fetch_assoc();

  $_SESSION["id_usuario"] = $u["id_usuario"];
  $_SESSION["nombre"]     = $u["nombre_usuario"];
  $_SESSION["nombre_rol"] = $u["nombre_rol"];
  $_SESSION["id_rol"] = $u["id_rol"];

  if ($u["nombre_rol"] === "Administrador") {
    $_SESSION["dashboard"] = "admin.php";
  } elseif ($u["nombre_rol"] === "Tecnico") {
    $_SESSION["dashboard"] = "tecnico.php";
  } else {
    $_SESSION["dashboard"] = "funcionario.php";
  }

  header("Location: ../dashboards/" . $_SESSION["dashboard"]);
  exit;
}
header("Location: login.php?error=Credenciales+incorrectas+o+usuario+inactivo");
exit;
