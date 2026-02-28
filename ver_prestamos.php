<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT p.id_prestamo, u.nombre AS usuario, l.titulo AS libro,
               p.fecha_prestamo, p.fecha_devolucion, p.estado
        FROM prestamos p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        INNER JOIN libros l ON p.id_libro = l.id_libro
        ORDER BY p.fecha_prestamo DESC";

$resultado = $conexion->query($sql);
$total = $resultado->num_rows;

include("../includes/header.php");
?>

<main id="contenido-principal">

  <div class="page-header">
    <h2>📋 Préstamos registrados</h2>
    <p>Gestiona y revisa todos los préstamos de la biblioteca</p>
  </div>

  <div class="admin-wrapper">

    <!-- Resumen rápido -->
    <div class="prestamos-resumen">
      <span>📊 Total de registros: <strong><?php echo $total; ?></strong></span>
      <a href="dashboard_admin.php" class="btn-volver">← Volver al panel</a>
    </div>

    <?php if ($total === 0): ?>
      <div class="empty-state">
        <span>📭</span>
        <p>No hay préstamos registrados aún.</p>
      </div>
    <?php else: ?>

    <div class="tabla-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>👤 Usuario</th>
            <th>📚 Libro</th>
            <th>📅 Fecha préstamo</th>
            <th>🔔 Fecha devolución</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($p = $resultado->fetch_assoc()): ?>
          <tr>
            <td class="td-num"><?php echo $i++; ?></td>
            <td>
              <div class="td-usuario">
                <span class="usuario-avatar"><?php echo strtoupper(substr($p['usuario'], 0, 1)); ?></span>
                <?php echo htmlspecialchars($p['usuario']); ?>
              </div>
            </td>
            <td class="td-libro"><em><?php echo htmlspecialchars($p['libro']); ?></em></td>
            <td><?php echo date('d/m/Y', strtotime($p['fecha_prestamo'])); ?></td>
            <td>
              <?php
                $hoy = new DateTime();
                $devolucion = new DateTime($p['fecha_devolucion']);
                $vencido = ($p['estado'] === 'prestado' && $devolucion < $hoy);
              ?>
              <span class="<?php echo $vencido ? 'fecha-vencida' : ''; ?>">
                <?php echo date('d/m/Y', strtotime($p['fecha_devolucion'])); ?>
                <?php echo $vencido ? ' ⚠️' : ''; ?>
              </span>
            </td>
            <td>
              <?php if ($p['estado'] === 'prestado'): ?>
                <span class="badge badge-amarillo">📤 Prestado</span>
              <?php else: ?>
                <span class="badge badge-verde">✅ Devuelto</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($p['estado'] === 'prestado'): ?>
                <a href="devolver_libro.php?id=<?php echo $p['id_prestamo']; ?>" class="btn-devolver">
                  ↩️ Devolver
                </a>
              <?php else: ?>
                <span class="td-devuelto">✔ Completado</span>
              <?php endif; ?>
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

/* Tabla */
.tabla-container {
  overflow-x: auto;
  border-radius: 14px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--crema-oscura);
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  font-size: 0.9rem;
}

thead {
  background: linear-gradient(135deg, var(--marron-oscuro), var(--marron));
  color: var(--crema);
}

thead th {
  padding: 1rem 1.25rem;
  text-align: left;
  font-family: 'Playfair Display', serif;
  font-weight: 600;
  font-size: 0.88rem;
  letter-spacing: 0.03em;
  white-space: nowrap;
}

tbody tr {
  border-bottom: 1px solid var(--crema-oscura);
  transition: background 0.15s;
}

tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: #fdf8f0; }

tbody td {
  padding: 0.9rem 1.25rem;
  vertical-align: middle;
  color: var(--texto);
}

.td-num {
  color: var(--texto-suave);
  font-size: 0.8rem;
  text-align: center;
  width: 40px;
}

.td-usuario {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.usuario-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--marron), var(--marron-claro));
  color: var(--crema);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  font-family: 'Playfair Display', serif;
  flex-shrink: 0;
}

.td-libro { color: var(--marron-oscuro); font-style: italic; }

.fecha-vencida { color: #c0392b; font-weight: 700; }

/* Badges */
.badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 700;
  white-space: nowrap;
}

.badge-verde  { background: #d4edda; color: #155724; }
.badge-amarillo { background: #fff3cd; color: #856404; }

/* Botón devolver */
.btn-devolver {
  display: inline-block;
  padding: 0.4rem 0.9rem;
  background: var(--dorado);
  color: var(--marron-oscuro);
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition: background 0.2s, transform 0.2s;
  box-shadow: 0 2px 6px rgba(201,146,42,0.25);
}

.btn-devolver:hover {
  background: var(--dorado-claro);
  transform: translateY(-1px);
}

.td-devuelto {
  font-size: 0.82rem;
  color: #27ae60;
  font-weight: 600;
}

/* Empty state */
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
  thead th, tbody td { padding: 0.75rem 0.9rem; font-size: 0.82rem; }
}
</style>

<?php include("../includes/footer.php"); ?>