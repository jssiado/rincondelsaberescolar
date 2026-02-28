<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = (int)$_SESSION['id_usuario'];
$hoy = date("Y-m-d");

$stmt = $conexion->prepare(
    "SELECT l.titulo, p.fecha_prestamo, p.fecha_devolucion, p.estado
     FROM prestamos p
     INNER JOIN libros l ON p.id_libro = l.id_libro
     WHERE p.id_usuario = ?
     ORDER BY p.fecha_prestamo DESC"
);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$total     = $resultado->num_rows;

// Contadores
$activos = $vencidos = $devueltos = 0;
$rows = [];
while ($p = $resultado->fetch_assoc()) {
    if ($p['estado'] === 'prestado' && $p['fecha_devolucion'] < $hoy) $vencidos++;
    elseif ($p['estado'] === 'prestado') $activos++;
    else $devueltos++;
    $rows[] = $p;
}

include("../includes/header.php");
?>

<div class="page-header">
  <h2>📦 Mis Préstamos</h2>
  <p>Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong> — aquí está tu historial de préstamos</p>
</div>

<main id="contenido-principal">
<div class="admin-wrapper">

  <!-- Resumen -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">📋</div>
      <div class="stat-info">
        <span class="stat-number"><?php echo $total; ?></span>
        <span class="stat-label">Total préstamos</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📤</div>
      <div class="stat-info">
        <span class="stat-number"><?php echo $activos; ?></span>
        <span class="stat-label">Activos</span>
      </div>
    </div>
    <div class="stat-card <?php echo $vencidos > 0 ? 'stat-card-alerta' : ''; ?>">
      <div class="stat-icon">⚠️</div>
      <div class="stat-info">
        <span class="stat-number"><?php echo $vencidos; ?></span>
        <span class="stat-label">Vencidos</span>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div class="stat-info">
        <span class="stat-number"><?php echo $devueltos; ?></span>
        <span class="stat-label">Devueltos</span>
      </div>
    </div>
  </div>

  <!-- Acciones -->
  <div class="prestamos-resumen">
    <span style="font-size:0.9rem;color:var(--texto-suave);">
      <?php echo $total; ?> préstamo<?php echo $total !== 1 ? 's' : ''; ?> registrado<?php echo $total !== 1 ? 's' : ''; ?>
    </span>
    <a href="catalogo.php" class="btn-devolver">📚 Explorar catálogo</a>
  </div>

  <?php if (isset($_GET['exito'])): ?>
    <div class="alerta alerta-exito">✅ ¡Préstamo registrado correctamente! Recuerda devolverlo a tiempo.</div>
  <?php endif; ?>

  <?php if ($total === 0): ?>
    <div class="empty-state">
      <span>📭</span>
      <p>No tienes préstamos registrados aún.</p>
      <a href="catalogo.php" class="btn-primary" style="margin-top:1rem;display:inline-block;">Ver catálogo</a>
    </div>

  <?php else: ?>

  <?php if ($vencidos > 0): ?>
    <div class="alerta alerta-error">
      ⚠️ Tienes <strong><?php echo $vencidos; ?></strong> préstamo<?php echo $vencidos !== 1 ? 's' : ''; ?> vencido<?php echo $vencidos !== 1 ? 's' : ''; ?>. Por favor devuelve los libros a la brevedad.
    </div>
  <?php endif; ?>

  <div class="tabla-container">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>📚 Libro</th>
          <th>📅 Fecha préstamo</th>
          <th>🔔 Fecha devolución</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $i => $p):
          $vencido  = $p['estado'] === 'prestado' && $p['fecha_devolucion'] < $hoy;
          $devuelto = $p['estado'] !== 'prestado';
          // Días restantes o vencidos
          $diff = (new DateTime($hoy))->diff(new DateTime($p['fecha_devolucion']));
          $dias = (int)$diff->format('%r%a');
        ?>
        <tr>
          <td class="td-num"><?php echo $i + 1; ?></td>
          <td class="td-libro"><em><?php echo htmlspecialchars($p['titulo']); ?></em></td>
          <td><?php echo date('d/m/Y', strtotime($p['fecha_prestamo'])); ?></td>
          <td>
            <span class="<?php echo $vencido ? 'fecha-vencida' : ''; ?>">
              <?php echo date('d/m/Y', strtotime($p['fecha_devolucion'])); ?>
            </span>
            <?php if (!$devuelto): ?>
              <br>
              <small class="dias-badge <?php echo $vencido ? 'dias-vencido' : ($dias <= 3 ? 'dias-urgente' : 'dias-ok'); ?>">
                <?php if ($vencido): ?>
                  <?php echo abs($dias); ?> día<?php echo abs($dias) !== 1 ? 's' : ''; ?> vencido
                <?php else: ?>
                  <?php echo $dias; ?> día<?php echo $dias !== 1 ? 's' : ''; ?> restante<?php echo $dias !== 1 ? 's' : ''; ?>
                <?php endif; ?>
              </small>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($vencido): ?>
              <span class="badge badge-rojo">⚠️ Vencido</span>
            <?php elseif ($devuelto): ?>
              <span class="badge badge-verde">✅ Devuelto</span>
            <?php else: ?>
              <span class="badge badge-amarillo">📤 Prestado</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php endif; ?>

