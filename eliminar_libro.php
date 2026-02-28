<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conexion->query("DELETE FROM libros WHERE id_libro = $id");
}

header("Location: listar_libros.php");
exit();