<?php
$host = "localhost";
$user = "usuario_db";
$pass = "password_db";
$db   = "sistema_tickets";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
