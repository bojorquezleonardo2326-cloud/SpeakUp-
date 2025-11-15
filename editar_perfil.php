<?php
include 'session.php'; // tu session.php no tiene $conn definido
include 'db1.php';     // aquí se define $conn

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Manejar actualización POST (igual que tu código anterior, solo cambiar DB a speakup_db)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = $_POST['first_name'];
    $last_name_p = $_POST['last_name_p'];
    $last_name_m = $_POST['last_name_m'];
    $age         = $_POST['age'];
    $location    = $_POST['location'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $gender      = $_POST['gender'];
    $grade       = $_POST['grade'];
    $password    = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
	 $message = "Perfil actualizado correctamente";
    $toast_type = "success";

    $avatar = $user['avatar'] ?? 'avatar_default.png';
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0){
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $avatar = "avatar_{$user_id}.".$ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar);
    }

    if ($password) {
        $sql = "UPDATE usuarios SET first_name=?, last_name_p=?, last_name_m=?, age=?, location=?, email=?, phone=?, gender=?, grade=?, password=?, avatar=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiisssissi", $first_name, $last_name_p, $last_name_m, $age, $location, $email, $phone, $gender, $grade, $password, $avatar, $user_id);
    } else {
        $sql = "UPDATE usuarios SET first_name=?, last_name_p=?, last_name_m=?, age=?, location=?, email=?, phone=?, gender=?, grade=?, avatar=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiisssisi", $first_name, $last_name_p, $last_name_m, $age, $location, $email, $phone, $gender, $grade, $avatar, $user_id);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: mi_perfil.php");
    exit;
}


