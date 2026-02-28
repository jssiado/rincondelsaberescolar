<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Estadísticas
$total_libros    = $conexion->query("SELECT COUNT(*) AS total FROM libros")->fetch_assoc()['total'];
$total_usuarios  = $conexion->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()['total'];
$total_prestamos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos")->fetch_assoc()['total'];
$prestamos_activos = $conexion->query("SELECT COUNT(*) AS total FROM prestamos WHERE estado = 'prestado'")->fetch_assoc()['total'];
?>
<?php include("../includes/header.php"); ?>

<main id="contenido-principal">

  <!-- Page Header -->
  <div class="page-header">
    <h2>🛠️ Panel de Administrador</h2>
    <p>Bienvenido de nuevo, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong> — Gestiona tu biblioteca escolar</p>
  </div>

  <div class="admin-wrapper">

    <!-- Estadísticas -->
    <section class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo $total_libros; ?></span>
          <span class="stat-label">Libros en catálogo</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo $total_usuarios; ?></span>
          <span class="stat-label">Usuarios registrados</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo $total_prestamos; ?></span>
          <span class="stat-label">Préstamos totales</span>
        </div>
      </div>
      <div class="stat-card stat-card-highlight">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
          <span class="stat-number"><?php echo $prestamos_activos; ?></span>
          <span class="stat-label">Préstamos activos</span>
        </div>
      </div>
    </section>

    <!-- Acciones -->
    <section>
      <h3 class="admin-section-title">Gestión de Libros</h3>
      <div class="admin-grid">

        <a href="agregar_libro.php" class="admin-card">
          <div class="admin-card-icon">➕</div>
          <div class="admin-card-body">
            <h4>Agregar Libro</h4>
            <p>Registra un nuevo libro en el catálogo de la biblioteca.</p>
          </div>
          <span class="admin-card-arrow">→</span>
        </a>

        <a href="listar_libros.php" class="admin-card">
          <div class="admin-card-icon">📖</div>
          <div class="admin-card-body">
            <h4>Ver Libros</h4>
            <p>Consulta, edita o elimina libros del catálogo.</p>
          </div>
          <span class="admin-card-arrow">→</span>
        </a>

      </div>
    </section>

    <section>
      <h3 class="admin-section-title">Gestión de Préstamos</h3>
      <div class="admin-grid">

        <a href="ver_prestamos.php" class="admin-card">
          <div class="admin-card-icon">📋</div>
          <div class="admin-card-body">
            <h4>Ver Préstamos</h4>
            <p>Revisa todos los préstamos activos y el historial completo.</p>
          </div>
          <span class="admin-card-arrow">→</span>
        </a>

        <a href="devolver_libro.php" class="admin-card">
          <div class="admin-card-icon">↩️</div>
          <div class="admin-card-body">
            <h4>Registrar Devolución</h4>
            <p>Marca libros como devueltos y actualiza el inventario.</p>
          </div>
          <span class="admin-card-arrow">→</span>
        </a>

      </div>
    </section>

    <!-- Botón cerrar sesión -->
    <div class="admin-footer-actions">
      <a href="elegir_vista.php" class="btn-secondary-dark">👤 Cambiar a vista usuario</a>
      <a href="logout.php" class="btn-danger">🚪 Cerrar sesión</a>
    </div>

  </div>

</main>

<style>
.page-header {
  background: linear-gradient(135deg, var(--marron-oscuro), var(--marron));
  color: var(--crema);
  padding: 3rem 3rem 2.5rem;
  margin-bottom: 0;
}

.page-header h2 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(1.6rem, 3vw, 2.2rem);
  color: var(--dorado-claro);
  margin-bottom: 0.4rem;
}

.page-header p { color: var(--crema-oscura); font-style: italic; font-size: 0.95rem; }
.page-header strong { color: var(--crema); font-style: normal; }

.admin-wrapper {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2.5rem 2rem 4rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.25rem;
}

.stat-card {
  background: white;
  border: 1px solid var(--crema-oscura);
  border-radius: 14px;
  padding: 1.5rem 1.75rem;
  display: flex;
  align-items: center;
  gap: 1.25rem;
  box-shadow: var(--shadow-sm);
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
}

.stat-card-highlight {
  background: linear-gradient(135deg, var(--marron-oscuro), var(--marron));
  border-color: var(--marron);
  color: var(--crema);
}

.stat-icon { font-size: 2.2rem; flex-shrink: 0; }

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.stat-number {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  font-weight: 900;
  color: var(--marron-oscuro);
  line-height: 1;
}

.stat-card-highlight .stat-number { color: var(--dorado-claro); }

.stat-label {
  font-size: 0.78rem;
  color: var(--texto-suave);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.stat-card-highlight .stat-label { color: var(--crema-oscura); }

/* Section title */
.admin-section-title {
  font-family: 'Playfair Display', serif;
  font-size: 1.2rem;
  color: var(--marron-oscuro);
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--crema-oscura);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* Action cards */
.admin-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1rem;
}

.admin-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: white;
  border: 1.5px solid var(--crema-oscura);
  border-radius: 12px;
  padding: 1.4rem 1.5rem;
  text-decoration: none;
  color: var(--texto);
  box-shadow: var(--shadow-sm);
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}

.admin-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
  border-color: var(--dorado);
}

.admin-card-icon { font-size: 2rem; flex-shrink: 0; }

.admin-card-body { flex: 1; }

.admin-card-body h4 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.3rem;
}

.admin-card-body p {
  font-size: 0.82rem;
  color: var(--texto-suave);
  line-height: 1.5;
  margin: 0;
}

.admin-card-arrow {
  font-size: 1.2rem;
  color: var(--dorado);
  flex-shrink: 0;
  transition: transform 0.2s;
}

.admin-card:hover .admin-card-arrow { transform: translateX(4px); }

/* Footer actions */
.admin-footer-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  padding-top: 1rem;
  border-top: 1px solid var(--crema-oscura);
}

.btn-secondary-dark {
  padding: 0.7rem 1.5rem;
  border-radius: 8px;
  border: 1.5px solid var(--marron-claro);
  color: var(--marron);
  background: white;
  font-family: 'Lora', serif;
  font-size: 0.9rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-secondary-dark:hover {
  background: var(--crema);
  border-color: var(--marron);
}

.btn-danger {
  padding: 0.7rem 1.5rem;
  border-radius: 8px;
  border: 1.5px solid #e74c3c;
  color: #c0392b;
  background: white;
  font-family: 'Lora', serif;
  font-size: 0.9rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.btn-danger:hover {
  background: #fdf0ef;
  border-color: #c0392b;
}

@media (max-width: 600px) {
  .admin-wrapper { padding: 1.5rem 1rem 3rem; }
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .admin-footer-actions { flex-direction: column; }
}
</style>

<?php include("../includes/footer.php"); ?>