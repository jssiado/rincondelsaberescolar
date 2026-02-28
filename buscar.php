<?php
include("../includes/header.php");
include("../conexion.php");

$busqueda    = isset($_GET['q'])   ? trim($_GET['q'])          : "";
$filtro_cat  = isset($_GET['cat']) ? (int)$_GET['cat']         : 0;

$params = [];
$types  = "";

$where = "WHERE (1=1)";

if ($busqueda !== "") {
    $like = "%" . $conexion->real_escape_string($busqueda) . "%";
    $where .= " AND (libros.titulo LIKE ? OR libros.autor LIKE ? OR categorias.nombre LIKE ?)";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= "sss";
}

if ($filtro_cat > 0) {
    $where .= " AND categorias.id_categoria = ?";
    $params[] = $filtro_cat;
    $types .= "i";
}

$sql = "SELECT libros.id_libro, libros.titulo, libros.autor, libros.stock,
               categorias.nombre AS categoria, categorias.id_categoria
        FROM libros
        INNER JOIN categorias ON libros.id_categoria = categorias.id_categoria
        $where ORDER BY libros.titulo ASC";

if (!empty($params)) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexion->query($sql);
}

$total      = $resultado ? $resultado->num_rows : 0;
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
$iconos     = ['📘','📗','📙','📕','📓','📔','📒','📃'];
$i = 0;
?>

<!-- Header de página -->
<div class="page-header">
  <h2>🔍 Buscar en el Catálogo</h2>
  <p>Encuentra el libro que necesitas</p>
</div>

<main id="contenido-principal">
<div class="buscar-wrapper">

  <!-- Barra de búsqueda -->
  <div class="buscar-hero">
    <form method="GET" class="buscar-form-main">
      <div class="buscar-input-wrap">
        <span class="buscar-input-icon">🔍</span>
        <input
          type="text"
          name="q"
          placeholder="Buscar por título, autor o categoría..."
          value="<?php echo htmlspecialchars($busqueda); ?>"
          autocomplete="off"
        >
        <?php if ($filtro_cat > 0): ?>
          <input type="hidden" name="cat" value="<?php echo $filtro_cat; ?>">
        <?php endif; ?>
        <button type="submit" class="btn-buscar">Buscar</button>
      </div>
    </form>

    <!-- Filtros de categoría -->
    <div class="buscar-filtros">
      <a href="buscar.php<?php echo $busqueda ? '?q='.urlencode($busqueda) : ''; ?>"
         class="filtro-chip <?php echo $filtro_cat === 0 ? 'activo' : ''; ?>">Todos</a>
      <?php while ($cat = $categorias->fetch_assoc()): ?>
        <a href="buscar.php?<?php echo $busqueda ? 'q='.urlencode($busqueda).'&' : ''; ?>cat=<?php echo $cat['id_categoria']; ?>"
           class="filtro-chip <?php echo $filtro_cat === (int)$cat['id_categoria'] ? 'activo' : ''; ?>">
          <?php echo htmlspecialchars($cat['nombre']); ?>
        </a>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Resultado -->
  <?php if ($busqueda !== "" || $filtro_cat > 0): ?>
    <p class="buscar-resultado">
      <?php if ($total > 0): ?>
        <strong><?php echo $total; ?></strong> resultado<?php echo $total !== 1 ? 's' : ''; ?> encontrado<?php echo $total !== 1 ? 's' : ''; ?>
        <?php echo $busqueda ? " para <em>\"".htmlspecialchars($busqueda)."\"</em>" : ''; ?>
      <?php else: ?>
        Sin resultados <?php echo $busqueda ? "para <em>\"".htmlspecialchars($busqueda)."\"</em>" : ''; ?>
      <?php endif; ?>
    </p>
  <?php endif; ?>

  <!-- Grid resultados -->
  <?php if ($total === 0 && ($busqueda !== "" || $filtro_cat > 0)): ?>
    <div class="empty-state">
      <span>📭</span>
      <p>No encontramos libros con esa búsqueda.</p>
      <a href="buscar.php" class="btn-primary" style="margin-top:1rem;display:inline-block;">Ver todos</a>
    </div>

  <?php elseif ($total > 0): ?>
    <div class="catalogo-grid">
      <?php while ($libro = $resultado->fetch_assoc()):
        $icono = $iconos[$i % count($iconos)]; $i++;
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

  <?php else: ?>
    <!-- Estado inicial — sin búsqueda -->
    <div class="buscar-inicial">
      <span>📚</span>
      <p>Escribe el título, autor o categoría para encontrar un libro.</p>
    </div>
  <?php endif; ?>