</div>
</main>

<style>
.admin-wrapper { max-width: 1000px; margin: 0 auto; padding: 2rem 2rem 4rem; display: flex; flex-direction: column; gap: 1.5rem; }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }

.stat-card { background: white; border: 1px solid var(--crema-oscura); border-radius: 14px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm); }
.stat-card-alerta { background: #fff3cd; border-color: #ffc107; }
.stat-icon { font-size: 2rem; flex-shrink: 0; }
.stat-info { display: flex; flex-direction: column; }
.stat-number { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 900; color: var(--marron-oscuro); line-height: 1; }
.stat-label { font-size: 0.75rem; color: var(--texto-suave); text-transform: uppercase; letter-spacing: 0.06em; }

.prestamos-resumen { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; }

.btn-devolver { display: inline-block; padding: 0.5rem 1.1rem; background: var(--dorado); color: var(--marron-oscuro); border-radius: 8px; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: background 0.2s; box-shadow: 0 2px 6px rgba(201,146,42,0.25); }
.btn-devolver:hover { background: var(--dorado-claro); }

.tabla-container { overflow-x: auto; border-radius: 14px; box-shadow: var(--shadow-md); border: 1px solid var(--crema-oscura); }
table { width: 100%; border-collapse: collapse; background: white; font-size: 0.9rem; }
thead { background: linear-gradient(135deg, var(--marron-oscuro), var(--marron)); color: var(--crema); }
thead th { padding: 1rem 1.25rem; text-align: left; font-family: 'Playfair Display', serif; font-weight: 600; font-size: 0.88rem; white-space: nowrap; }
tbody tr { border-bottom: 1px solid var(--crema-oscura); transition: background 0.15s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: #fdf8f0; }
tbody td { padding: 0.9rem 1.25rem; vertical-align: middle; }

.td-num { color: var(--texto-suave); font-size: 0.8rem; text-align: center; width: 40px; }
.td-libro { color: var(--marron-oscuro); font-weight: 600; }
.fecha-vencida { color: #c0392b; font-weight: 700; }

.dias-badge { display: inline-block; font-size: 0.72rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 50px; margin-top: 0.25rem; }
.dias-ok      { background: #d4edda; color: #155724; }
.dias-urgente { background: #fff3cd; color: #856404; }
.dias-vencido { background: #f8d7da; color: #721c24; }

.badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
.badge-verde    { background: #d4edda; color: #155724; }
.badge-amarillo { background: #fff3cd; color: #856404; }
.badge-rojo     { background: #f8d7da; color: #721c24; }

.alerta { padding: 1rem 1.25rem; border-radius: 8px; font-size: 0.9rem; display: flex; align-items: center; gap: 0.6rem; }
.alerta-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

.empty-state { text-align: center; padding: 4rem 2rem; background: white; border-radius: 14px; border: 1px dashed var(--crema-oscura); color: var(--texto-suave); }
.empty-state span { font-size: 3rem; display: block; margin-bottom: 1rem; }
.empty-state p { font-style: italic; }

@media (max-width: 700px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .admin-wrapper { padding: 1.25rem 1rem 3rem; }
}
@media (max-width: 420px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<?php include("../includes/footer.php"); ?>