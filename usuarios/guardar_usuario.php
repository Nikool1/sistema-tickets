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

$nombre = $_POST["nombre_usuario"] ?? "";
$rut    = $_POST["rut"] ?? "";
$correo = $_POST["correo_institucional"] ?? "";
$pass   = $_POST["password"] ?? "";
$idRol  = $_POST["id_rol"] ?? "";

if ($nombre === "" || $rut === "" || $correo === "" || $pass === "" || $idRol === "") {
  header("Location: crear_usuario.php?error=Completa+todos+los+campos");
  exit;
}

$activo = 1;
$fecha  = date("Y-m-d");

$sql = "INSERT INTO usuarios (nombre_usuario, rut, correo_institucional, password, activo, fecha_creacion, id_rol)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssisi", $nombre, $rut, $correo, $pass, $activo, $fecha, $idRol);

if ($stmt->execute()) {
  header("Location: crear_usuario.php?ok=Usuario+creado+correctamente");
  exit;
}

if ($conn->errno == 1062) {
  header("Location: crear_usuario.php?error=RUT+o+correo+ya+existe");
  exit;
}

header("Location: crear_usuario.php?error=Error+al+guardar+usuario");
exit;
