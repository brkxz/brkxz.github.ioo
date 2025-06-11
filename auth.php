<?php
session_start();

// Datos conexión MySQL en XAMPP
$host = 'localhost';
$db   = 'bd_22';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

// Captura los datos del formulario
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Usuario y contraseña son obligatorios';
    header('Location: login.php');
    exit;
}

// Consulta para obtener el usuario
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE nombre = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

// Verificar contraseña (compatible con hash y texto plano para migración)
if ($user) {
    $password_valid = false;
    
    // Si la contraseña está hasheada, usar password_verify
    if (password_get_info($user['passwor'])['algo']) {
        $password_valid = password_verify($password, $user['passwor']);
    } else {
        // Para compatibilidad con contraseñas en texto plano existentes
        $password_valid = ($user['passwor'] === $password);
        
        // Actualizar a hash para mayor seguridad
        if ($password_valid) {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare('UPDATE usuarios SET passwor = ? WHERE id = ?');
            $update_stmt->execute([$new_hash, $user['id']]);
        }
    }
    
    if ($password_valid) {
        $_SESSION['user'] = $username;
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    }
}

$_SESSION['error'] = 'Usuario o contraseña incorrectos';
header('Location: login.php');
exit;