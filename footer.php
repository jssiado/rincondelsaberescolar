<footer id="contacto">

  <div class="footer-main">

    <!-- Columna 1: Logo + frase + redes -->
    <div class="footer-col footer-brand">
      <div class="footer-logo">
        <span>📚</span>
        <span class="footer-logo-text">Rincón del Saber Escolar</span>
      </div>
      <p class="footer-frase">
        "Un libro abierto es un cerebro que habla; cerrado, un amigo que espera; olvidado, un alma que perdona."
      </p>
      <p class="footer-contactanos">CONTÁCTANOS</p>
      <div class="footer-redes">
        <a href="#" class="red-btn red-whatsapp">
          <span>💬</span> WhatsApp
        </a>
        <a href="https://wa.link/hkbb7j" class="red-btn red-facebook">
          <span>📘</span> Facebook
        </a>
        <a href="#" class="red-btn red-instagram">
          <span>📸</span> Instagram
        </a>
        <a href="#" class="red-btn red-correo">
          <span>✉️</span> Correo
        </a>
      </div>
    </div>

    <!-- Columna 2: Navegación -->
    <div class="footer-col">
      <h4>Navegación</h4>
      <hr class="footer-hr">
      <ul class="footer-links">
        <li><a href="/biblioteca/pages/index.php">Inicio</a></li>
        <li><a href="/biblioteca/pages/catalogo.php">Catálogo</a></li>
        <li><a href="/biblioteca/pages/buscar.php">Buscar Libros</a></li>
        <li><a href="/biblioteca/pages/prestamo.php">Préstamos</a></li>
        <?php if (isset($_SESSION['id_usuario'])): ?>
          <li><a href="/biblioteca/pages/mis_prestamos.php">Mis Préstamos</a></li>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Columna 3: Horarios + Más info -->
    <div class="footer-col">
      <h4>Horarios de Atención</h4>
      <hr class="footer-hr">
      <table class="footer-horarios">
        <tr>
          <td>Lun – Vie</td>
          <td>7:00 – 16:00</td>
        </tr>
        <tr>
          <td>Sábados</td>
          <td>8:00 – 12:00</td>
        </tr>
        <tr>
          <td>Domingos</td>
          <td class="cerrado">Cerrado</td>
        </tr>
      </table>

      <h4 style="margin-top:1.75rem;">Más información</h4>
      <hr class="footer-hr">
      <ul class="footer-links">
        <li><a href="#">Reglamento Interno</a></li>
        <li><a href="#">Donar un Libro</a></li>
        <li><a href="#">Política de Préstamos</a></li>
      </ul>
    </div>

  </div>

  <!-- Barra inferior -->
  <div class="footer-bottom">
    <p>© 2026 Rincón del Saber Escolar – Biblioteca Escolar</p>
    <p>Todos los derechos reservados · <a href="#">Política de privacidad</a></p>
  </div>

</footer>

<script>
  const navToggle = document.querySelector('.nav-toggle');
  const navMenu   = document.getElementById('nav-menu');
  if (navToggle) {
    navToggle.addEventListener('click', () => {
      navMenu.classList.toggle('abierto');
    });
  }
</script>

</body>
</html>

<style>
footer {
  background: var(--marron-oscuro);
  color: var(--crema-oscura);
  margin-top: 4rem;
}

.footer-main {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr;
  gap: 3rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 3.5rem 2.5rem 2.5rem;
}

/* Brand */
.footer-brand {
  padding-right: 1rem;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1rem;
  font-size: 1.05rem;
}

.footer-logo span:first-child { font-size: 1.5rem; }

.footer-logo-text {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  color: var(--dorado-claro);
}

.footer-frase {
  font-style: italic;
  font-size: 0.85rem;
  color: rgba(232,213,176,0.75);
  line-height: 1.7;
  margin-bottom: 1.5rem;
  max-width: 320px;
}

.footer-contactanos {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--crema-oscura);
  margin-bottom: 0.75rem;
}

.footer-redes {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.red-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 0.9rem;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  text-decoration: none;
  color: white;
  transition: opacity 0.2s, transform 0.2s;
}

.red-btn:hover { opacity: 0.88; transform: translateY(-2px); }

.red-whatsapp  { background: #25D366; }
.red-facebook  { background: #1877F2; }
.red-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
.red-correo    { background: #5C3317; border: 1px solid rgba(255,255,255,0.2); }

/* Columnas */
.footer-col h4 {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  color: var(--crema);
  margin-bottom: 0.6rem;
}

.footer-hr {
  border: none;
  border-top: 1px solid rgba(255,255,255,0.15);
  margin-bottom: 1.1rem;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
}

.footer-links a {
  font-size: 0.87rem;
  color: rgba(232,213,176,0.8);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover { color: var(--dorado-claro); }

/* Horarios - MEJORADO */
.footer-horarios {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
  background: rgba(0, 0, 0, 0.15);
  border-radius: 10px;
  overflow: hidden;
}

.footer-horarios tr:not(:last-child) {
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-horarios td {
  padding: 0.55rem 0.85rem;
  color: rgba(232, 213, 176, 0.85);
  vertical-align: middle;
}

.footer-horarios td:first-child {
  color: rgba(232, 213, 176, 0.7);
  font-weight: 500;
}

.footer-horarios td:last-child {
  text-align: right;
  color: var(--crema);
  font-weight: 600;
}

.cerrado {
  background: rgba(180, 100, 60, 0.25);
  color: rgba(255, 190, 140, 0.85) !important;
  font-style: italic;
  font-weight: 500 !important;
}

/* Bottom bar */
.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.1);
  padding: 1.25rem 2.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  font-size: 0.78rem;
  color: rgba(232,213,176,0.55);
  max-width: 1200px;
  margin: 0 auto;
}

.footer-bottom p {
  margin: 0;
}

.footer-bottom a {
  color: rgba(232,213,176,0.7);
  text-decoration: none;
  transition: color 0.2s;
}
.footer-bottom a:hover { color: var(--dorado-claro); }

@media (max-width: 900px) {
  .footer-main { grid-template-columns: 1fr 1fr; gap: 2rem; }
  .footer-brand { grid-column: 1 / -1; }
}

@media (max-width: 600px) {
  .footer-main { grid-template-columns: 1fr; padding: 2.5rem 1.5rem 1.5rem; }
  .footer-brand { grid-column: auto; padding-right: 0; }
  .footer-bottom { flex-direction: column; text-align: center; padding: 1rem 1.5rem; }
}
</style>