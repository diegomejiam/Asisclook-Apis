<?php
header('Content-Type: application/json');

/*este es una conexion que lo puedes utilizar si lo quieres probra en xampp pero solo utilizalo 
si deseas si no dejalo asi en comentario*/

/*$host = "localhost";
$user = "root";
$pass = "";
$db = "asistapp";*/

$host = 'localhost';
$db = 'asistapp';
$user = 'ivanportador';
$pass = 'Mintario153';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

$userId = $_GET['user_id'] ?? null;
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? "Todos";

if (!$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta el ID de usuario']);
    exit;
}

if ($month === "Todos") {
    $stmt = $pdo->prepare("
        SELECT fecha_registro AS date, hora_registro AS time, tipo AS type
        FROM attendance
        WHERE referenced_id = ? AND YEAR(fecha_registro) = ?
        ORDER BY fecha_registro DESC, hora_registro DESC
    ");
    $stmt->execute([$userId, $year]);

} else {
    $stmt = $pdo->prepare("
        SELECT fecha_registro AS date, hora_registro AS time, tipo AS type
        FROM attendance
        WHERE referenced_id = ? 
        AND YEAR(fecha_registro) = ?
        AND MONTH(fecha_registro) = ?
        ORDER BY fecha_registro DESC, hora_registro DESC
    ");

    $stmt->execute([$userId, $year, $month]);
}

$records = $stmt->fetchAll();
echo json_encode($records);
?>