<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit("No user logged");
}

$user_id = $_SESSION['user_id'];

// Recibir el puntaje que mandaste por fetch
$puntaje = isset($_POST['puntaje']) ? intval($_POST['puntaje']) : 0;

// Actualizar SOLO la columna memorama_1ro
$stmt = $conn->prepare("UPDATE puntajes SET memorama_1ro = ? WHERE user_id = ?");
$stmt->bind_param("ii", $puntaje, $user_id);
$stmt->execute();

echo "ok";
