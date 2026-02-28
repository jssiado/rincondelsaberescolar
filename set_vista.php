<?php
session_start();

// Seguridad básica
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validar vista
if (!isset($_POST['vista']) || !in_array($_POST['vista'], ['admin', 'usuario'])) {
    header("Location: elegir_vista.php");
    exit();
}

// Guardar vista
$_SESSION['vista'] = $_POST['vista'];

// Redirigir según vista
if ($_POST['vista'] === 'admin') {
    header("Location: dashboard_admin.php");
} else {
    header("Location: index.php");
}
exit();