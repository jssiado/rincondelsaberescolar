<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rincón del Saber Escolar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Lora:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS principal -->
    <link rel="stylesheet" href="/biblioteca/assets/css/style.css">
</head>

<body>

<a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

<header>
    <nav role="navigation" aria-label="Menú principal">

        <a class="nav-logo" href="/biblioteca/pages/index.php">
            <span class="icono">📚</span>
            <div class="nav-logo-texto">
                <h1>Rincón del Saber Escolar</h1>
                <span>Biblioteca Escolar</span>
            </div>
        </a>

        <button class="nav-toggle" aria-label="Abrir menú">
            <i class="fa-solid fa-bars"></i>
        </button>

        <ul class="nav-links" id="nav-menu">
            <li><a href="/biblioteca/pages/index.php">Inicio</a></li>
            <li><a href="/biblioteca/pages/catalogo.php">Catálogo</a></li>

            <?php if (isset($_SESSION['id_usuario'])) { ?>
                <li><a href="/biblioteca/pages/mis_prestamos.php">Mis préstamos</a></li>
            <?php } ?>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') { ?>
                <li><a href="/biblioteca/pages/dashboard_admin.php">🛠️ Panel Admin</a></li>
            <?php } ?>

            <?php if (!isset($_SESSION['id_usuario'])) { ?>
                <li><a href="/biblioteca/pages/login.php">Iniciar sesión</a></li>
            <?php } else { ?>
                <li><a href="/biblioteca/pages/logout.php">Cerrar sesión</a></li>
            <?php } ?>
        </ul>
    </nav>
</header>