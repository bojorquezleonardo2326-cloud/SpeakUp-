
<?php
$lifetime = 60 * 60 * 24 * 365 * 10;
ini_set('session.gc_maxlifetime', $lifetime);
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
session_regenerate_id(true);
include 'db.php';
$user_id = $_SESSION['user_id'] ?? null;

$user = null;
if ($user_id) {
    $sql = "SELECT first_name, last_name_p, last_name_m, grade FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

$first_name = $user['first_name'] ?? 'Usuario';
$last_name_p = $user['last_name_p'] ?? '';
$grade = $user['grade'] ?? 0;

$full_name = $user ? htmlspecialchars($user['first_name'] . " " . $user['last_name_p']) : 'Usuario';


switch((int)$grade){
    case 1: $course_link = "cursos_1ro.php"; break;
    case 2: $course_link = "cursos_2do.php"; break;
    case 3: $course_link = "cursos_3ro.php"; break;
    case 4: $course_link = "cursos_4to.php"; break;
    default: $course_link = "cursos.php";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpeakUp! - Inicio</title>
<style>
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: url('FondoSpeak.png') no-repeat center center fixed;
    background-size: cover;
    color: #4b2f00;
    overflow-x: hidden;
}


@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}
@keyframes shine {
    0% { background-position: -200px 0; }
    100% { background-position: 200px 0; }
}
@keyframes bubbleFloat {
    0% { transform: translateY(0) scale(1); opacity: 0.8; }
    50% { opacity: 1; }
    100% { transform: translateY(-500px) scale(1.2); opacity: 0; }
}


.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffffffee;
    padding: 15px 30px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    backdrop-filter: blur(8px);
    border-bottom: 3px solid #FFD600;
}
.navbar img { height: 50px; }
.nav-links { display: flex; align-items: center; gap: 18px; }
.nav-links button {
    background: none;
    border: none;
    font-weight: 600;
    cursor: pointer;
    font-size: 15px;
    transition: 0.3s;
    color: #4b2f00;
}
.nav-links button:hover {
    color: #FFB300;
    transform: scale(1.05);
    text-shadow: 0 0 10px #FFD600aa;
}
.nav-profile {
    width: 45px; height: 45px;
    border-radius: 50%; overflow: hidden; cursor: pointer;
    border: 2px solid #FFD600;
    box-shadow: 0 0 10px #FFD60088;
    transition: 0.3s;
}
.nav-profile:hover { transform: scale(1.1); }


.hero {
    position: relative;
    text-align: center;
    color: #4b2f00;
    height: 370px;
    background: linear-gradient(135deg, #FFD600cc, #FFB300cc), url('FondoHero.jpg') center/cover no-repeat;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-bottom-left-radius: 40px;
    border-bottom-right-radius: 40px;
    padding: 0 20px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    animation: fadeInUp 1s ease forwards;
    overflow: hidden;
}

.bubble {
    position: absolute;
    bottom: -100px;
    border-radius: 50%;
    opacity: 0.7;
    background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,214,0,0.6) 70%);
    animation: bubbleFloat 10s linear infinite;
}
.bubble:nth-child(1) { left: 10%; width: 40px; height: 40px; animation-delay: 1s; }
.bubble:nth-child(2) { left: 25%; width: 25px; height: 25px; animation-delay: 3s; }
.bubble:nth-child(3) { left: 45%; width: 50px; height: 50px; animation-delay: 2s; }
.bubble:nth-child(4) { left: 65%; width: 35px; height: 35px; animation-delay: 4s; }
.bubble:nth-child(5) { left: 80%; width: 60px; height: 60px; animation-delay: 0s; }


.hero::after {
    content: "";
    position: absolute;
    top: 0; left: -100%;
    width: 50%; height: 100%;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.6) 50%, transparent 100%);
    animation: shine 5s infinite linear;
}
.hero h1 {
    font-size: 40px;
    margin-bottom: 10px;
    text-shadow: 2px 2px 8px rgba(75,47,0,0.4);
    animation: float 5s ease-in-out infinite;
}
.hero p {
    font-size: 19px;
    max-width: 600px;
    text-shadow: 1px 1px 6px rgba(75,47,0,0.4);
}
.hero button {
    margin-top: 25px;
    padding: 14px 32px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg,#FFD600,#FFB300);
    color: #4b2f00;
    font-weight: bold;
    font-size: 17px;
    cursor: pointer;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    position: relative;
    overflow: hidden;
}
.hero button::before {
    content: "";
    position: absolute;
    top: 0; left: -75px;
    width: 50px; height: 100%;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,0.6), transparent);
    transform: skewX(-20deg);
    transition: 0.6s;
}
.hero button:hover::before { left: 120%; }
.hero button:hover {
    transform: scale(1.08);
    box-shadow: 0 0 20px #FFD600aa;
}


