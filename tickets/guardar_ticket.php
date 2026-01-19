<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

$descripcion  = $_POST["descripcion"] ?? "";
$idCategoria  = $_POST["id_categoria"] ?? "";

if ($descripcion === "" || $idCategoria === "") {
  header("Location: crear_ticket.php?error=Completa+todos+los+campos");
  exit;
}

$idUsuario = $_SESSION["id_usuario"];
$prioridad = "En Proceso"; 
$hoy = date("Y-m-d");

$idEstado = 1; 

$idTecnicoAsignado = null;

$sql = "INSERT INTO tickets
  (descripcion, prioridad, fecha_creacion, fecha_actualizacion, id_usuario, id_categoria, id_usuario2, id_estado)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "ssssiiii",
  $descripcion,
  $prioridad,
  $hoy,
  $hoy,
  $idUsuario,
  $idCategoria,
  $idTecnicoAsignado, 
  $idEstado
);

if ($stmt->execute()) {

  $idTicket = $conn->insert_id;


  $accion  = "Creacion";
  $detalle = "Ticket creado por el usuario";

  $sqlBit = "INSERT INTO bitacora_ticket (accion, detalle, fecha_evento, id_ticket, id_usuario)
             VALUES (?, ?, ?, ?, ?)";

  $stmtBit = $conn->prepare($sqlBit);
  $stmtBit->bind_param("sssii", $accion, $detalle, $hoy, $idTicket, $idUsuario);
  $stmtBit->execute();


  $_SESSION["flash_ok"] = "Ticket creado correctamente";
  header("Location: ../dashboards/" . $_SESSION["dashboard"]);
  exit;
}

header("Location: crear_ticket.php?error=No+se+pudo+crear+el+ticket");
exit;
