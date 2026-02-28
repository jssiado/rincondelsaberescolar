<?php
session_start();
include("../conexion.php");

// Seguridad admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ver_prestamos.php");
    exit();
}

$id_prestamo = $_GET['id'];

// Obtener id_libro
$consulta = $conexion->query(
    "SELECT id_libro FROM prestamos WHERE id_prestamo = $id_prestamo"
);

$datos = $consulta->fetch_assoc();
$id_libro = $datos['id_libro'];

// Cambiar estado
$conexion->query(
    "UPDATE prestamos SET estado='devuelto' WHERE id_prestamo = $id_prestamo"
);

// Devolver stock
$conexion->query(
    "UPDATE libros SET stock = stock + 1 WHERE id_libro = $id_libro"
);

header("Location: ver_prestamos.php");
exit();