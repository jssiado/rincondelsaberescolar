<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<?php include("../includes/header.php"); ?>

<main id="contenido-principal">

  <div class="vista-wrapper">

    <div class="vista-header">
      <div class="vista-corona">👑</div>
      <h2>Bienvenido, <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span></h2>
      <p>Tienes acceso como administrador. ¿Cómo deseas ingresar hoy?</p>
    </div>

    <form action="set_vista.php" method="POST" class="vista-opciones">

      <button name="vista" value="usuario" class="vista-card">
        <div class="vista-card-icon">👤</div>
        <div class="vista-card-info">
          <h3>Vista de Usuario</h3>
          <p>Explora el catálogo, solicita préstamos y consulta tu historial como estudiante.</p>
        </div>
        <span class="vista-arrow">→</span>
      </button>

      <button name="vista" value="admin" class="vista-card vista-card-admin">
        <div class="vista-card-icon">🛠️</div>
        <div class="vista-card-info">
          <h3>Panel Administrador</h3>
          <p>Gestiona libros, usuarios y préstamos desde el panel de control.</p>
        </div>
        <span class="vista-arrow">→</span>
      </button>

    </form>

    <p class="vista-salir">
      ¿No eres tú? <a href="logout.php">Cerrar sesión</a>
    </p>

  </div>

</main>

<style>
#contenido-principal {
  min-height: calc(100vh - 140px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1.5rem;
  background: var(--crema-suave);
}

.vista-wrapper {
  width: 100%;
  max-width: 560px;
  text-align: center;
}

.vista-corona {
  font-size: 3.5rem;
  margin-bottom: 0.75rem;
  animation: flotar 2.5s ease-in-out infinite;
}

@keyframes flotar {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-8px); }
}

.vista-header h2 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.5rem;
}

.vista-header h2 span {
  color: var(--dorado);
}

.vista-header p {
  color: var(--texto-suave);
  font-style: italic;
  font-size: 0.95rem;
  margin-bottom: 2.5rem;
}

/* Tarjetas */
.vista-opciones {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: none;
  border: none;
  padding: 0;
}

.vista-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  background: white;
  border: 2px solid var(--crema-oscura);
  border-radius: 14px;
  padding: 1.5rem 1.75rem;
  cursor: pointer;
  text-align: left;
  width: 100%;
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
  box-shadow: var(--shadow-sm);
  font-family: 'Lora', serif;
}

.vista-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-md);
  border-color: var(--dorado);
}

.vista-card-admin {
  background: linear-gradient(135deg, var(--marron-oscuro), var(--marron));
  border-color: var(--marron);
  color: var(--crema);
}

.vista-card-admin:hover {
  border-color: var(--dorado-claro);
  box-shadow: 0 8px 28px rgba(59,32,13,0.25);
}

.vista-card-icon {
  font-size: 2.4rem;
  flex-shrink: 0;
}

.vista-card-info { flex: 1; }

.vista-card-info h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.1rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.3rem;
}

.vista-card-admin .vista-card-info h3 {
  color: var(--dorado-claro);
}

.vista-card-info p {
  font-size: 0.82rem;
  color: var(--texto-suave);
  line-height: 1.5;
  margin: 0;
}

.vista-card-admin .vista-card-info p {
  color: var(--crema-oscura);
}

.vista-arrow {
  font-size: 1.3rem;
  color: var(--dorado);
  flex-shrink: 0;
  transition: transform 0.2s;
}

.vista-card:hover .vista-arrow {
  transform: translateX(4px);
}

.vista-card-admin .vista-arrow {
  color: var(--dorado-claro);
}

.vista-salir {
  margin-top: 1.75rem;
  font-size: 0.85rem;
  color: var(--texto-suave);
}

.vista-salir a {
  color: var(--dorado);
  font-weight: 700;
  text-decoration: none;
  transition: color 0.2s;
}

.vista-salir a:hover { color: var(--marron); }

@media (max-width: 480px) {
  .vista-card { padding: 1.25rem; }
  .vista-card-icon { font-size: 2rem; }
}
</style>

<?php include("../includes/footer.php"); ?>