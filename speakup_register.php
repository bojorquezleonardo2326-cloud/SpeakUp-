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
$toast_type = '';
$email_error = '';
$form_data = [
    'first_name'=>'',
    'last_name_p'=>'',
    'last_name_m'=>'',
    'age'=>'',
    'location'=>'',
    'email'=>'',
    'phone'=>'',
    'gender'=>'',
    'grade'=>''
];
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['first_name'] = trim($_POST['first_name']);
    $form_data['last_name_p'] = trim($_POST['last_name_p']);
    $form_data['last_name_m'] = trim($_POST['last_name_m']);
    $form_data['age'] = (int) $_POST['age'];
    $form_data['location'] = trim($_POST['location']);
    $form_data['email'] = trim($_POST['email']);
    $form_data['phone'] = trim($_POST['phone']);
    $form_data['gender'] = $_POST['gender'];
    $form_data['grade'] = (int) $_POST['grade'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if(strlen($password) < 6){
        $message = "La contraseña debe tener al menos 6 caracteres.";
        $toast_type = 'error';
    } elseif($password !== $confirm_password){
        $message = "Las contraseñas no coinciden.";
        $toast_type = 'error';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios 
            (first_name,last_name_p,last_name_m,age,location,email,phone,gender,grade,password)
            VALUES (:first_name,:last_name_p,:last_name_m,:age,:location,:email,:phone,:gender,:grade,:password)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                ':first_name'=>$form_data['first_name'],
                ':last_name_p'=>$form_data['last_name_p'],
                ':last_name_m'=>$form_data['last_name_m'],
                ':age'=>$form_data['age'],
                ':location'=>$form_data['location'],
                ':email'=>$form_data['email'],
                ':phone'=>$form_data['phone'],
                ':gender'=>$form_data['gender'],
                ':grade'=>$form_data['grade'],
                ':password'=>$password_hash
            ]);
            $message = "¡Cuenta creada con éxito! Redirigiendo al login...";
            $toast_type = 'success';
            $form_data = [
                'first_name'=>'','last_name_p'=>'','last_name_m'=>'','age'=>'',
                'location'=>'','email'=>'','phone'=>'','gender'=>'','grade'=>''
            ];
        } catch(PDOException $e) {
            if($e->getCode() == 23000 && !empty($form_data['email'])){
                $message = "Este correo ya está registrado.";
            } else {
                $message = "Error: " . $e->getMessage();
            }
            $toast_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpeakUp! - Crear cuenta</title>
<link rel="icon" href="Logo_SpeakUp.png">
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body {
    font-family:'Poppins',sans-serif;
    background:url('FondoSpeak.png') no-repeat center center fixed;
    background-size:cover;
    display:flex;flex-direction:column;align-items:center;
    min-height:100vh;position:relative;color:#3b2f00;overflow-x:hidden;
}
body::before {
    content:"";position:absolute;inset:0;
    background:linear-gradient(180deg, rgba(255,247,205,0.3), rgba(255,243,190,0.25));
    backdrop-filter: blur(3px); z-index:0;
}
.bubble{position:absolute;bottom:-150px;border-radius:50%;background:rgba(255,255,255,0.85);filter: drop-shadow(0 0 10px rgba(255,240,170,0.7)); animation:floatUp 6s linear infinite; z-index:-1;}
.b1{width:50px;height:50px;left:5%;animation-delay:0s;}
.b2{width:30px;height:30px;left:25%;animation-delay:1s;}
.b3{width:60px;height:60px;left:50%;animation-delay:0.5s;}
.b4{width:40px;height:40px;left:70%;animation-delay:1.5s;}
.b5{width:50px;height:50px;left:85%;animation-delay:2s;}
@keyframes floatUp {0%{transform:translateY(0) scale(1);opacity:0.85;}50%{transform:translateY(-500px) scale(1.15);opacity:1;}100%{transform:translateY(-1000px) scale(1);opacity:0;}}
.header{width:100%;display:flex;justify-content:flex-end;padding:18px 30px 0 30px;z-index:2;position:relative;}
.logo-wrap img{width:200px;transition: transform 0.3s, filter 0.3s;}
.logo-wrap img:hover{transform:scale(1.15) rotate(-5deg);filter:drop-shadow(0 0 25px #FFD600);}
.wrap{display:flex;justify-content:center;align-items:flex-start;padding:26px 12px 60px 12px;z-index:2;width:100%;}
.container{background:rgba(255,255,255,0.95);border-radius:28px;padding:36px 40px;width:620px;max-width:calc(100%-32px);border:3px solid #FFD600;box-shadow:0 14px 40px rgba(255,204,60,0.25);position:relative;transition: transform 0.2s ease, box-shadow 0.2s ease;}
.container:hover{transform:translateY(-3px);box-shadow:0 20px 50px rgba(255,200,70,0.3);}
h1{color:#FFB300;font-size:32px;font-weight:900;margin-bottom:22px;text-align:center;text-shadow:0 0 10px rgba(255,250,220,0.9);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 16px;margin-bottom:10px;}
.field{display:flex;flex-direction:column;}
.field label{margin-bottom:6px;font-weight:700;color:#4b3a00;font-size:14px;}
.field input,.field select{width:100%;padding:12px;border-radius:12px;border:2px solid #FFD857;background:#FFF9D9;font-size:15px;outline:none;}
.field input:focus,.field select:focus{box-shadow:0 6px 18px rgba(255,210,80,0.45);transform:translateY(-1px);}
.full-row{grid-column:1/-1;}
.actions{margin-top:12px;display:flex;flex-direction:column;gap:12px;}
button.primary{background:linear-gradient(135deg,#FFD600,#FFB300);color:#4b2f00;border:none;border-radius:14px;padding:14px;font-weight:800;font-size:16px;cursor:pointer;}
button.primary:hover{transform:scale(1.05) rotate(-1deg);box-shadow:0 14px 36px rgba(255,200,70,0.34);}
button.link{background:none;border:none;color:#FFB300;text-decoration:underline;cursor:pointer;font-weight:700;}
.small-note{color:#7a6730;font-size:13px;margin-top:6px;text-align:center;}
.toast{position:fixed;top:20px;right:20px;padding:14px 22px;border-radius:12px;font-weight:700;box-shadow:0 6px 20px rgba(0,0,0,0.2);opacity:0;transform:translateY(-20px);transition:opacity 0.4s ease, transform 0.4s ease;z-index:9999;}
.toast.show{opacity:1;transform:translateY(0);}
.toast.success{background:#C9F191;color:#2B4200;}
.toast.error{background:#FFADAD;color:#4b2f00;}
@media(max-width:700px){.form-grid{grid-template-columns:1fr;}.header{padding:12px 18px 0 18px;}.header img{width:150px;}.container{padding:26px 20px;width:calc(100%-20px);}}
</style>
</head>
<body>

<div class="bubble b1"></div>
<div class="bubble b2"></div>
<div class="bubble b3"></div>
<div class="bubble b4"></div>
<div class="bubble b5"></div>

<div class="header">
    <div class="logo-wrap">
        <img src="Logo_SpeakUp.png" alt="Logo SpeakUp">
    </div>
</div>

<div class="wrap">
    <div class="container">
        <h1>✨ Crear nueva cuenta ✨</h1>
        <form id="registerForm" method="POST" autocomplete="off">
            <div class="form-grid">
                <div class="field">
                    <label for="first_name">Nombre(s):</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Ej. Sofía" value="<?= htmlspecialchars($form_data['first_name']) ?>">
                </div>
                <div class="field">
                    <label for="last_name_p">Apellido Paterno:</label>
                    <input type="text" id="last_name_p" name="last_name_p" required placeholder="Ej. López" value="<?= htmlspecialchars($form_data['last_name_p']) ?>">
                </div>
                <div class="field">
                    <label for="last_name_m">Apellido Materno:</label>
                    <input type="text" id="last_name_m" name="last_name_m" required placeholder="Ej. Martínez" value="<?= htmlspecialchars($form_data['last_name_m']) ?>">
                </div>
                <div class="field">
                    <label for="age">Edad:</label>
                    <input type="number" id="age" name="age" min="3" max="120" required placeholder="Ej. 9" value="<?= htmlspecialchars($form_data['age']) ?>">
                </div>
                <div class="field">
                    <label for="location">Localidad:</label>
                    <input type="text" id="location" name="location" required placeholder="Ej. Ciudad de México" value="<?= htmlspecialchars($form_data['location']) ?>">
                </div>
                <div class="field full-row">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com" value="<?= htmlspecialchars($form_data['email']) ?>">
                </div>
                <div class="field">
                    <label for="phone">Teléfono:</label>
                    <input type="tel" id="phone" name="phone" required placeholder="Ej. 55 1234 5678" value="<?= htmlspecialchars($form_data['phone']) ?>">
                </div>
                <div class="field">
                    <label for="gender">Género:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Selecciona</option>
                        <option value="masculino" <?= $form_data['gender']=='masculino'?'selected':'' ?>>Masculino</option>
                        <option value="femenino" <?= $form_data['gender']=='femenino'?'selected':'' ?>>Femenino</option>
                        <option value="otro" <?= $form_data['gender']=='otro'?'selected':'' ?>>Otro</option>
                    </select>
                </div>
                <div class="field">
                    <label for="grade">Grado:</label>
                    <select id="grade" name="grade" required>
                        <option value="">Selecciona</option>
                        <option value="1" <?= $form_data['grade']=='1'?'selected':'' ?>>1º de Primaria</option>
                        <option value="2" <?= $form_data['grade']=='2'?'selected':'' ?>>2º de Primaria</option>
                        <option value="3" <?= $form_data['grade']=='3'?'selected':'' ?>>3º de Primaria</option>
                        <option value="4" <?= $form_data['grade']=='4'?'selected':'' ?>>4º de Primaria</option>
                    </select>
                </div>

                <div class="field full-row">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres" minlength="6">
                </div>
                <div class="field full-row">
                    <label for="confirm_password">Confirmar contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repite la contraseña" minlength="6">
                </div>
            </div>
            <div class="actions">
                <button type="submit" class="primary" id="createBtn">💫 Crear cuenta</button>
                <button type="button" class="link" id="backBtn">← Regresar al inicio</button>
                <div class="small-note">Al registrarte aceptas nuestras políticas de privacidad 🔐</div>
            </div>
        </form>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
function showToast(message, type='error', redirect=false){
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show '+type;
    setTimeout(()=>{
        toast.classList.remove('show');
        if(type==='success' && redirect){
            window.location.href='login.php';
        }
    },2500);
}
<?php if($message != ''): ?>
showToast("<?= $message ?>", "<?= $toast_type ?>", <?= $toast_type==='success'?'true':'false' ?>);
<?php endif; ?>

const clickSound = new Audio('click.mp3');
document.getElementById('backBtn').addEventListener('click', ()=>{
    clickSound.currentTime=0; clickSound.play();
    setTimeout(()=>window.location.href='login.php',200);
});
document.getElementById('registerForm').addEventListener('submit',function(e){
    clickSound.currentTime=0; clickSound.play();
});
</script>

</body>
</html>