.table-section { padding: 50px 20px; text-align: center; animation: fadeInUp 1s ease forwards; }
.table-section h2 { font-size: 28px; margin-bottom: 30px; }
.grades-table {
    margin: 0 auto; width: 75%;
    display: grid; grid-template-columns: repeat(2,1fr);
    grid-template-rows: repeat(2,200px);
    gap: 25px;
}
.grade-card {
    background: #ffffffcc; backdrop-filter: blur(10px);
    border-radius: 22px; display: flex;
    justify-content: center; align-items: center;
    font-size: 24px; font-weight: bold;
    cursor: pointer; position: relative;
    overflow: hidden; transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.grade-card:hover { transform: scale(1.05); box-shadow: 0 0 20px #FFD600aa; }
.grade-card img {
    position: absolute; top: 0; left: 0;
    width: 100%; height: 100%; object-fit: cover;
    border-radius: 22px; opacity: 0;
    transition: opacity 0.4s ease-in-out; z-index: 1;
}
.grade-card span { position: relative; z-index: 2; pointer-events: none; }
.grade-card:hover img { opacity: 1; }


.why-section {
    padding: 50px 20px; background: #fff8cc;
    text-align: center; animation: fadeInUp 1s ease forwards;
}
.why-section h2 { font-size: 28px; margin-bottom: 25px; }
.why-cards { display: flex; justify-content: center; flex-wrap: wrap; gap: 25px; }
.why-card {
    background: #ffffffcc; backdrop-filter: blur(10px);
    border-radius: 20px; padding: 25px;
    width: 250px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s; animation: float 6s ease-in-out infinite;
}
.why-card:hover { transform: translateY(-5px) scale(1.03); box-shadow: 0 0 20px #FFD600aa; }


footer {
    background: linear-gradient(135deg,#FFD600,#FFB300);
    color: #4b2f00; padding: 30px 20px;
    text-align: center; border-top-left-radius: 30px;
    border-top-right-radius: 30px; box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
}
footer a { color: #4b2f00; margin: 0 10px; font-size: 20px; transition: 0.2s; }
footer a:hover { transform: scale(1.2); text-shadow: 0 0 10px #FFB300; }
footer p { margin-top: 15px; font-size: 14px; }


@media(max-width:900px){
    .grades-table { grid-template-columns: 1fr; width: 90%; }
    .navbar { flex-direction: column; gap: 10px; }
    .hero h1 { font-size: 30px; }
}
</style>
</head>
<body>

<div class="navbar">
    <img src="Logo_SpeakUp.png" alt="Logo SpeakUp!">
    <div class="nav-links">
        <button onclick="window.location.href='principalphp.php'">Inicio</button>
        <button onclick="window.location.href='mi_perfil.php'">Mi perfil</button>
        <button onclick="window.location.href='cursos.php'">Cursos</button>
        <button onclick="window.location.href='mi_progreso.php'">Mi Progreso</button>
        <button onclick="window.location.href='contacto.php'">Contacto</button>
        <button onclick="window.location.href='login.php'">Cerrar sesión</button>
        <div class="nav-profile" onclick="window.location.href='mi_perfil.php'">
            <img src="avatar_default.png" alt="Avatar Usuario">
        </div>
    </div>
</div>

<div class="hero">
    <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
    <div class="bubble"></div><div class="bubble"></div>

    <h1>¡Bienvenid@, <?= htmlspecialchars($full_name); ?>!</h1>
    <p>Aprende inglés jugando y divirtiéndote con nuestros cursos gratuitos de SpeakUp! para primaria.</p>
    <button onclick="window.location.href='<?= $course_link ?>'">Explora los cursos</button>
</div>

<div class="table-section">
    <h2>¿Qué grado eres?</h2>
    <div class="grades-table">
        <div class="grade-card" onclick="window.location.href='cursos_1ro.php'">
            <img src="Grado1.png" alt="1ro"><span>1ro de Primaria</span>
        </div>
        <div class="grade-card" onclick="window.location.href='cursos_2do.php'">
            <img src="Grado2.png" alt="2do"><span>2do de Primaria</span>
        </div>
        <div class="grade-card" onclick="window.location.href='cursos_3ro.php'">
            <img src="Grado3.png" alt="3ro"><span>3ro de Primaria</span>
        </div>
        <div class="grade-card" onclick="window.location.href='cursos_4to.php'">
            <img src="Grado4 .png" alt="4to"><span>4to de Primaria</span>
        </div>
    </div>
</div>

<div class="why-section">
    <h2>¿Por qué aprender con SpeakUp!?</h2>
    <div class="why-cards">
        <div class="why-card">
            <h3>🆓 Gratis y divertido</h3>
            <p>Todos nuestros cursos son gratuitos y diseñados para que los niños disfruten aprendiendo inglés.</p>
        </div>
        <div class="why-card">
            <h3>🎮 Aprende jugando</h3>
            <p>Lecciones basadas en actividades lúdicas que hacen que cada palabra cobre vida.</p>
        </div>
        <div class="why-card">
            <h3>🧠 Lecciones cortas, grandes resultados</h3>
            <p>Diseñadas para mantener la atención y el ritmo de aprendizaje ideal para niños.</p>
        </div>
    </div>
</div>

<footer>
    <p>Síguenos en nuestras redes:</p>
    <a href="#">📘</a>
    <a href="#">📺</a>
    <a href="#">🔗</a>
    <a href="#">🐦</a>
    <p>© 2025 SpeakUp!. Todos los derechos reservados.</p>
</footer>
</body>
</html>
