<?php
session_start();
include("../conexion.php");

// Seguridad: solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Verificar ID
if (!isset($_GET['id'])) {
    header("Location: listar_libros.php");
    exit();
}

$id = $_GET['id'];

// Obtener datos del libro
$libro_sql = "SELECT * FROM libros WHERE id_libro = $id";
$libro_resultado = $conexion->query($libro_sql);
$libro = $libro_resultado->fetch_assoc();

// Obtener categorías
$categorias = $conexion->query("SELECT * FROM categorias");

// Actualizar libro
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    $update = "UPDATE libros SET
                titulo='$titulo',
                autor='$autor',
                stock='$stock',
                id_categoria='$id_categoria'
               WHERE id_libro=$id";

    $conexion->query($update);
    header("Location: listar_libros.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Libro</title>
</head>
<body>

<h2>Editar Libro</h2>

<form method="POST">
    <label>Título:</label><br>
    <input type="text" name="titulo" value="<?php echo $libro['titulo']; ?>" required><br><br>

    <label>Autor:</label><br>
    <input type="text" name="autor" value="<?php echo $libro['autor']; ?>" required><br><br>

    <label>Categoría:</label><br>
    <select name="id_categoria" required>
        <?php while ($cat = $categorias->fetch_assoc()) { ?>
            <option value="<?php echo $cat['id_categoria']; ?>"
                <?php if ($cat['id_categoria'] == $libro['id_categoria']) echo "selected"; ?>>
                <?php echo $cat['nombre']; ?>
            </option>
        <?php } ?>
    </select><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?php echo $libro['stock']; ?>" min="0" required><br><br>

    <button type="submit">Guardar cambios</button>
</form>

<br>
<a href="listar_libros.php">Cancelar</a>

</body>
</html>