// Obtener datos actualizados
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Perfil - SpeakUp</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;background:url('FondoSpeak.png') no-repeat center center fixed;background-size:cover;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;position:relative;color:#3b2f00;}
body::before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,247,205,0.3),rgba(255,243,190,0.25));backdrop-filter:blur(3px);z-index:0;}
.bubble{position:absolute;bottom:-150px;border-radius:50%;background:rgba(255,255,255,0.85);filter:drop-shadow(0 0 10px rgba(255,240,170,0.7));animation:floatUp 6s linear infinite;z-index:-1;}
.b1{width:50px;height:50px;left:5%;animation-delay:0s;}
.b2{width:30px;height:30px;left:25%;animation-delay:1s;}
.b3{width:60px;height:60px;left:50%;animation-delay:0.5s;}
.b4{width:40px;height:40px;left:70%;animation-delay:1.5s;}
.b5{width:50px;height:50px;left:85%;animation-delay:2s;}
@keyframes floatUp{0%{transform:translateY(0) scale(1);opacity:0.85;}50%{transform:translateY(-500px) scale(1.15);opacity:1;}100%{transform:translateY(-1000px) scale(1);opacity:0;}}
.container{background:rgba(255,255,255,0.95);border-radius:28px;padding:36px 40px;width:620px;max-width:calc(100%-32px);border:3px solid #FFD600;box-shadow:0 14px 40px rgba(255,204,60,0.25);position:relative;z-index:2;transition:.2s;}
.container:hover{transform:translateY(-3px);box-shadow:0 20px 50px rgba(255,200,70,0.3);}
h1{color:#FFB300;font-size:32px;font-weight:900;margin-bottom:22px;text-align:center;text-shadow:0 0 10px rgba(255,250,220,0.9);}
.logo {
    position: absolute;
    top: 18px;
    right: 22px;
    width: 100px; /* Antes era 60px */
    height: auto;
    cursor: pointer;
    z-index: 10;
    transition: transform .3s;
}

.logo:hover {
    transform: scale(1.15); /* Antes 1.1 */
}

.avatar-container{position:relative;display:flex;justify-content:center;margin-bottom:18px;}
.avatar-preview{width:120px;height:120px;border-radius:50%;border:3px solid #FFD600;object-fit:cover;transition:transform .3s ease;}
.upload-btn{position:absolute;bottom:0;right:calc(50% - 60px);background:#FFB300;border:none;border-radius:50%;width:38px;height:38px;display:flex;justify-content:center;align-items:center;cursor:pointer;box-shadow:0 3px 6px rgba(0,0,0,0.15);}
.upload-btn:hover{background:#FFD600;transform:scale(1.1);}
.upload-btn input{display:none;}
.upload-btn svg{fill:#4b2f00;width:22px;height:22px;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 16px;margin-bottom:10px;}
.field{display:flex;flex-direction:column;}
.field label{margin-bottom:6px;font-weight:700;color:#4b3a00;font-size:14px;}
.field input,.field select{width:100%;padding:12px;border-radius:12px;border:2px solid #FFD857;background:#FFF9D9;font-size:15px;outline:none;}
.field input:focus,.field select:focus{box-shadow:0 6px 18px rgba(255,210,80,0.45);}
.full-row{grid-column:1/-1;}
.actions{margin-top:12px;display:flex;flex-direction:column;gap:12px;}
button.primary,button.secondary{border:none;border-radius:14px;padding:14px;font-weight:800;font-size:16px;cursor:pointer;transition:all .2s;}
button.primary{background:linear-gradient(135deg,#FFD600,#FFB300);color:#4b2f00;}
button.primary:hover{transform:scale(1.05) rotate(-1deg);box-shadow:0 14px 36px rgba(255,200,70,0.34);}
button.secondary{background:#fff;color:#4b2f00;border:2px solid #FFD600;}
button.secondary:hover{background:#FFF9C6;transform:scale(1.03);}
.toast{position:fixed;top:25px;right:110px;padding:14px 22px;border-radius:12px;font-weight:700;box-shadow:0 6px 20px rgba(0,0,0,0.2);opacity:0;transform:translateY(-20px);transition:opacity .5s ease,transform .5s ease;z-index:9999;}
.toast.show{opacity:1;transform:translateY(0);}
.toast.success{background:#C9F191;color:#2B4200;}
.toast.error{background:#FFADAD;color:#4b2f00;}
@media(max-width:700px){.form-grid{grid-template-columns:1fr;}.container{padding:26px 20px;width:calc(100%-20px);}}
</style>
</head>
<body>
<div class="bubble b1"></div><div class="bubble b2"></div><div class="bubble b3"></div><div class="bubble b4"></div><div class="bubble b5"></div>

<img src="Logo_SpeakUp.png" class="logo" alt="Logo SpeakUp">

<div id="toast" class="toast"></div>

<div class="container">
    <h1>✨ Editar Perfil ✨</h1>
    <form method="POST" enctype="multipart/form-data" id="profileForm">
        <div class="avatar-container">
            <img id="avatarPreview" class="avatar-preview" src="<?= htmlspecialchars($user['avatar'] ?? 'avatar_default.png') ?>" alt="Avatar Usuario">
            <label class="upload-btn">
                <input type="file" name="avatar" id="avatarInput" accept="image/*">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 5c-1.1 0-2 .9-2 2H8c-1.1 0-2 .9-2 2v2H4v6h16v-6h-2V9c0-1.1-.9-2-2-2h-2c0-1.1-.9-2-2-2zm0 2a2 2 0 0 1 2 2h-4a2 2 0 0 1 2-2zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg>
            </label>
        </div>

        <div class="form-grid">
            <div class="field"><label>Nombre:</label><input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required></div>
            <div class="field"><label>Apellido Paterno:</label><input type="text" name="last_name_p" value="<?= htmlspecialchars($user['last_name_p']) ?>" required></div>
            <div class="field"><label>Apellido Materno:</label><input type="text" name="last_name_m" value="<?= htmlspecialchars($user['last_name_m']) ?>" required></div>
            <div class="field"><label>Edad:</label><input type="number" name="age" value="<?= htmlspecialchars($user['age']) ?>" required></div>
            <div class="field"><label>Ubicación:</label><input type="text" name="location" value="<?= htmlspecialchars($user['location']) ?>" required></div>
            <div class="field"><label>Email:</label><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></div>
            <div class="field"><label>Teléfono:</label><input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required></div>
            <div class="field"><label>Género:</label>
                <select name="gender" required>
                    <option value="M" <?= $user['gender']=='M'?'selected':'' ?>>Masculino</option>
                    <option value="F" <?= $user['gender']=='F'?'selected':'' ?>>Femenino</option>
                    <option value="O" <?= $user['gender']=='O'?'selected':'' ?>>Otro</option>
                </select>
            </div>
            <div class="field"><label>Grado:</label>
                <select name="grade" required>
                    <option value="1" <?= $user['grade']==1?'selected':'' ?>>1º de Primaria</option>
                    <option value="2" <?= $user['grade']==2?'selected':'' ?>>2º de Primaria</option>
                    <option value="3" <?= $user['grade']==3?'selected':'' ?>>3º de Primaria</option>
                    <option value="4" <?= $user['grade']==4?'selected':'' ?>>4º de Primaria</option>
                </select>
            </div>
            <div class="field full-row"><label>Nueva contraseña:</label><input type="password" name="password" placeholder="Opcional"></div>
        </div>
        <div class="actions">
            <button type="submit" class="primary">Guardar cambios</button>
            <button type="button" class="secondary" onclick="window.location.href='mi_perfil.php'">Cancelar</button>
        </div>
    </form>
</div>

<script>
// Avatar preview
const input = document.getElementById('avatarInput');
const preview = document.getElementById('avatarPreview');
input.addEventListener('change', () => {
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => preview.src = e.target.result;
    reader.readAsDataURL(file);
  }
});

// Toast
<?php if($message != ''): ?>
const toast = document.getElementById('toast');
toast.textContent = "<?= $message ?>";
toast.className = "toast show <?= $toast_type ?>";
setTimeout(()=>{
    toast.classList.remove("show");
    <?php if($toast_type==='success'): ?>
        window.location.href='PRUEBA.php';
    <?php endif; ?>
},2500);
<?php endif; ?>
</script>
</body>
</html>
