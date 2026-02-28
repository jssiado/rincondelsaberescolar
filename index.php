<?php include("../includes/header.php"); ?>

<!-- HERO — ancho completo -->
<section id="hero">
  <span class="hero-badge">📚 Biblioteca Escolar</span>
  <h2>Un lugar para<br><em>descubrir, aprender</em><br>e imaginar</h2>
  <p>Un espacio dedicado a la lectura, el aprendizaje y la investigación. Aquí encontrarás libros para todas las áreas del conocimiento.</p>
  <div class="hero-btns">
    <a href="catalogo.php" class="btn-primary">📖 Ver catálogo</a>
    <a href="buscar.php" class="btn-secondary">🔍 Buscar libros</a>
  </div>
</section>

<!-- OBJETIVOS — fondo crema oscura -->
<div class="full-band band-crema">
  <section id="objetivos">
    <h3>Nuestros Objetivos</h3>
    <div class="section-divider"></div>
    <p class="section-subtitle">Lo que buscamos lograr con nuestra comunidad lectora</p>
    <div class="objetivos-grid">
      <div class="objetivo-card">
        <span class="objetivo-icon">📖</span>
        <h4>Fomentar el Hábito de Lectura</h4>
        <p>Promovemos la lectura como herramienta fundamental para el desarrollo personal y académico de nuestros estudiantes.</p>
      </div>
      <div class="objetivo-card">
        <span class="objetivo-icon">🔍</span>
        <h4>Facilitar el Acceso a la Información</h4>
        <p>Ponemos a disposición una amplia colección de libros y recursos para apoyar los procesos académicos e investigativos.</p>
      </div>
      <div class="objetivo-card">
        <span class="objetivo-icon">🌱</span>
        <h4>Desarrollar Habilidades</h4>
        <p>Contribuimos al desarrollo del pensamiento crítico y las competencias comunicativas a través de la lectura y la investigación.</p>
      </div>
    </div>
  </section>
</div>

<!-- CATÁLOGO PREVIEW — fondo crema suave -->
<div class="full-band band-white">
  <section id="catalogo-preview">
    <h3>Catálogo de Libros</h3>
    <div class="section-divider"></div>
    <p class="section-subtitle">Explora nuestra colección disponible</p>

    <?php
    include("../conexion.php");
    $sql = "SELECT libros.titulo, libros.autor, categorias.nombre AS categoria
            FROM libros
            INNER JOIN categorias ON libros.id_categoria = categorias.id_categoria
            LIMIT 6";
    $resultado = $conexion->query($sql);
    $iconos = ['📘','📗','📙','📕','📓','📔'];
    $i = 0;

    if ($resultado && $resultado->num_rows > 0) {
        echo '<div class="libros-grid">';
        while ($libro = $resultado->fetch_assoc()) {
            $icono = $iconos[$i % count($iconos)];
            echo "
            <div class='libro-card'>
              <span class='libro-icono'>{$icono}</span>
              <h4>{$libro['titulo']}</h4>
              <p class='autor'>{$libro['autor']}</p>
              <span class='libro-badge'>{$libro['categoria']}</span>
            </div>";
            $i++;
        }
        echo '</div>';
    } else {
        echo "<p style='text-align:center;color:var(--texto-suave);font-style:italic;'>No hay libros registrados aún.</p>";
    }
    ?>

    <div style="text-align:center; margin-top:2rem;">
      <a href="catalogo.php" class="btn-primary">Ver todos los libros →</a>
    </div>
  </section>
</div>

<!-- SOLICITUD DE PRÉSTAMO — fondo crema oscura -->
<div class="full-band band-crema">
  <section id="prestamo-preview">
    <h3>Solicitud de Préstamo</h3>
    <div class="section-divider"></div>
    <p class="section-subtitle">Lleva tus libros favoritos a casa</p>

    <div class="prestamo-preview-grid">

      <!-- Formulario funcional -->
      <div class="prestamo-form-card">
        <div class="prestamo-form-title">
          <span>📋</span> Formulario de Préstamo
        </div>

        <?php if (!isset($_SESSION['id_usuario'])): ?>
          <div class="pf-login-aviso">
            🔐 <a href="login.php">Inicia sesión</a> para solicitar un préstamo.
          </div>
        <?php else: ?>

        <form method="GET" action="prestamo.php">

          <div class="pf-group">
            <label>Nombre del estudiante</label>
            <input type="text" value="<?php echo htmlspecialchars($_SESSION['nombre']); ?>" disabled>
          </div>

          <div class="pf-group">
            <label for="pf_libro">Título del libro <span class="req">*</span></label>
            <select id="pf_libro" name="id" required>
              <option value="" disabled selected>— Selecciona un libro —</option>
              <?php
              $libros_prev = $conexion->query("SELECT id_libro, titulo FROM libros WHERE stock > 0 ORDER BY titulo ASC");
              while ($l = $libros_prev->fetch_assoc()):
              ?>
                <option value="<?php echo $l['id_libro']; ?>"><?php echo htmlspecialchars($l['titulo']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="pf-group">
            <label for="pf_fecha">Fecha de devolución <span class="req">*</span></label>
            <input type="date" id="pf_fecha" name="fecha"
              min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
              max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>"
              required>
          </div>

          <button type="submit" class="pf-btn">✈️ Solicitar Préstamo</button>
          <p class="pf-note">* Campos obligatorios</p>

        </form>
        <?php endif; ?>
      </div>

      <!-- Info importante -->
      <div class="prestamo-info-card">
        <div class="prestamo-info-title">
          <span>ℹ️</span> <span>Información Importante</span>
        </div>
        <ol class="info-numerada">
          <li>
            El plazo máximo de préstamo es de <strong>15 días</strong> calendario.
          </li>
          <li>
            Puedes renovar el préstamo una vez si nadie más lo ha solicitado.
          </li>
          <li>
            El libro debe ser devuelto en buen estado. Cualquier daño deberá ser reportado.
          </li>
          <li>
            Con carnet estudiantil puedes solicitar hasta <strong>3 libros</strong> simultáneos.
          </li>
          <li>
            Horario de atención: Lunes a Viernes, 7:00 am – 4:00 pm.
          </li>
        </ol>
      </div>

    </div>
  </section>
</div>

<!-- SOBRE LA BIBLIOTECA — fondo marrón oscuro -->
<div class="full-band band-dark">
  <section id="info">
    <h3>Sobre la Biblioteca</h3>
    <div class="section-divider"></div>
    <p>La Biblioteca Escolar Rincón del Saber busca ser un punto de encuentro para estudiantes y docentes, promoviendo el conocimiento y el pensamiento crítico.</p>
  </section>
</div>



<?php include("../includes/footer.php"); ?>