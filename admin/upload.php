<?php
/**
 * Open Group — Image Upload Handler
 */
require_once __DIR__ . '/config.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

if (!isset($_FILES['image'])) {
    echo json_encode(['error' => 'No se recibió ningún archivo.']);
    exit;
}

$result = handle_upload($_FILES['image']);
echo json_encode($result);
