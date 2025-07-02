<?php
require_once __DIR__ . '/vendor/autoload.php';

$host = '127.0.0.1';
$port = '3306';
$dbname = 'mantenimiento_tractores';
$user = 'root';
$password = '1234567'; // Tu password de MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}