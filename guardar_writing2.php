<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "No autorizado";
    exit();
}

$user_id = $_SESSION['user_id'];

if(!isset($_POST['puntaje'])){
    echo "No hay puntaje";
    exit();
}

$puntaje = (int)$_POST['puntaje'];

$stmt = $conn->prepare("SELECT writing2 FROM puntajes WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if($data){
    if($puntaje > $data['writing2']){
        $stmt2 = $conn->prepare("UPDATE puntajes SET writing2=? WHERE user_id=?");
        $stmt2->bind_param("ii", $puntaje, $user_id);
        $stmt2->execute();
    }
} else {
    $stmt2 = $conn->prepare("INSERT INTO puntajes (user_id, writing2) VALUES (?, ?)");
    $stmt2->bind_param("ii", $user_id, $puntaje);
    $stmt2->execute();
}

echo "ok";