</div>
</main>

<style>
.buscar-wrapper { max-width: 1100px; margin: 0 auto; padding: 2rem 2rem 4rem; }

/* Hero de búsqueda */
.buscar-hero {
  background: linear-gradient(135deg, var(--marron-oscuro), var(--marron));
  border-radius: 16px;
  padding: 2.5rem 2rem;
  margin-bottom: 2rem;
  text-align: center;
}

.buscar-form-main { margin-bottom: 1.25rem; }

.buscar-input-wrap {
  display: flex;
  align-items: center;
  background: white;
  border-radius: 50px;
  overflow: hidden;
  max-width: 580px;
  margin: 0 auto;
  box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}

.buscar-input-icon { padding: 0 0.75rem 0 1.25rem; font-size: 1.1rem; }

.buscar-input-wrap input {
  flex: 1;
  border: none;
  outline: none;
  padding: 0.85rem 0.5rem;
  font-family: 'Lora', serif;
  font-size: 0.93rem;
  color: var(--texto);
  background: transparent;
}

.btn-buscar {
  background: var(--dorado);
  color: var(--marron-oscuro);
  border: none;
  padding: 0.85rem 1.75rem;
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
}
.btn-buscar:hover { background: var(--dorado-claro); }

/* Filtros */
.buscar-filtros {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.filtro-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 1rem;
  border-radius: 50px;
  border: 1.5px solid rgba(255,255,255,0.25);
  background: rgba(255,255,255,0.08);
  color: var(--crema-oscura);
  font-family: 'Lora', serif;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}
.filtro-chip:hover { background: rgba(255,255,255,0.18); color: var(--crema); }
.filtro-chip.activo { background: var(--dorado); border-color: var(--dorado); color: var(--marron-oscuro); }

/* Resultado count */
.buscar-resultado {
  font-size: 0.88rem;
  color: var(--texto-suave);
  font-style: italic;
  margin-bottom: 1.5rem;
}

/* Grid (mismo que catálogo) */
.catalogo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem; }

.libro-card-full { background: white; border: 1.5px solid var(--crema-oscura); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s; }
.libro-card-full:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
.libro-agotado { opacity: 0.6; }

.libro-card-top { background: var(--crema); padding: 1.5rem 1.25rem 1rem; display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--crema-oscura); }
.libro-card-icono { font-size: 2.8rem; }
.libro-cat-badge { background: var(--marron-oscuro); color: var(--dorado-claro); font-size: 0.68rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap; }

.libro-card-body { padding: 1.1rem 1.25rem; flex: 1; }
.libro-card-body h3 { font-family: 'Playfair Display', serif; font-size: 0.98rem; color: var(--marron-oscuro); margin-bottom: 0.4rem; line-height: 1.35; }
.libro-autor { font-size: 0.8rem; color: var(--texto-suave); font-style: italic; margin: 0; }

.libro-card-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--crema-oscura); display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.stock-disponible { font-size: 0.78rem; font-weight: 700; color: #155724; }
.stock-agotado { font-size: 0.78rem; font-weight: 700; color: #721c24; }

.btn-pedir { padding: 0.35rem 0.85rem; background: var(--dorado); color: var(--marron-oscuro); border-radius: 7px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: background 0.2s; white-space: nowrap; }
.btn-pedir:hover { background: var(--dorado-claro); }
.btn-pedir-outline { padding: 0.35rem 0.85rem; background: transparent; color: var(--marron-claro); border-radius: 7px; border: 1px solid var(--marron-claro); font-size: 0.78rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-pedir-outline:hover { background: var(--crema); }

/* Estado inicial */
.buscar-inicial { text-align: center; padding: 3rem 2rem; color: var(--texto-suave); }
.buscar-inicial span { font-size: 3rem; display: block; margin-bottom: 1rem; }
.buscar-inicial p { font-style: italic; }

.empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 14px; border: 1px dashed var(--crema-oscura); color: var(--texto-suave); }
.empty-state span { font-size: 3rem; display: block; margin-bottom: 1rem; }
.empty-state p { font-style: italic; }

@media (max-width: 600px) {
  .buscar-wrapper { padding: 1.25rem 1rem 3rem; }
  .catalogo-grid { grid-template-columns: 1fr 1fr; }
  .buscar-input-wrap { border-radius: 12px; }
}
</style>

<?php include("../includes/footer.php"); ?>