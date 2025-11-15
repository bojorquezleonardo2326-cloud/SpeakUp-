<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) exit('No logueado');

$user_id = $_SESSION['user_id'];
$puntaje = isset($_POST['puntaje']) ? (int)$_POST['puntaje'] : 0;

$stmt = $conn->prepare("UPDATE puntajes SET juego1 = GREATEST(juego1, ?) WHERE user_id=?");
$stmt->bind_param("ii", $puntaje, $user_id);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO puntajes (user_id, juego1) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $puntaje);
    $stmt->execute();
}

echo 'OK';
?>
