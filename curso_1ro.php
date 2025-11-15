<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$first_name = $user['first_name'];

$messages = [
    "List@ para el siguiente reto, $first_name?",
    "¿Cuál es nuestro siguiente desafío, $first_name?",
    "¡Vamos a aprender algo nuevo, $first_name!",
    "¿Preparad@ para el próximo reto, $first_name?"
];
$random_msg = $messages[array_rand($messages)];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>1er Grado - Cursos de Inglés</title>
<style>
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: url('FondoSpeak.png') no-repeat center center fixed;
    background-size: cover;
    color: #4b2f00;
}

/* Barra superior simple con logo */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffffff;
    padding: 10px 30px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.navbar img.logo { height: 50px; }

.navbar button {
    background: linear-gradient(145deg, #FFD600 0%, #FFB300 100%);
    border: none;
    border-radius: 20px;
    padding: 6px 14px; /* más delgado */
    cursor: pointer;
    font-weight: 700;
    font-size: 14px;
    box-shadow: inset 0 -3px 0 rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.navbar button:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: inset 0 -3px 0 rgba(0,0,0,0.15), 0 8px 15px rgba(0,0,0,0.25);
}

/* Título centrado */
.hero {
    text-align: center;
    margin: 30px 20px;
}

.hero h1 {
    font-size: 48px;
    font-weight: 900;
    color: #FFB300;
    text-shadow: 2px 2px 10px rgba(75,47,0,0.5);
    margin-bottom: 15px;
}

.hero p {
    font-size: 22px;
    margin-bottom: 40px;
}

/* Animación de entrada desde abajo */
@keyframes riseUp {
    0% { transform: translateY(40px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

/* Grid de cursos: 2 columnas x 3 filas */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(3, 140px);
    gap: 25px;
    padding: 0 20px 50px 20px;
}

/* Botones de cursos estilizados */
.course-card {
    position: relative;
    border-radius: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    font-weight: 800;
    cursor: pointer;
    color: #fff;
    transition: transform 0.4s, box-shadow 0.4s, background 0.4s;
    text-align: center;
    padding: 12px 18px; /* más delgado */
    letter-spacing: 1px;
    box-shadow: inset 0 -4px 0 rgba(0,0,0,0.1), 0 6px 15px rgba(0,0,0,0.2);
    opacity: 0;
    animation: riseUp 0.6s forwards;
}

/* Cada botón entra con delay */
.course-card:nth-child(1) { animation-delay: 0.1s; background: linear-gradient(145deg,#FFD600,#FFB300); }
.course-card:nth-child(2) { animation-delay: 0.3s; background: linear-gradient(145deg,#FFB300,#FFD600); }
.course-card:nth-child(3) { animation-delay: 0.5s; background: linear-gradient(145deg,#FFC95C,#FF9F1C); }
.course-card:nth-child(4) { animation-delay: 0.7s; background: linear-gradient(145deg,#FFDA6C,#FFD166); }
.course-card:nth-child(5) { animation-delay: 0.9s; background: linear-gradient(145deg,#FFE27A,#FFD166); }
.course-card:nth-child(6) { animation-delay: 1.1s; background: linear-gradient(145deg,#FFD966,#FFB347); }

/* Hover único */
.course-card:hover {
    transform: translateY(-8px) scale(1.07);
    box-shadow: inset 0 -4px 0 rgba(0,0,0,0.1), 0 12px 25px rgba(0,0,0,0.3);
}

/* Responsive */
@media(max-width:700px){
    .hero h1 { font-size:36px; }
    .hero p { font-size:18px; }
    .course-card { font-size:18px; height:120px; padding:10px 14px; }
    .courses-grid { grid-template-columns: 1fr; grid-template-rows: repeat(6,120px); }
}
</style>
</head>
<body>

<div class="navbar">
    <img src="Logo_SpeakUp.png" alt="Logo SpeakUp!" class="logo">
    <button onclick="window.location.href='principalphp.php'">Volver al inicio</button>
</div>

<div class="hero">
    <h1>1er Grado</h1>
    <p><?php echo htmlspecialchars($random_msg); ?></p>
</div>

<div class="courses-grid">
    <div class="course-card" onclick="window.location.href='memorama1.php'">Memorama</div>
    <div class="course-card" onclick="window.location.href='trivia1.php'">Trivia</div>
    <div class="course-card" onclick="window.location.href='colores1.php'">Colores</div>
    <div class="course-card" onclick="window.location.href='vocales1.php'">Vocales</div>
    <div class="course-card" onclick="window.location.href='numeros1.php'">Números</div>
    <div class="course-card" onclick="window.location.href='animales1.php'">Animales</div>
</div>

</body>
</html>
