<?php
include("../includes/header.php");
include("../conexion.php");

// Filtro por categoría
$filtro_cat = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;

if ($filtro_cat > 0) {
    $sql = "SELECT libros.id_libro, libros.titulo, libros.autor, libros.stock,
                   categorias.nombre AS categoria, categorias.id_categoria
            FROM libros
            INNER JOIN categorias ON libros.id_categoria = categorias.id_categoria
            WHERE categorias.id_categoria = ?
            ORDER BY libros.titulo ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $filtro_cat);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $sql = "SELECT libros.id_libro, libros.titulo, libros.autor, libros.stock,
                   categorias.nombre AS categoria, categorias.id_categoria
            FROM libros
            INNER JOIN categorias ON libros.id_categoria = categorias.id_categoria
            ORDER BY libros.titulo ASC";
    $resultado = $conexion->query($sql);
}

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
$total = $resultado ? $resultado->num_rows : 0;

$iconos = ['📘','📗','📙','📕','📓','📔','📒','📃'];
$i = 0;
?>

<!-- Page Header -->
<div class="page-header">
  <h2>📚 Catálogo de Libros</h2>
  <p>Explora nuestra colección completa de libros disponibles</p>
</div>

<main id="contenido-principal">
  <div class="catalogo-wrapper">

    <!-- Filtros por categoría -->
    <div class="catalogo-filtros">
      <a href="catalogo.php" class="filtro-chip <?php echo $filtro_cat === 0 ? 'activo' : ''; ?>">
        Todos <span class="filtro-count"><?php echo $total; ?></span>
      </a>
      <?php
      $categorias->data_seek(0);
      while ($cat = $categorias->fetch_assoc()):
        $count = $conexion->query("SELECT COUNT(*) AS c FROM libros WHERE id_categoria = {$cat['id_categoria']}")->fetch_assoc()['c'];
      ?>
        <a href="catalogo.php?categoria=<?php echo $cat['id_categoria']; ?>"
           class="filtro-chip <?php echo $filtro_cat === (int)$cat['id_categoria'] ? 'activo' : ''; ?>">
          <?php echo htmlspecialchars($cat['nombre']); ?>
          <span class="filtro-count"><?php echo $count; ?></span>
        </a>
      <?php endwhile; ?>
    </div>

    <!-- Resultado -->
    <p class="catalogo-resultado">
      <?php echo $total; ?> libro<?php echo $total !== 1 ? 's' : ''; ?> encontrado<?php echo $total !== 1 ? 's' : ''; ?>
    </p>

    <?php if ($total === 0): ?>
      <div class="empty-state">
        <span>📭</span>
        <p>No hay libros en esta categoría.</p>
        <a href="catalogo.php" class="btn-primary" style="margin-top:1rem;display:inline-block;">Ver todos</a>
      </div>
    <?php else: ?>

    <!-- Grid de libros -->
    <div class="catalogo-grid">
      <?php while ($libro = $resultado->fetch_assoc()):
        $icono = $iconos[$i % count($iconos)];
        $i++;
        $disponible = $libro['stock'] > 0;
      ?>
      <div class="libro-card-full <?php echo !$disponible ? 'libro-agotado' : ''; ?>">

        <div class="libro-card-top">
          <span class="libro-card-icono"><?php echo $icono; ?></span>
          <span class="libro-cat-badge"><?php echo htmlspecialchars($libro['categoria']); ?></span>
        </div>

        <div class="libro-card-body">
          <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
          <p class="libro-autor">✍️ <?php echo htmlspecialchars($libro['autor']); ?></p>
        </div>

        <div class="libro-card-footer">
          <?php if ($disponible): ?>
            <span class="stock-disponible">✅ Disponible (<?php echo $libro['stock']; ?>)</span>
            <?php if (isset($_SESSION['id_usuario'])): ?>
              <a href="prestamo.php?id=<?php echo $libro['id_libro']; ?>" class="btn-pedir">📥 Solicitar</a>
            <?php else: ?>
              <a href="login.php" class="btn-pedir-outline">Inicia sesión</a>
            <?php endif; ?>
          <?php else: ?>
            <span class="stock-agotado">❌ No disponible</span>
          <?php endif; ?>
        </div>

      </div>
      <?php endwhile; ?>
    </div>

    <?php endif; ?>

  </div>
