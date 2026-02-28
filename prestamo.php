<?php
session_start();
include("../conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = (int)$_SESSION['id_usuario'];

// ── Contar préstamos ACTIVOS del usuario (solo 'prestado') ────────────────
$stmt_check = $conexion->prepare(
    "SELECT COUNT(*) AS total FROM prestamos
     WHERE id_usuario = ? AND estado = 'prestado'"
);
$stmt_check->bind_param("i", $id_usuario);
$stmt_check->execute();
$total_activos  = (int)$stmt_check->get_result()->fetch_assoc()['total'];
$limite_alcanzado = $total_activos >= 3;
$prestamos_disponibles = 3 - $total_activos; // cuántos más puede pedir

// ── Preseleccionar libro y fecha si vienen por GET (desde index) ──────────
$id_preseleccionado    = isset($_GET['id'])    ? (int)$_GET['id']                : 0;
$fecha_preseleccionada = isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '';

// ── Libros disponibles ────────────────────────────────────────────────────
$libros = $conexion->query(
    "SELECT id_libro, titulo FROM libros WHERE stock > 0 ORDER BY titulo ASC"
);

// ── Mensajes de error desde procesar_prestamo.php ────────────────────────
$errores = [
    'limite_prestamos' => '🚫 Ya tienes 3 préstamos activos. Devuelve uno antes de solicitar otro.',
    'libro_duplicado'  => '⚠️ Ya tienes este libro en préstamo activo. No puedes pedirlo dos veces.',
    'sin_stock'        => '⚠️ Este libro ya no tiene stock disponible.',
    'libro_invalido'   => '⚠️ Selecciona un libro válido de la lista.',
    'fecha_excedida'   => '⚠️ La fecha de devolución no puede superar los 15 días desde hoy.',
    'db'               => '⚠️ Error al registrar el préstamo. Inténtalo de nuevo.',
];
$error_msg = isset($_GET['error'], $errores[$_GET['error']]) ? $errores[$_GET['error']] : '';

include("../includes/header.php");
?>

<div class="page-header">
  <h2>📦 Solicitud de Préstamo</h2>
  <p>Crea una solicitud para un libro de nuestra colección</p>
</div>

<main id="contenido-principal">
<div class="prestamo-wrapper">

  <!-- Formulario -->
  <div class="form-card">

    <div class="form-card-header">
      <span class="form-card-icon">📋</span>
      <div>
        <h3>Formulario de Préstamo</h3>
        <p>Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
          — puedes solicitar <strong><?php echo $prestamos_disponibles; ?></strong>
          libro<?php echo $prestamos_disponibles !== 1 ? 's' : ''; ?> más
        </p>
      </div>
    </div>

    <?php if ($error_msg !== ''): ?>
      <div class="alerta alerta-error" style="margin:1.25rem 2rem 0;">
        <?php echo $error_msg; ?>
      </div>
    <?php endif; ?>

    <?php if ($limite_alcanzado): ?>
      <!-- BLOQUEADO: ya tiene 3 préstamos activos -->
      <div class="prestamo-bloqueado">
        <span class="bloqueo-icon">🚫</span>
        <h4>Límite de préstamos alcanzado</h4>
        <p>Ya tienes <strong>3 libros prestados</strong>, que es el máximo permitido.</p>
        <p>Debes devolver al menos uno antes de solicitar un nuevo préstamo.</p>
        <a href="mis_prestamos.php" class="btn-guardar"
           style="margin-top:1.25rem;display:inline-block;text-decoration:none;">
          📋 Ver mis préstamos
        </a>
      </div>

    <?php else: ?>
      <!-- FORMULARIO ACTIVO -->
      <form method="POST" action="procesar_prestamo.php" class="libro-form">

        <div class="form-group">
          <label>👤 Solicitante</label>
          <input type="text"
                 value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>"
                 disabled>
        </div>

        <div class="form-group">
          <label for="id_libro">📚 Libro <span class="label-req">*</span></label>
          <select id="id_libro" name="id_libro" required>
            <option value="" disabled selected>— Elige un libro disponible —</option>
            <?php
            $libros->data_seek(0);
            while ($libro = $libros->fetch_assoc()):
            ?>
              <option value="<?php echo $libro['id_libro']; ?>"
                <?php echo $id_preseleccionado === (int)$libro['id_libro'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($libro['titulo']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="fecha_devolucion">
            📅 Fecha de devolución <span class="label-req">*</span>
            <span class="label-hint">(máximo 15 días)</span>
          </label>
          <input type="date"
                 id="fecha_devolucion"
                 name="fecha_devolucion"
                 min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                 max="<?php echo date('Y-m-d', strtotime('+15 days')); ?>"
                 value="<?php echo $fecha_preseleccionada; ?>"
                 oninput="validarFecha(this)"
                 required>
          <span id="fecha-error"
                style="display:none;color:#c0392b;font-size:0.8rem;margin-top:0.25rem;">
            ⚠️ La fecha no puede superar los 15 días desde hoy.
          </span>
        </div>

        <div class="form-actions-row">
          <a href="catalogo.php" class="btn-volver">← Ver catálogo</a>
          <button type="submit" class="btn-guardar" id="btn-submit">
            📥 Solicitar Préstamo
          </button>
        </div>

      </form>
    <?php endif; ?>

  </div>

  <!-- Panel informativo -->
  <div class="form-sidebar">
    <div class="sidebar-card sidebar-card-dark">
      <h4>ℹ️ Información Importante</h4>
      <ol class="info-numerada">
        <li>El plazo máximo de préstamo es de <strong>15 días</strong> calendario.</li>
        <li>Puedes renovar el préstamo una vez si nadie más lo ha solicitado.</li>
        <li>El libro debe ser devuelto en buen estado. Cualquier daño deberá ser reportado.</li>
        <li>Con carnet estudiantil puedes solicitar hasta <strong>3 libros</strong> simultáneos.</li>
        <li>Horario de atención: Lunes a Viernes, 7:00 am – 4:00 pm.</li>
      </ol>
    </div>

    <div class="sidebar-card">
      <h4>📚 Mis préstamos</h4>
      <p style="font-size:0.83rem;color:var(--texto-suave);margin-bottom:1rem;">
        Tienes <strong><?php echo $total_activos; ?></strong> de 3 préstamos activos.
      </p>
      <div class="prestamos-barra">
        <div class="prestamos-barra-fill" style="width:<?php echo ($total_activos/3)*100; ?>%"></div>
      </div>
      <a href="mis_prestamos.php" class="sidebar-link-btn" style="margin-top:1rem;">
        Ver mis préstamos →
      </a>
    </div>
  </div>

</div>
</main>

<style>
.prestamo-wrapper { max-width: 1000px; margin: 2.5rem auto 4rem; padding: 0 2rem; display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start; }

.form-card { background: white; border-radius: 16px; border: 1px solid var(--crema-oscura); box-shadow: var(--shadow-md); overflow: hidden; }
.form-card-header { display: flex; align-items: center; gap: 1rem; padding: 1.75rem 2rem; background: var(--crema); border-bottom: 1px solid var(--crema-oscura); }
.form-card-icon { font-size: 2.5rem; }
.form-card-header h3 { font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--marron-oscuro); margin-bottom: 0.2rem; }
.form-card-header p { font-size: 0.82rem; color: var(--texto-suave); font-style: italic; margin: 0; }
.form-card-header strong { color: var(--marron-oscuro); font-style: normal; }

.libro-form { padding: 1.75rem 2rem 2rem; display: flex; flex-direction: column; gap: 1.25rem; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-group label { font-size: 0.82rem; font-weight: 700; color: var(--marron-oscuro); text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; }
.label-req { color: #c0392b; }
.label-hint { font-size: 0.72rem; color: var(--texto-suave); font-weight: 400; text-transform: none; letter-spacing: 0; font-style: italic; }

.form-group input, .form-group select { padding: 0.75rem 1rem; border: 1.5px solid var(--crema-oscura); border-radius: 8px; font-family: 'Lora', serif; font-size: 0.93rem; color: var(--texto); background: var(--crema-suave); transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
.form-group input:focus, .form-group select:focus { border-color: var(--dorado); box-shadow: 0 0 0 3px rgba(201,146,42,0.15); background: white; }
.form-group input:disabled { opacity: 0.6; cursor: not-allowed; }

.form-actions-row { display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; padding-top: 1.25rem; border-top: 1px solid var(--crema-oscura); gap: 1rem; flex-wrap: wrap; }

.btn-volver { padding: 0.7rem 1.25rem; border-radius: 8px; border: 1.5px solid var(--marron-claro); color: var(--marron); background: white; font-family: 'Lora', serif; font-size: 0.88rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-volver:hover { background: var(--crema); }

.btn-guardar { padding: 0.75rem 2rem; background: linear-gradient(135deg, var(--dorado), var(--dorado-claro)); color: var(--marron-oscuro); border: none; border-radius: 8px; font-family: 'Playfair Display', serif; font-size: 1rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(201,146,42,0.3); transition: transform 0.2s, box-shadow 0.2s; }
.btn-guardar:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(201,146,42,0.4); }
.btn-guardar:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* Bloqueado */
.prestamo-bloqueado { text-align: center; padding: 2.5rem 2rem; background: #fff3cd; border-top: 1px solid #ffc107; }
.bloqueo-icon { font-size: 3rem; display: block; margin-bottom: 0.75rem; }
.prestamo-bloqueado h4 { font-family: 'Playfair Display', serif; font-size: 1.2rem; color: #856404; margin-bottom: 0.5rem; }
.prestamo-bloqueado p { font-size: 0.88rem; color: #856404; margin-bottom: 0.25rem; }
.prestamo-bloqueado strong { color: #533f03; }

/* Sidebar */
.form-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }
.sidebar-card { background: white; border-radius: 14px; border: 1px solid var(--crema-oscura); padding: 1.5rem; box-shadow: var(--shadow-sm); }
.sidebar-card h4 { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--marron-oscuro); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--crema-oscura); }
.sidebar-card-dark { background: linear-gradient(135deg, var(--marron-oscuro), var(--marron)); }
.sidebar-card-dark h4 { color: var(--dorado-claro); border-color: rgba(255,255,255,0.15); }

.info-numerada { list-style: none; padding: 0; counter-reset: info-counter; display: flex; flex-direction: column; gap: 0.85rem; }
.info-numerada li { counter-increment: info-counter; display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.82rem; color: var(--crema-oscura); line-height: 1.55; }
.info-numerada li::before { content: counter(info-counter); background: var(--dorado); color: var(--marron-oscuro); font-weight: 700; font-size: 0.72rem; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.15rem; }
.info-numerada strong { color: var(--dorado-claro); }

/* Barra de progreso préstamos */
.prestamos-barra { width: 100%; height: 8px; background: var(--crema-oscura); border-radius: 50px; overflow: hidden; margin-bottom: 0.5rem; }
.prestamos-barra-fill { height: 100%; background: linear-gradient(90deg, var(--dorado), var(--dorado-claro)); border-radius: 50px; transition: width 0.4s ease; }

.sidebar-link-btn { display: block; padding: 0.65rem 1rem; background: var(--dorado); color: var(--marron-oscuro); border-radius: 8px; font-weight: 700; font-size: 0.85rem; text-decoration: none; text-align: center; transition: background 0.2s; }
.sidebar-link-btn:hover { background: var(--dorado-claro); }

.alerta { padding: 1rem 1.25rem; border-radius: 8px; font-size: 0.9rem; display: flex; align-items: center; gap: 0.6rem; }
.alerta-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alerta-exito { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

@media (max-width: 768px) {
  .prestamo-wrapper { grid-template-columns: 1fr; padding: 0 1rem; }
  .libro-form { padding: 1.25rem; }
  .form-card-header { padding: 1.25rem; }
  .form-sidebar { display: none; }
}
</style>

<script>
const fechaMax = new Date();
fechaMax.setDate(fechaMax.getDate() + 15);

function validarFecha(input) {
  const sel   = new Date(input.value);
  const error = document.getElementById('fecha-error');
  const btn   = document.getElementById('btn-submit');
  if (input.value && sel > fechaMax) {
    error.style.display = 'block';
    input.style.borderColor = '#c0392b';
    if (btn) btn.disabled = true;
  } else {
    error.style.display = 'none';
    input.style.borderColor = '';
    if (btn) btn.disabled = false;
  }
}
</script>

<?php include("../includes/footer.php"); ?>