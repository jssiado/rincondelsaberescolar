<?php
session_start();
include("../conexion.php");

if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$ocultar_menu = true;
$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['contraseña'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['rol']        = $usuario['rol'];

            if ($usuario['rol'] === 'admin') {
                header("Location: elegir_vista.php");
            } else {
                $_SESSION['vista'] = 'usuario';
                header("Location: index.php");
            }
            exit();
        } else {
            $mensaje = "Contraseña incorrecta. Inténtalo de nuevo.";
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "El correo no está registrado.";
        $tipo_mensaje = "error";
    }
}
?>
<?php include("../includes/header.php"); ?>

<main id="contenido-principal">

  <div class="login-wrapper">

    <!-- Panel izquierdo decorativo -->
    <div class="login-deco" aria-hidden="true">
      <div class="login-deco-inner">
        <span class="login-deco-icon">📚</span>
        <h2>Rincón del<br>Saber Escolar</h2>
        <p>Un espacio dedicado a la lectura, el aprendizaje y la investigación.</p>
        <ul class="login-deco-list">
          <li>📖 Accede a tu historial de préstamos</li>
          <li>🔍 Busca libros del catálogo</li>
          <li>✅ Solicita préstamos fácilmente</li>
        </ul>
      </div>
    </div>

    <!-- Tarjeta del formulario -->
    <div class="login-card">

      <div class="login-card-header">
        <div class="login-avatar">🔐</div>
        <h3>Iniciar sesión</h3>
        <p>Bienvenido de nuevo. Ingresa tus datos para continuar.</p>
      </div>

      <?php if ($mensaje !== ""): ?>
        <div class="alerta alerta-<?php echo $tipo_mensaje === 'error' ? 'error' : 'exito'; ?>">
          <?php echo $tipo_mensaje === 'error' ? '⚠️' : '✅'; ?>
          <?php echo htmlspecialchars($mensaje); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="login-form" novalidate>

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
              placeholder="Tu contraseña"
              required
              autocomplete="current-password"
            >
            <button type="button" class="toggle-password" aria-label="Mostrar contraseña" onclick="togglePassword()">
              <i class="fa-regular fa-eye" id="eye-icon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login">
          Ingresar <span>→</span>
        </button>

      </form>

      <div class="login-footer-links">
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
      </div>

    </div>
  </div>

</main>

<style>
/* ---- Login Layout ---- */
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

/* ---- Panel decorativo izquierdo ---- */
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

.login-deco-icon {
  font-size: 3.5rem;
  display: block;
  margin-bottom: 1.25rem;
}

.login-deco h2 {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  color: var(--dorado-claro);
  line-height: 1.2;
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
  gap: 0.75rem;
}

.login-deco-list li {
  font-size: 0.85rem;
  color: var(--crema-oscura);
  background: rgba(255,255,255,0.07);
  padding: 0.6rem 1rem;
  border-radius: 8px;
  border-left: 3px solid var(--dorado);
}

/* ---- Tarjeta formulario ---- */
.login-card {
  flex: 1;
  background: white;
  padding: 3.5rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.login-card-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-avatar {
  font-size: 3rem;
  margin-bottom: 0.75rem;
  display: block;
}

.login-card-header h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  color: var(--marron-oscuro);
  margin-bottom: 0.4rem;
}

.login-card-header p {
  font-size: 0.88rem;
  color: var(--texto-suave);
  font-style: italic;
}

/* ---- Form groups ---- */
.login-form .form-group {
  margin-bottom: 1.35rem;
}

.login-form label {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--marron-oscuro);
  margin-bottom: 0.45rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

.label-icon { font-size: 1rem; }

.login-form input[type="email"],
.login-form input[type="password"],
.login-form input[type="text"] {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 1.5px solid var(--crema-oscura);
  border-radius: 8px;
  font-family: 'Lora', serif;
  font-size: 0.95rem;
  color: var(--texto);
  background: var(--crema-suave);
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
}

.login-form input:focus {
  border-color: var(--dorado);
  box-shadow: 0 0 0 3px rgba(201,146,42,0.15);
  background: white;
}

/* ---- Password toggle ---- */
.input-password-wrap {
  position: relative;
}

.input-password-wrap input {
  padding-right: 3rem;
}

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

/* ---- Botón submit ---- */
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

/* ---- Links inferiores ---- */
.login-footer-links {
  text-align: center;
  margin-top: 1.75rem;
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

/* ---- Responsive ---- */
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