</main>

<style>
.catalogo-wrapper {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem 2rem 4rem;
}

/* Filtros */
.catalogo-filtros {
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
  margin-bottom: 1.25rem;
}

.filtro-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 1rem;
  border-radius: 50px;
  border: 1.5px solid var(--crema-oscura);
  background: white;
  color: var(--texto-suave);
  font-family: 'Lora', serif;
  font-size: 0.82rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}

.filtro-chip:hover {
  border-color: var(--dorado);
  color: var(--marron);
}

.filtro-chip.activo {
  background: var(--marron-oscuro);
  border-color: var(--marron-oscuro);
  color: var(--dorado-claro);
}

.filtro-count {
  background: rgba(0,0,0,0.08);
  border-radius: 50px;
  padding: 0.1rem 0.45rem;
  font-size: 0.72rem;
}

.filtro-chip.activo .filtro-count {
  background: rgba(255,255,255,0.15);
  color: var(--crema);
}

.catalogo-resultado {
  font-size: 0.85rem;
  color: var(--texto-suave);
  font-style: italic;
  margin-bottom: 1.5rem;
}

/* Grid */
.catalogo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.5rem;
}

.libro-card-full {
  background: white;
  border: 1.5px solid var(--crema-oscura);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, box-shadow 0.2s;
}

.libro-card-full:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.libro-agotado { opacity: 0.6; }

.libro-card-top {
  background: var(--crema);
  padding: 1.5rem 1.25rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  border-bottom: 1px solid var(--crema-oscura);
}

.libro-card-icono { font-size: 2.8rem; }

.libro-cat-badge {
  background: var(--marron-oscuro);
  color: var(--dorado-claro);
  font-size: 0.68rem;
  font-weight: 700;
  padding: 0.25rem 0.65rem;
  border-radius: 50px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  white-space: nowrap;
}

.libro-card-body {
  padding: 1.1rem 1.25rem;
  flex: 1;
}

.libro-card-body h3 {
  font-family: 'Playfair Display', serif;
  font-size: 0.98rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.4rem;
  line-height: 1.35;
}

.libro-autor {
  font-size: 0.8rem;
  color: var(--texto-suave);
  font-style: italic;
  margin: 0;
}

.libro-card-footer {
  padding: 1rem 1.25rem;
  border-top: 1px solid var(--crema-oscura);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.stock-disponible {
  font-size: 0.78rem;
  font-weight: 700;
  color: #155724;
}

.stock-agotado {
  font-size: 0.78rem;
  font-weight: 700;
  color: #721c24;
}

.btn-pedir {
  padding: 0.35rem 0.85rem;
  background: var(--dorado);
  color: var(--marron-oscuro);
  border-radius: 7px;
  font-size: 0.78rem;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.2s;
  white-space: nowrap;
}
.btn-pedir:hover { background: var(--dorado-claro); }

.btn-pedir-outline {
  padding: 0.35rem 0.85rem;
  background: transparent;
  color: var(--marron-claro);
  border-radius: 7px;
  border: 1px solid var(--marron-claro);
  font-size: 0.78rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-pedir-outline:hover { background: var(--crema); }

/* Empty */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 14px;
  border: 1px dashed var(--crema-oscura);
  color: var(--texto-suave);
}
.empty-state span { font-size: 3rem; display: block; margin-bottom: 1rem; }
.empty-state p { font-style: italic; }

@media (max-width: 600px) {
  .catalogo-wrapper { padding: 1.25rem 1rem 3rem; }
  .catalogo-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 400px) {
  .catalogo-grid { grid-template-columns: 1fr; }
}
</style>

<?php include("../includes/footer.php"); ?>