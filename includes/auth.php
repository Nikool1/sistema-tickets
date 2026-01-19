<?php
session_start();

function require_login() {
  if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../auth/login.php");
    exit;
  }
}

//requiere rol por nombre
function require_role($nombre_rol) {
    require_login();
  if (!isset($_SESSION["nombre_rol"]) || $_SESSION["nombre_rol"] !== $nombre_rol) {
    header("Location: ../auth/login.php");
    exit;
  }
}

//requiere rol por id
function require_role_id($idRol) {
  require_login();
  if (!isset($_SESSION["id_rol"]) || $_SESSION["id_rol"] != $idRol) {
    header("Location: ../auth/login.php");
    exit;
  }
}
