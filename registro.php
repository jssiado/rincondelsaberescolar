<?php
include("../conexion.php");

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol      = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $password, $rol);

    try {
        $stmt->execute();
        header("Location: registro.php?exito=1");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $mensaje = "Este correo ya está registrado.";
        } else {
            $mensaje = "Error al registrar usuario. Inténtalo de nuevo.";
        }
        $tipo_mensaje = "error";
    }
}

if (isset($_GET['exito'])) {
    $mensaje = "¡Usuario registrado correctamente! Ya puedes iniciar sesión.";
    $tipo_mensaje = "exito";
}
?>
<?php include("../includes/header.php"); ?>

<main id="contenido-principal">

  <div class="login-wrapper">

    <!-- Panel decorativo izquierdo -->
    <div class="login-deco" aria-hidden="true">
      <div class="login-deco-inner">
        <span class="login-deco-icon">📚</span>
        <h2>Únete al<br><span style="color:var(--dorado-claro)">Rincón del<br>Saber Escolar</span></h2>
        <p>Crea tu cuenta y accede a todos los beneficios de nuestra biblioteca escolar.</p>
        <ul class="login-deco-list">
          <li>📖 Solicita préstamos de libros</li>
          <li>📋 Consulta tu historial</li>
          <li>🔔 Recibe recordatorios de devolución</li>
          <li>🌱 Apoya tu aprendizaje</li>
        </ul>
      </div>
    </div>

    <!-- Tarjeta formulario -->
    <div class="login-card">

      <div class="login-card-header">
        <div class="login-avatar">✏️</div>
        <h3>Crear cuenta</h3>
        <p>Completa el formulario para registrarte.</p>
      </div>

      <?php if ($mensaje !== ""): ?>
        <div class="alerta alerta-<?php echo $tipo_mensaje === 'error' ? 'error' : 'exito'; ?>">
          <?php echo $tipo_mensaje === 'error' ? '⚠️' : '✅'; ?>
          <?php echo htmlspecialchars($mensaje); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="login-form" novalidate>

        <div class="form-group">
          <label for="nombre">
            <span class="label-icon">👤</span> Nombre completo
          </label>
          <input
            type="text"
            id="nombre"
            name="nombre"
            placeholder="Tu nombre completo"
            value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
            required
            autocomplete="name"
          >
        </div>

        <div class="form-group">
          <label for="email">
            <span class="label-icon">✉️</span> Correo electrónico
          </label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="ejemplo@correo.com"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
            required
            autocomplete="email"
          >
        </div>

        <div class="form-group">
          <label for="password">
            <span class="label-icon">🔑</span> Contraseña
          </label>
          <div class="input-password-wrap">
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Mínimo 6 caracteres"
              required
              autocomplete="new-password"
            >
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña" onclick="togglePassword()">
              <i class="fa-regular fa-eye" id="eye-icon"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="rol">
            <span class="label-icon">🎓</span> Tipo de usuario
          </label>
          <select id="rol" name="rol">
            <option value="estudiante" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'estudiante') ? 'selected' : ''; ?>>
              🎓 Estudiante
            </option>
            <option value="admin" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'admin') ? 'selected' : ''; ?>>
              🛠️ Administrador
            </option>
          </select>
        </div>

        <button type="submit" class="btn-login">
          Crear cuenta <span>→</span>
        </button>

      </form>

      <div class="login-footer-links">
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
      </div>

    </div>
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

.login-wrapper {
  display: flex;
  width: 100%;
  max-width: 900px;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(59,32,13,0.18);
}

.login-deco {
  flex: 1;
  background: linear-gradient(145deg, var(--marron-oscuro) 0%, var(--marron) 60%, #7a4a25 100%);
  color: var(--crema);
  padding: 3.5rem 2.5rem;
  display: flex;
  align-items: center;
  position: relative;
  overflow: hidden;
}

.login-deco::before {
  content: '';
  position: absolute;
  inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
}

.login-deco-inner { position: relative; z-index: 1; }

.login-deco-icon { font-size: 3.5rem; display: block; margin-bottom: 1.25rem; }

.login-deco h2 {
  font-family: 'Playfair Display', serif;
  font-size: 1.85rem;
  color: var(--crema);
  line-height: 1.25;
  margin-bottom: 1rem;
}

.login-deco p {
  font-style: italic;
  color: var(--crema-oscura);
  font-size: 0.9rem;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.login-deco-list {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.login-deco-list li {
  font-size: 0.85rem;
  color: var(--crema-oscura);
  background: rgba(255,255,255,0.07);
  padding: 0.55rem 1rem;
  border-radius: 8px;
  border-left: 3px solid var(--dorado);
}

.login-card {
  flex: 1;
  background: white;
  padding: 3rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.login-card-header { text-align: center; margin-bottom: 1.75rem; }

.login-avatar { font-size: 2.8rem; margin-bottom: 0.6rem; display: block; }

.login-card-header h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.75rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.35rem;
}

.login-card-header p {
  font-size: 0.88rem;
  color: var(--texto-suave);
  font-style: italic;
}

.login-form .form-group { margin-bottom: 1.15rem; }

.login-form label {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--marron-oscuro);
  margin-bottom: 0.4rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.label-icon { font-size: 1rem; }

.login-form input[type="text"],
.login-form input[type="email"],
.login-form input[type="password"],
.login-form select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1.5px solid var(--crema-oscura);
  border-radius: 8px;
  font-family: 'Lora', serif;
  font-size: 0.93rem;
  color: var(--texto);
  background: var(--crema-suave);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.login-form input:focus,
.login-form select:focus {
  border-color: var(--dorado);
  box-shadow: 0 0 0 3px rgba(201,146,42,0.15);
  background: white;
}

.input-password-wrap { position: relative; }
.input-password-wrap input { padding-right: 3rem; }

.toggle-password {
  position: absolute;
  right: 0.9rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: var(--texto-suave);
  font-size: 1rem;
  padding: 0;
  transition: color 0.2s;
}
.toggle-password:hover { color: var(--dorado); }

.btn-login {
  width: 100%;
  padding: 0.9rem;
  background: linear-gradient(135deg, var(--dorado), var(--dorado-claro));
  color: var(--marron-oscuro);
  border: none;
  border-radius: 8px;
  font-family: 'Playfair Display', serif;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  box-shadow: 0 4px 15px rgba(201,146,42,0.35);
  transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
}

.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(201,146,42,0.45);
  opacity: 0.93;
}

.btn-login span { font-size: 1.2rem; }

.login-footer-links {
  text-align: center;
  margin-top: 1.5rem;
  font-size: 0.85rem;
  color: var(--texto-suave);
}

.login-footer-links a {
  color: var(--dorado);
  font-weight: 700;
  text-decoration: none;
  transition: color 0.2s;
}
.login-footer-links a:hover { color: var(--marron); }

@media (max-width: 700px) {
  .login-deco { display: none; }
  .login-card { padding: 2.5rem 1.5rem; border-radius: 20px; }
  .login-wrapper { border-radius: 20px; }
}
</style>

<script>
function togglePassword() {
  const input = document.getElementById('password');
  const icon  = document.getElementById('eye-icon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fa-regular fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fa-regular fa-eye';
  }
}
</script>

<?php include("../includes/footer.php"); ?>