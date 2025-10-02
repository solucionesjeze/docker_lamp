<?php 
$host = 'db'; 
$db   = getenv('MYSQL_DATABASE') ?: 'appdb'; 
$user = getenv('MYSQL_USER') ?: 'appuser'; 
$pass = getenv('MYSQL_PASSWORD') ?: 'app1234'; 
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4"; 
 
try { 
  $pdo = new PDO($dsn, $user, $pass, [ 
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
  ]); 
  $stmt = $pdo->query("SELECT COUNT(*) AS total FROM mensajes"); 
  $row = $stmt->fetch(); 
  echo "<h1>Apache + PHP + MySQL en Docker</h1>"; 
  echo "<p>Conexión OK. Mensajes en la base: <strong>{$row['total']}</strong></p>"; 
} catch (Throwable $e) { 
  http_response_code(500); 
  echo "<h1>Error conectando a MySQL</h1>"; 
  echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>"; 
}
#Comentario para github