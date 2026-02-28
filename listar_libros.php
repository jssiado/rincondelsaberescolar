<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT libros.id_libro, libros.titulo, libros.autor, libros.stock,
               categorias.nombre AS categoria
        FROM libros
        INNER JOIN categorias ON libros.id_categoria = categorias.id_categoria
        ORDER BY libros.titulo ASC";

$resultado = $conexion->query($sql);
$total = $resultado ? $resultado->num_rows : 0;

include("../includes/header.php");
?>

<main id="contenido-principal">

  <div class="page-header">
    <h2>📚 Listado de Libros</h2>
    <p>Consulta, edita o elimina los libros del catálogo</p>
  </div>

  <div class="admin-wrapper">

    <div class="prestamos-resumen">
      <span>📊 Total: <strong><?php echo $total; ?> libro<?php echo $total !== 1 ? 's' : ''; ?></strong></span>
      <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <a href="agregar_libro.php" class="btn-devolver">➕ Agregar libro</a>
        <a href="dashboard_admin.php" class="btn-volver">← Volver al panel</a>
      </div>
    </div>

    <?php if ($total === 0): ?>
      <div class="empty-state">
        <span>📭</span>
        <p>No hay libros registrados aún.</p>
        <a href="agregar_libro.php" class="btn-devolver" style="margin-top:1rem;display:inline-block;">➕ Agregar el primero</a>
      </div>
    <?php else: ?>

    <div class="tabla-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>📖 Título</th>
            <th>✍️ Autor</th>
            <th>🏷️ Categoría</th>
            <th>📦 Stock</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($libro = $resultado->fetch_assoc()): ?>
          <tr>
            <td class="td-num"><?php echo $i++; ?></td>
            <td class="td-libro"><em><?php echo htmlspecialchars($libro['titulo']); ?></em></td>
            <td><?php echo htmlspecialchars($libro['autor']); ?></td>
            <td>
              <span class="badge badge-azul"><?php echo htmlspecialchars($libro['categoria']); ?></span>
            </td>
            <td>
              <span class="stock-badge <?php echo $libro['stock'] <= 1 ? 'stock-bajo' : ($libro['stock'] <= 3 ? 'stock-medio' : 'stock-ok'); ?>">
                <?php echo $libro['stock']; ?>
              </span>
            </td>
            <td class="td-acciones">
              <a href="editar_libro.php?id=<?php echo $libro['id_libro']; ?>" class="btn-editar">✏️ Editar</a>
              <a href="eliminar_libro.php?id=<?php echo $libro['id_libro']; ?>"
                 class="btn-eliminar"
                 onclick="return confirm('¿Seguro que deseas eliminar este libro?');">
                🗑️ Eliminar
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <?php endif; ?>

  </div>

</main>

<style>
.admin-wrapper {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem 2rem 4rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.prestamos-resumen {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
  color: var(--texto-suave);
  flex-wrap: wrap;
  gap: 0.75rem;
}

.prestamos-resumen strong { color: var(--marron-oscuro); }

.btn-volver {
  padding: 0.5rem 1.1rem;
  border-radius: 8px;
  border: 1.5px solid var(--marron-claro);
  color: var(--marron);
  background: white;
  font-family: 'Lora', serif;
  font-size: 0.85rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
}
.btn-volver:hover { background: var(--crema); }

.btn-devolver {
  display: inline-block;
  padding: 0.5rem 1.1rem;
  background: var(--dorado);
  color: var(--marron-oscuro);
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.2s, transform 0.2s;
  box-shadow: 0 2px 6px rgba(201,146,42,0.25);
}
.btn-devolver:hover { background: var(--dorado-claro); transform: translateY(-1px); }

/* Tabla */
.tabla-container {
  overflow-x: auto;
  border-radius: 14px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--crema-oscura);
}

table { width: 100%; border-collapse: collapse; background: white; font-size: 0.9rem; }

thead { background: linear-gradient(135deg, var(--marron-oscuro), var(--marron)); color: var(--crema); }

thead th {
  padding: 1rem 1.25rem;
  text-align: left;
  font-family: 'Playfair Display', serif;
  font-weight: 600;
  font-size: 0.88rem;
  letter-spacing: 0.03em;
  white-space: nowrap;
}

tbody tr { border-bottom: 1px solid var(--crema-oscura); transition: background 0.15s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: #fdf8f0; }

tbody td { padding: 0.9rem 1.25rem; vertical-align: middle; color: var(--texto); }

.td-num { color: var(--texto-suave); font-size: 0.8rem; text-align: center; width: 40px; }
.td-libro { color: var(--marron-oscuro); font-style: italic; font-weight: 600; }

/* Badges */
.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 700;
}
.badge-azul { background: #d1ecf1; color: #0c5460; }

/* Stock */
.stock-badge {
  display: inline-block;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  text-align: center;
  line-height: 32px;
  font-weight: 700;
  font-size: 0.88rem;
}
.stock-ok    { background: #d4edda; color: #155724; }
.stock-medio { background: #fff3cd; color: #856404; }
.stock-bajo  { background: #f8d7da; color: #721c24; }

/* Acciones */
.td-acciones { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }

.btn-editar {
  padding: 0.35rem 0.85rem;
  border-radius: 7px;
  background: #e8f4fd;
  color: #0c5460;
  border: 1px solid #bee5eb;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-editar:hover { background: #bee5eb; }

.btn-eliminar {
  padding: 0.35rem 0.85rem;
  border-radius: 7px;
  background: #fdf0ef;
  color: #c0392b;
  border: 1px solid #f5c6cb;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-eliminar:hover { background: #f8d7da; }

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
  .admin-wrapper { padding: 1.25rem 1rem 3rem; }
  thead th, tbody td { padding: 0.75rem 0.75rem; font-size: 0.82rem; }
  .td-acciones { flex-direction: column; gap: 0.35rem; }
}
</style>

<?php include("../includes/footer.php"); ?>