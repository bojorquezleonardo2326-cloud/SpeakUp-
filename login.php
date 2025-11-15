<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include("conexion.php"); 

    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Rellena todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            $error = "El correo no está registrado.";
        } else {
            $usuario = $resultado->fetch_assoc();
            if (!password_verify($password, $usuario["password"])) {
                $error = "Contraseña incorrecta.";
            } else {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['first_name'] = $usuario['first_name'];
                $_SESSION['last_name_p'] = $usuario['last_name_p'];
                $_SESSION['last_name_m'] = $usuario['last_name_m'];

                header("Location: Principalphp.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpeakUp! - Iniciar sesión</title>
<style>
body {
    margin: 0;
    font-family: "Poppins", sans-serif;
    background: url('FondoSpeak.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    animation: fadeIn 0.8s ease-in-out;
    color: #3b2f00;
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

.header {
    position: absolute;
    top: 20px;
    right: 30px;
    z-index: 100;
}
.header img { width: 140px; transition: transform 0.3s ease; }
.header img:hover { transform: scale(1.08) rotate(4deg); }

.main-container {
    display: flex;
    background: rgba(255, 255, 210, 0.95);
    border-radius: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    overflow: hidden;
    margin-top: 30px;
    animation: slideUp 0.8s ease;
}
@keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }

.left-panel {
    flex: 1;
    background: linear-gradient(135deg, #FFD600, #FFB300);
    color: #4b2f00;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
}
.left-panel .logo-circle {
    width: 180px;
    height: 180px;
    background: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.left-panel .logo-circle img {
    width: 140px;
    height: auto;
    border-radius: 50%;
    transition: transform 0.3s ease;
}
.left-panel .logo-circle img:hover { transform: scale(1.08); }
.left-panel h2 { font-size: 26px; margin-bottom: 15px; }
.left-panel p { font-size: 15px; line-height: 1.6; max-width: 300px; }

.right-panel {
    flex: 1;
    padding: 40px 35px;
    text-align: center;
}
.right-panel .form-logo {
    width: 120px;
    margin-bottom: 15px;
}
h1 { color: #4b2f00; margin-bottom: 25px; font-size: 24px; }

label { display: block; text-align: left; margin-top: 10px; margin-bottom: 5px; font-weight: 600; }
input[type="email"], input[type="password"] {
    width: 100%; padding: 12px; border-radius: 8px; border: 2px solid #FFD857; outline: none;
    margin-bottom: 15px; background: #FFF9D9;
}
input:focus { box-shadow: 0 0 8px rgba(255,200,70,0.3); }

button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background: linear-gradient(135deg, #FFD600, #FFB300);
    color: #4b2f00;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.links { margin-top: 15px; }
.links button {
    background: none;
    border: none;
    color: #4b2f00;
    text-decoration: underline;
    cursor: pointer;
    font-size: 14px;
}
.links button:hover { color: #FFD600; }

/* --- TOAST --- */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #FFD600;
    color: #4b2f00;
    padding: 14px 22px;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateY(-20px);
    transition: opacity 0.4s ease, transform 0.4s ease;
    z-index: 9999;
}
.toast.show {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 800px) {
    .main-container { flex-direction: column; width: 90%; }
    .left-panel { padding: 30px; }
    .right-panel { padding: 25px; }
}
</style>
</head>
<body>

<div class="header">
    <img src="Logo_SpeakUp.png" alt="Emblema SpeakUp!">
</div>

<div class="main-container">
    <div class="left-panel">
        <div class="logo-circle">
            <img src="Logo_SpeakUp.png" alt="Logo SpeakUp">
        </div>
        <h2>¡Aprende inglés con confianza!</h2>
        <p>SpeakUp! es tu espacio para mejorar tus habilidades de comunicación en inglés de manera dinámica y divertida. Practica conversación, vocabulario y pronunciación con lecciones interactivas diseñadas para todos los niveles.</p>
    </div>

    <div class="right-panel">
        <img src="Logo_SpeakUp.png" alt="Logo SpeakUp" class="form-logo">
        <h1>Inicia sesión</h1>

        <form method="POST">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Entrar</button>

            <div class="links">
                <button type="button" onclick="window.location.href='speakup_register.php'">
                    ¿No tienes cuenta? ¡Crea una!
                </button><br>
                <button type="button" onclick="window.location.href='contraseña_olvidada.php'">
                    ¿Olvidaste tu contraseña?
                </button>
            </div>
        </form>
    </div>
</div>
<div id="toast" class="toast"></div>

<script>
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

<?php if(!empty($error)): ?>
    showToast("<?= $error ?>");
<?php endif; ?>
</script>

</body>
</html>
