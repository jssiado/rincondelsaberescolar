<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo      = trim($_POST['titulo']);
    $autor       = trim($_POST['autor']);
    $stock       = (int)$_POST['stock'];
    $id_categoria = (int)$_POST['id_categoria'];

    // Verificar si ya existe un libro con el mismo título
    $check = $conexion->prepare("SELECT id_libro FROM libros WHERE titulo = ? LIMIT 1");
    $check->bind_param("s", $titulo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensaje      = "Ya existe un libro con el título \"" . htmlspecialchars($titulo) . "\" en el catálogo.";
        $tipo_mensaje = "error";
    } else {
        $sql  = "INSERT INTO libros (titulo, autor, stock, id_categoria) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssii", $titulo, $autor, $stock, $id_categoria);

        if ($stmt->execute()) {
            $mensaje      = "¡Libro agregado correctamente al catálogo!";
            $tipo_mensaje = "exito";
        } else {
            $mensaje      = "Error al agregar el libro. Inténtalo de nuevo.";
            $tipo_mensaje = "error";
        }
    }
}

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");

include("../includes/header.php");
?>

<main id="contenido-principal">

  <div class="page-header">
    <h2>➕ Agregar Libro</h2>
    <p>Registra un nuevo libro en el catálogo de la biblioteca</p>
  </div>

  <div class="form-page-wrapper">

    <!-- Tarjeta del formulario -->
    <div class="form-card">

      <div class="form-card-header">
        <span class="form-card-icon">📚</span>
        <div>
          <h3>Nuevo libro</h3>
          <p>Completa los datos del libro que deseas agregar</p>
        </div>
      </div>

      <?php if ($mensaje !== ""): ?>
        <div class="alerta alerta-<?php echo $tipo_mensaje === 'exito' ? 'exito' : 'error'; ?>">
          <?php echo $tipo_mensaje === 'exito' ? '✅' : '⚠️'; ?>
          <?php echo htmlspecialchars($mensaje); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="libro-form" novalidate>

        <div class="form-row">
          <div class="form-group">
            <label for="titulo">📖 Título del libro</label>
            <input
              type="text"
              id="titulo"
              name="titulo"
              placeholder="Ej: Cien años de soledad"
              value="<?php echo isset($_POST['titulo']) && $tipo_mensaje !== 'exito' ? htmlspecialchars($_POST['titulo']) : ''; ?>"
              required
            >
          </div>

          <div class="form-group">
            <label for="autor">✍️ Autor</label>
            <input
              type="text"
              id="autor"
              name="autor"
              placeholder="Ej: Gabriel García Márquez"
              value="<?php echo isset($_POST['autor']) && $tipo_mensaje !== 'exito' ? htmlspecialchars($_POST['autor']) : ''; ?>"
              required
            >
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="id_categoria">🏷️ Categoría</label>
            <select id="id_categoria" name="id_categoria" required>
              <option value="" disabled selected>Selecciona una categoría</option>
              <?php while ($cat = $categorias->fetch_assoc()): ?>
                <option value="<?php echo $cat['id_categoria']; ?>"
                  <?php echo (isset($_POST['id_categoria']) && $_POST['id_categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['nombre']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="stock">📦 Stock disponible</label>
            <input
              type="number"
              id="stock"
              name="stock"
              min="1"
              max="999"
              placeholder="Ej: 5"
              value="<?php echo isset($_POST['stock']) && $tipo_mensaje !== 'exito' ? (int)$_POST['stock'] : ''; ?>"
              required
            >
          </div>
        </div>

        <div class="form-actions-row">
          <a href="dashboard_admin.php" class="btn-volver">← Volver al panel</a>
          <button type="submit" class="btn-guardar">💾 Guardar libro</button>
        </div>

      </form>
    </div>

    <!-- Panel lateral informativo -->
    <div class="form-sidebar">
      <div class="sidebar-card">
        <h4>💡 Consejos</h4>
        <ul>
          <li>Verifica la ortografía del título y autor antes de guardar.</li>
          <li>El stock indica cuántos ejemplares están disponibles para préstamo.</li>
          <li>Si la categoría no existe, deberás crearla primero desde la base de datos.</li>
        </ul>
      </div>
      <div class="sidebar-card sidebar-card-dark">
        <h4>📋 Accesos rápidos</h4>
        <a href="listar_libros.php" class="sidebar-link">📚 Ver todos los libros</a>
        <a href="ver_prestamos.php" class="sidebar-link">📋 Ver préstamos</a>
      </div>
    </div>

  </div>

</main>

<style>
.form-page-wrapper {
  max-width: 1000px;
  margin: 2.5rem auto 4rem;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 2rem;
  align-items: start;
}

/* Tarjeta principal */
.form-card {
  background: white;
  border-radius: 16px;
  border: 1px solid var(--crema-oscura);
  box-shadow: var(--shadow-md);
  overflow: hidden;
}

.form-card-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.75rem 2rem;
  background: var(--crema);
  border-bottom: 1px solid var(--crema-oscura);
}

.form-card-icon { font-size: 2.5rem; }

.form-card-header h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.3rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.2rem;
}

