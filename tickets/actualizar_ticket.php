<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
  header("Location: ../auth/login.php");
  exit;
}

$idRol  = (int)($_SESSION["id_rol"] ?? 0);
$idUser = (int)$_SESSION["id_usuario"];

if ($idRol !== 2 && $idRol !== 3) {
  header("Location: ../auth/login.php");
  exit;
}

require_once("../config/db.php");

$return = $_POST["return"] ?? "globales";
$return = ($return === "asignados") ? "asignados" : "globales";

$idTicket   = (int)($_POST["id_ticket"] ?? 0);
$prioridad  = trim($_POST["prioridad"] ?? "");
$idEstado   = (int)($_POST["id_estado"] ?? 0);
$detalle    = trim($_POST["detalle"] ?? "");

if ($idTicket <= 0 || $prioridad === "" || $idEstado <= 0 || $detalle === "") {
  header("Location: editar_ticket.php?id_ticket={$idTicket}&return=" . urlencode($return) . "&error=Completa+todos+los+campos");
  exit;
}

$sql = "SELECT id_ticket, prioridad, id_estado, id_usuario2
        FROM tickets
        WHERE id_ticket = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idTicket);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {

  header("Location: tickets_globales.php?error=Ticket+no+existe");
  exit;
}

$ticket = $res->fetch_assoc();

if ($idRol === 2) {
  $asignado = $ticket["id_usuario2"]; 
  if ($asignado === null || (int)$asignado !== $idUser) {
    header("Location: tickets_asignados.php?error=No+tienes+permiso");
    exit;
  }
}

$hoy = date("Y-m-d");

if ($idRol === 3) {
  $idUsuario2 = $_POST["id_usuario2"] ?? "";

  if ($idUsuario2 === "") {
    $sqlUp = "UPDATE tickets
              SET prioridad = ?, id_estado = ?, id_usuario2 = NULL, fecha_actualizacion = ?
              WHERE id_ticket = ?";
    $stmtUp = $conn->prepare($sqlUp);
    $stmtUp->bind_param("sisi", $prioridad, $idEstado, $hoy, $idTicket);
  } else {
    $idUsuario2 = (int)$idUsuario2;
    $sqlUp = "UPDATE tickets
              SET prioridad = ?, id_estado = ?, id_usuario2 = ?, fecha_actualizacion = ?
              WHERE id_ticket = ?";
    $stmtUp = $conn->prepare($sqlUp);
    $stmtUp->bind_param("siisi", $prioridad, $idEstado, $idUsuario2, $hoy, $idTicket);
  }
} else {
  $sqlUp = "UPDATE tickets
            SET prioridad = ?, id_estado = ?, fecha_actualizacion = ?
            WHERE id_ticket = ?";
  $stmtUp = $conn->prepare($sqlUp);
  $stmtUp->bind_param("sisi", $prioridad, $idEstado, $hoy, $idTicket);
}

if (!$stmtUp->execute()) {
  header("Location: editar_ticket.php?id_ticket={$idTicket}&return=" . urlencode($return) . "&error=No+se+pudo+guardar");
  exit;
}

$accion = "Actualizacion";
$detalleFinal = $detalle;

$sqlBit = "INSERT INTO bitacora_ticket (accion, detalle, fecha_evento, id_ticket, id_usuario)
           VALUES (?, ?, ?, ?, ?)";

$stmtB = $conn->prepare($sqlBit);
$stmtB->bind_param("sssii", $accion, $detalleFinal, $hoy, $idTicket, $idUser);
$stmtB->execute();

header("Location: editar_ticket.php?id_ticket={$idTicket}&return=" . urlencode($return) . "&ok=Cambios+guardados");
exit;
