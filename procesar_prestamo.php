<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = (int)$_SESSION['id_usuario'];
$id_libro   = isset($_POST['id_libro']) ? (int)$_POST['id_libro'] : 0;

if ($id_libro === 0) {
    header("Location: prestamo.php?error=libro_invalido");
    exit();
}

// ── Validación 1: máximo 3 préstamos activos ──────────────────────────────
$stmt = $conexion->prepare(
    "SELECT COUNT(*) AS total FROM prestamos
     WHERE id_usuario = ? AND estado = 'prestado'"
);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$total_activos = $stmt->get_result()->fetch_assoc()['total'];

if ($total_activos >= 3) {
    header("Location: prestamo.php?error=limite_prestamos");
    exit();
}

// ── Validación 2: no puede pedir el mismo libro dos veces ─────────────────
$stmt2 = $conexion->prepare(
    "SELECT COUNT(*) AS total FROM prestamos
     WHERE id_usuario = ? AND id_libro = ? AND estado = 'prestado'"
);
$stmt2->bind_param("ii", $id_usuario, $id_libro);
$stmt2->execute();
$ya_tiene = $stmt2->get_result()->fetch_assoc()['total'];

if ($ya_tiene > 0) {
    header("Location: prestamo.php?error=libro_duplicado&id=$id_libro");
    exit();
}

// ── Validación 3: el libro tiene stock disponible ─────────────────────────
$stmt3 = $conexion->prepare(
    "SELECT stock FROM libros WHERE id_libro = ? LIMIT 1"
);
$stmt3->bind_param("i", $id_libro);
$stmt3->execute();
$libro = $stmt3->get_result()->fetch_assoc();

if (!$libro || $libro['stock'] <= 0) {
    header("Location: prestamo.php?error=sin_stock&id=$id_libro");
    exit();
}

// ── Validación 4: fecha de devolución no supera 15 días ──────────────────
$fecha_devolucion_input = isset($_POST['fecha_devolucion']) && $_POST['fecha_devolucion'] !== ''
    ? $_POST['fecha_devolucion']
    : date("Y-m-d", strtotime("+15 days"));

$fecha_max = date("Y-m-d", strtotime("+15 days"));
if ($fecha_devolucion_input > $fecha_max) {
    header("Location: prestamo.php?error=fecha_excedida&id=$id_libro");
    exit();
}

// ── Registrar préstamo ────────────────────────────────────────────────────
$fecha_prestamo   = date("Y-m-d");
$fecha_devolucion = $fecha_devolucion_input;

$stmt4 = $conexion->prepare(
    "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, estado)
     VALUES (?, ?, ?, ?, 'prestado')"
);
$stmt4->bind_param("iiss", $id_usuario, $id_libro, $fecha_prestamo, $fecha_devolucion);

if ($stmt4->execute()) {
    $upd = $conexion->prepare("UPDATE libros SET stock = stock - 1 WHERE id_libro = ?");
    $upd->bind_param("i", $id_libro);
    $upd->execute();

    header("Location: mis_prestamos.php?exito=1");
    exit();
} else {
    header("Location: prestamo.php?error=db");
    exit();
}
?>