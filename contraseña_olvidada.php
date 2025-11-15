<?php
$host = 'localhost';
$db   = 'speakup_db';
$user = 'root';
$pass = '';  
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
$message = '';
$email_error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(strlen($new_password) < 6){
        $message = "La contraseña debe tener al menos 6 caracteres.";
    } elseif($new_password !== $confirm_password){
        $message = "Las contraseñas no coinciden.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password_hash = :password_hash WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':password_hash' => $password_hash, ':email' => $email]);

        if($stmt->rowCount() > 0){
            $message = "¡Contraseña actualizada con éxito! Serás redirigido al login...";
        } else {
            $email_error = "Este correo no está registrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpeakUp! - Recuperar contraseña</title>
<link rel="icon" href="Logo_SpeakUp.png">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Poppins', sans-serif;
    background: url('FondoSpeak.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    position: relative;
    color: #3b2f00;
    overflow-x: hidden;
}
body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(255,247,205,0.3), rgba(255,243,190,0.25));
    backdrop-filter: blur(3px);
    z-index: 0;
}
.bubble { position: absolute; bottom: -150px; border-radius: 50%; background: rgba(255,255,255,0.85); filter: drop-shadow(0 0 10px rgba(255,240,170,0.7)); animation: floatUp 6s linear infinite; z-index: -1; }
.b1{width:50px;height:50px;left:5%; animation-delay:0s;}
.b2{width:30px;height:30px;left:25%; animation-delay:1s;}
.b3{width:60px;height:60px;left:50%; animation-delay:0.5s;}
.b4{width:40px;height:40px;left:70%; animation-delay:1.5s;}
.b5{width:50px;height:50px;left:85%; animation-delay:2s;}
@keyframes floatUp {0% { transform: translateY(0) scale(1); opacity: 0.85; } 50% { transform: translateY(-500px) scale(1.15); opacity: 1; } 100% { transform: translateY(-1000px) scale(1); opacity: 0; }}

.header { width: 100%; display: flex; justify-content: flex-end; padding: 18px 30px 0 30px; z-index: 2; position: relative; }
.header img { width: 200px; transition: transform 0.3s, filter 0.3s; }
.header img:hover { transform: scale(1.15) rotate(-5deg); filter: drop-shadow(0 0 25px #FFD600); }

.wrap { display: flex; justify-content: center; align-items: flex-start; padding: 26px 12px 60px 12px; z-index: 2; width: 100%; }
.container { background: rgba(255,255,255,0.95); border-radius: 28px; padding: 36px 40px; width: 420px; max-width: calc(100% - 32px); border: 3px solid #FFD600; box-shadow: 0 14px 40px rgba(255,204,60,0.25); position: relative; text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.container:hover { transform: translateY(-3px); box-shadow: 0 20px 50px rgba(255,200,70,0.3); }

h1 { color: #FFB300; font-size: 28px; font-weight: 900; margin-bottom: 22px; text-shadow: 0 0 10px rgba(255,250,220,0.9); }

input[type="email"], input[type="text"] {
    width: 100%; padding: 12px; border-radius: 12px; border: 2px solid #FFD857; background: #FFF9D9; font-size: 15px; outline: none; margin-bottom: 15px;
}
input:focus { box-shadow: 0 6px 18px rgba(255,210,80,0.45); transform: translateY(-1px); }

button { width: 100%; padding: 14px; margin-top: 5px; background: linear-gradient(135deg,#FFD600,#FFB300); color: #4b2f00; border: none; border-radius: 14px; font-weight: 800; font-size: 16px; cursor: pointer; transition: transform .2s ease, box-shadow .2s ease; }
button:hover { transform: scale(1.05) rotate(-1deg); box-shadow: 0 14px 36px rgba(255,200,70,0.34); }

.text-button { background: none; border: none; color: #FFB300; text-decoration: underline; cursor: pointer; font-weight: 700; margin-top: 10px; }
.text-button:hover { color: #FFD600; }

.error-msg { color:red; font-size:13px; margin-bottom:6px; }

.toast { position: fixed; top: 20px; right: 20px; background: #FFD600; color: #4b2f00; padding: 14px 22px; border-radius: 12px; font-weight: 700; box-shadow: 0 6px 20px rgba(0,0,0,0.2); opacity: 0; transform: translateY(-20px); transition: opacity 0.4s ease, transform 0.4s ease; z-index: 9999; }
.toast.show { opacity: 1; transform: translateY(0); }

@media (max-width: 500px) { .container { width: 90%; padding: 25px; } .header img { width: 150px; } }
</style>
</head>
<body>

<div class="bubble b1"></div>
<div class="bubble b2"></div>
<div class="bubble b3"></div>
<div class="bubble b4"></div>
<div class="bubble b5"></div>

<div class="header">
    <img src="Logo_SpeakUp.png" alt="Logo SpeakUp">
</div>

<div class="wrap">
    <div class="container">
        <h1>🔑 Recuperar contraseña</h1>

        <form method="POST">
            <label for="email">Correo registrado:</label>
            <input type="email" id="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            <?php if($email_error != ''): ?>
                <div class="error-msg"><?= $email_error ?></div>
            <?php endif; ?>

            <label for="new_password">Nueva contraseña:</label>
            <input type="text" id="new_password" name="new_password" required minlength="6">

            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="text" id="confirm_password" name="confirm_password" required minlength="6">

            <button type="submit">Actualizar contraseña</button>
            <button type="button" class="text-button" onclick="window.location.href='login.php'">Regresar al inicio</button>
        </form>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 2000);
}

<?php if($message != ''): ?>
showToast("<?= $message ?>");
setTimeout(() => { window.location.href='login.php'; }, 2000);
<?php endif; ?>
</script>

</body>
</html>
