<?php
// ===============================
// CONFIGURACIÓN Y CONEXIÓN A MYSQL
// ===============================

// Nombre del host de MySQL dentro de la red de Docker.
// Coincide con el nombre del servicio en docker-compose.yml
$host = 'db';

// Nombre de la base de datos. Si no existe la variable de entorno MYSQL_DATABASE,
// usará 'appdb' como valor por defecto.
$db   = getenv('MYSQL_DATABASE') ?: 'appdb';

// Usuario y contraseña de la base de datos (variables de entorno en docker-compose).
$user = getenv('MYSQL_USER') ?: 'appuser';
$pass = getenv('MYSQL_PASSWORD') ?: 'app1234';

// Inicializamos las variables para manejar la conexión y posibles errores.
$pdo  = null;
$error = null;

try {
  // Creamos un nuevo objeto PDO para conectarnos a la BD.
  // "mysql:host=...;dbname=...;charset=utf8mb4" define el origen de datos (DSN).
  $pdo = new PDO(
    "mysql:host=$host;dbname=$db;charset=utf8mb4",
    $user,
    $pass,
    [
      // Si hay errores, se lanzan como excepciones (más fácil de controlar).
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // Al traer datos, se devuelven como arrays asociativos (clave → valor).
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );
} catch (Throwable $e) {
  // Si la conexión falla, guardamos el mensaje de error.
  $error = $e->getMessage();
}

// ===============================
// INSERCIÓN DE MENSAJES (POST)
// ===============================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['texto']) && trim($_POST['texto']) !== '') {
  try {
    // Preparamos una sentencia SQL para insertar datos en la tabla "mensajes".
    // Usamos parámetros (:t) para evitar inyección SQL.
    $stmt = $pdo->prepare("INSERT INTO mensajes (texto) VALUES (:t)");
    $stmt->execute([':t' => trim($_POST['texto'])]);

    // Redirigimos a la página principal para evitar reenviar el formulario al refrescar.
    header('Location: /');
    exit;
  } catch (Throwable $e) {
    $error = $e->getMessage();
  }
}

// ===============================
// LECTURA DE MENSAJES
// ===============================

$mensajes = [];
if ($pdo) {
  try {
    // Seleccionamos todos los mensajes, ordenados de más nuevo a más viejo.
    $mensajes = $pdo->query("SELECT id, texto FROM mensajes ORDER BY id DESC")->fetchAll();
  } catch (Throwable $e) {
    $error = $e->getMessage();
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mini App – PHP + MySQL + Flexbox</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Enlazamos los estilos CSS -->
  <link href="/assets/styles.css" rel="stylesheet">
</head>
<body>
  <div class="app">
    <!-- ==========================
         ENCABEZADO
    =========================== -->
    <header class="app__header">
      <div class="brand">Mini App</div>
      <nav class="nav">
        <!-- Menú de navegación -->
        <a href="/" class="nav__link">Inicio</a>
        <a href="#nuevo" class="nav__link">Nuevo</a>
        <a href="#lista" class="nav__link">Mensajes</a>
      </nav>
      <div class="user">Invitado</div>
    </header>

    <!-- ==========================
         CUERPO PRINCIPAL
    =========================== -->
    <div class="app__body">
      <!-- SIDEBAR -->
      <aside class="sidebar">
        <h3>Atajos</h3>
        <ul class="list">
          <li><a href="#nuevo">Agregar mensaje</a></li>
          <li><a href="#lista">Ver mensajes</a></li>
        </ul>
        <h3>Estado</h3>
        <p>
          <!-- Mostramos si la conexión a la BD fue correcta o no -->
          <?php if ($error): ?>
            <span class="badge badge--error">⚠︎ Error conexión</span>
          <?php else: ?>
            <span class="badge badge--ok">✔︎ DB OK</span>
          <?php endif; ?>
        </p>
      </aside>

      <!-- CONTENIDO -->
      <main class="content">
        <!-- Formulario para agregar mensajes -->
        <section id="nuevo" class="card">
          <h2>Agregar mensaje</h2>
          <form method="post" class="form">
            <label for="texto">Texto</label>
            <input id="texto" name="texto" type="text" placeholder="Escribe algo…" required>
            <button type="submit" class="btn">Guardar</button>
          </form>
          <?php if ($error): ?>
            <!-- Si hubo error, lo mostramos aquí -->
            <p class="help error">⚠︎ <?= htmlspecialchars($error) ?></p>
          <?php endif; ?>
        </section>

        <!-- Lista de mensajes guardados -->
        <section id="lista" class="card">
          <h2>Mensajes (<?= count($mensajes) ?>)</h2>
          <div class="cards">
            <?php if (empty($mensajes)): ?>
              <p class="muted">Aún no hay mensajes.</p>
            <?php else: ?>
              <?php foreach ($mensajes as $m): ?>
                <article class="msg">
                  <header class="msg__head">
                    <!-- ID del mensaje -->
                    <span class="msg__id">#<?= $m['id'] ?></span>
                    <!-- Para simplificar, ponemos "hoy". Luego se puede ampliar con fecha real -->
                    <time class="msg__time">hoy</time>
                  </header>
                  <!-- Texto del mensaje -->
                  <p class="msg__text"><?= htmlspecialchars($m['texto']) ?></p>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>
      </main>
    </div>

    <!-- ==========================
         PIE DE PÁGINA
    =========================== -->
    <footer class="app__footer">
      <small>© <?= date('Y') ?> Mini App de clase · Flexbox demo</small>
    </footer>
  </div>
</body>
</html>