.form-card-header p {
  font-size: 0.82rem;
  color: var(--texto-suave);
  font-style: italic;
  margin: 0;
}

/* Alertas */
.alerta {
  margin: 1.25rem 2rem 0;
  padding: 0.9rem 1.1rem;
  border-radius: 8px;
  font-size: 0.88rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.alerta-exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alerta-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

/* Form */
.libro-form { padding: 1.75rem 2rem 2rem; }

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
  margin-bottom: 1.25rem;
}

.form-group { display: flex; flex-direction: column; gap: 0.4rem; }

.form-group label {
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--marron-oscuro);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.form-group input,
.form-group select {
  padding: 0.75rem 1rem;
  border: 1.5px solid var(--crema-oscura);
  border-radius: 8px;
  font-family: 'Lora', serif;
  font-size: 0.93rem;
  color: var(--texto);
  background: var(--crema-suave);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.form-group input:focus,
.form-group select:focus {
  border-color: var(--dorado);
  box-shadow: 0 0 0 3px rgba(201,146,42,0.15);
  background: white;
}

.form-actions-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
  padding-top: 1.25rem;
  border-top: 1px solid var(--crema-oscura);
  gap: 1rem;
  flex-wrap: wrap;
}

.btn-volver {
  padding: 0.7rem 1.25rem;
  border-radius: 8px;
  border: 1.5px solid var(--marron-claro);
  color: var(--marron);
  background: white;
  font-family: 'Lora', serif;
  font-size: 0.88rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}
.btn-volver:hover { background: var(--crema); }

.btn-guardar {
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, var(--dorado), var(--dorado-claro));
  color: var(--marron-oscuro);
  border: none;
  border-radius: 8px;
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(201,146,42,0.3);
  transition: transform 0.2s, box-shadow 0.2s;
}
.btn-guardar:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(201,146,42,0.4);
}

/* Sidebar */
.form-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }

.sidebar-card {
  background: white;
  border-radius: 14px;
  border: 1px solid var(--crema-oscura);
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
}

.sidebar-card h4 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  color: var(--marron-oscuro);
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--crema-oscura);
}

.sidebar-card ul {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

.sidebar-card ul li {
  font-size: 0.82rem;
  color: var(--texto-suave);
  padding-left: 1rem;
  position: relative;
  line-height: 1.5;
}

.sidebar-card ul li::before {
  content: '•';
  position: absolute;
  left: 0;
  color: var(--dorado);
  font-weight: 700;
}

.sidebar-card-dark {
  background: linear-gradient(135deg, var(--marron-oscuro), var(--marron));
}

.sidebar-card-dark h4 { color: var(--dorado-claro); border-color: rgba(255,255,255,0.15); }

.sidebar-link {
  display: block;
  padding: 0.6rem 0.75rem;
  background: rgba(255,255,255,0.08);
  color: var(--crema-oscura);
  border-radius: 8px;
  font-size: 0.85rem;
  text-decoration: none;
  margin-bottom: 0.5rem;
  border: 1px solid rgba(255,255,255,0.1);
  transition: all 0.2s;
}
.sidebar-link:last-child { margin-bottom: 0; }
.sidebar-link:hover { background: rgba(255,255,255,0.15); color: var(--crema); }

@media (max-width: 768px) {
  .form-page-wrapper { grid-template-columns: 1fr; padding: 0 1rem; }
  .form-row { grid-template-columns: 1fr; }
  .libro-form { padding: 1.25rem; }
  .form-card-header { padding: 1.25rem; }
  .form-sidebar { display: none; }
}
</style>

<?php include("../includes/footer.php"); ?>