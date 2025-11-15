<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT first_name FROM usuarios WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$first_name = $user['first_name'];
$stmt2 = $conn->prepare("SELECT juego2 FROM puntajes WHERE user_id=?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$scoreData = $res2->fetch_assoc();
$currentScore = $scoreData ? $scoreData['juego2'] : 0;
$allPairs = [
    ["Bread", "Pan"], ["Rice", "Arroz"], ["Juice", "Jugo"], ["Water", "Agua"],
    ["Pencil", "Lápiz"], ["Eraser", "Goma"], ["Window", "Ventana"], ["Door", "Puerta"],
    ["Shirt", "Camisa"], ["Shoes", "Zapatos"], ["Cup", "Taza"], ["Plate", "Plato"]
];


shuffle($allPairs);
$gamePairs = array_slice($allPairs, 0, 8);

$cards = [];
foreach($gamePairs as $pair) {
    $cards[] = ["text"=>$pair[0], "pair"=>$pair[1]];
    $cards[] = ["text"=>$pair[1], "pair"=>$pair[0]];
}
shuffle($cards);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;700&display=swap" rel="stylesheet">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Memorama SpeakUp!</title>
<link rel="icon" href="Logo_SpeakUp.png">
<style>
@keyframes pop {
  0% { transform: scale(0); opacity:0; }
  50% { transform: scale(1.4); opacity:1; }
  100% { transform: scale(1); opacity:1; }
}

#countdown {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  display: none;
  justify-content: center;
  align-items: center;
  background: rgba(255, 243, 172, 0.97);
  z-index: 9998;
  font-size: 200px;
  font-weight: 900;
  color: #4b2f00;
  text-shadow: 0 0 25px #FFD600, 0 0 45px #FFB300;
  animation: fadeIn 0.3s ease-in-out;
}

#count-number {
  animation: pop 0.6s ease-in-out;
}

@keyframes pop {
  0% { transform: scale(0); opacity: 0; }
  50% { transform: scale(1.4); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

body {
    font-family:'Poppins',sans-serif;
    background: linear-gradient(to top,#FFF9D9,#FFE57F);
    display:flex; flex-direction:column; align-items:center; justify-content:flex-start;
    min-height:100vh; margin:0; padding:20px; color:#4b2f00; overflow-x:hidden;
    position:relative;
}

.bubble{position:absolute;bottom:-150px;border-radius:50%;background:rgba(255,255,255,0.6);animation:floatUp 6s linear infinite;}
@keyframes floatUp {0%{transform:translateY(0);opacity:0.7;}50%{transform:translateY(-500px);opacity:1;}100%{transform:translateY(-1000px);opacity:0;}}
.b1{width:50px;height:50px;left:5%;animation-delay:0s;}
.b2{width:30px;height:30px;left:25%;animation-delay:1s;}
.b3{width:60px;height:60px;left:50%;animation-delay:0.5s;}
.b4{width:40px;height:40px;left:70%;animation-delay:1.5s;}
.b5{width:50px;height:50px;left:85%;animation-delay:2s;}

#logo-container{
    position:relative;
    display:flex;
    flex-direction:column;
    align-items:center;
    z-index:10;
    margin-top:140px;
}
#logo{
    width:160px;
    cursor:pointer;
    transition:0.3s;
}
.toast{
    position:absolute;
    bottom:100%;
    margin-bottom:25px;
    background:#FFB300;
    padding:12px 20px;
    border-radius:12px;
    font-weight:700;
    color:#4b2f00;
    opacity:0;
    transition:opacity 0.5s, transform 0.5s;
    pointer-events:none;
    white-space: nowrap;
}
.toast.show{
    opacity:1;
    transform:translateX(-50%) translateY(-10px);
}
.card-container{display:grid;grid-template-columns:repeat(4,120px);grid-gap:15px;justify-content:center;margin-top:30px;}
.card{width:120px;height:80px;background:#FFD600;display:flex;align-items:center;justify-content:center;border-radius:12px;cursor:pointer;perspective:600px;position:relative;}
.card-inner{position:absolute;width:100%;height:100%;transition:transform 0.6s;transform-style:preserve-3d;border-radius:12px;}
.card.flipped .card-inner{transform:rotateY(180deg);}
.card-front, .card-back{position:absolute;width:100%;height:100%;backface-visibility:hidden;display:flex;align-items:center;justify-content:center;border-radius:12px;font-weight:bold;font-size:16px;}
.card-front{background:#FFD600;color:#fff;}
.card-back{background:#FFF9D9;transform:rotateY(180deg);}
.card.correct{box-shadow:0 0 15px #4CAF50; border:3px solid #4CAF50;}
.card.wrong{box-shadow:0 0 15px #f44336; border:3px solid #f44336; animation:shake 0.3s;}
@keyframes shake{0%{transform:translateX(0);}25%{transform:translateX(-5px);}50%{transform:translateX(5px);}75%{transform:translateX(-5px);}100%{transform:translateX(0);}}
#restart-container a {
    background: #FFB300;
    padding: 14px 26px;
    border-radius: 15px;
    font-weight: 700;
    color: #4b2f00;
    text-decoration: none;
    display: inline-block;
    margin-top: 30px;
    font-size: 20px;
    box-shadow: 0 4px 0 #d89a00;
    transition: all 0.2s ease-in-out;
}
#restart-container a:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 10px rgba(0,0,0,0.2);
    background: #FFD54F;
}

</style>
</head>
<body>

<div id="start-overlay" style="
    position:fixed; top:0; left:0; width:100%; height:100%;
   background:rgba(255, 243, 172, 0.92);
    display:flex; flex-direction:column; justify-content:center; align-items:center;
    z-index:9999; text-align:center; color:#4b2f00;">
    
    <img src="Logo_SpeakUp.png" style="width:140px; margin-bottom:15px;">

<h1 style="
    font-size:36px; 
    font-weight:700; 
    animation:bounce 1.5s infinite;
    padding:10px 25px;
    border-radius:20px;
    background:#FFB300;
    box-shadow:0 0 18px #FFDD67;
    border:3px solid #F7C843;
">
    Memorama 2dos
</h1>


    <button id="start-btn" style="
        margin-top:20px; padding:14px 28px; background:#FFB300;
        border:none; border-radius:15px; font-size:20px;
        cursor:pointer; font-weight:bold; color:#4b2f00;">
        ¡Empezar reto!
    </button>

    <a href="curso_2do.php" style="
    margin-top:18px; 
    background:#FFB300;
    padding:10px 20px;
    border-radius:12px;
    font-weight:700;
    color:#4b2f00;
    text-decoration:none;
    display:inline-block;
    box-shadow:0 4px 0 #d89a00;
    transition:0.2s;
">⬅ Regresar al inicio</a>

</div>

<style>
@keyframes bounce {
  0%,100% { transform:translateY(0); }
  50% { transform:translateY(-6px); }
}
</style>


<div id="countdown">
  <span id="count-number">3</span>
</div>


<div id="timer" style="
    font-size:22px; font-weight:bold;
    background:#FFB300; padding:8px 18px; border-radius:12px; margin-top:20px;
">Tiempo: 0s</div>

<div class="bubble b1"></div>
<div class="bubble b2"></div>
<div class="bubble b3"></div>
<div class="bubble b4"></div>
<div class="bubble b5"></div>

<div id="logo-container">
    <img src="Logo_SpeakUp.png" id="logo" alt="Logo SpeakUp">
    <div id="toast" class="toast"></div>
</div>

<h2 style="margin-top:20px; background:#FFB300; padding:10px 20px; border-radius:12px;">
    Puntaje actual: <b><?= $currentScore ?> pts</b>
</h2>

<div class="card-container">
<?php foreach($cards as $i=>$card): ?>
<div class="card" data-text="<?= htmlspecialchars($card['text']) ?>" data-pair="<?= htmlspecialchars($card['pair']) ?>">
    <div class="card-inner">
        <div class="card-front">?</div>
        <div class="card-back"><?= htmlspecialchars($card['text']) ?></div>
    </div>
</div>
<?php endforeach; ?>
</div>
<canvas id="confetti-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;"></canvas>

<div id="restart-container" style="display:none;margin-top:20px;">
    <a href="curso_2do.php" style="padding:12px 25px;background:#FFB300;color:#4b2f00;border-radius:12px;font-weight:bold;text-decoration:none;">Volver a los cursos</a>
</div>

<script>
const firstName = "<?= htmlspecialchars($first_name) ?>";
const cards = document.querySelectorAll('.card');
let flipped = [];
let lock = true; 
let pairsFound = 0;
const totalPairs = <?= count($gamePairs) ?>;

let startTime = null;
let timerInterval;

function updateTimer(){
    let now = Date.now();
    let elapsed = Math.floor((now - startTime) / 1000);
    document.getElementById('timer').textContent = `Tiempo: ${elapsed}s`;
}

document.getElementById('start-btn').onclick = () => {
    document.getElementById('start-overlay').style.display = 'none';
    const countdown = document.getElementById('countdown');
    const countNum = document.getElementById('count-number');
    countdown.style.display = 'flex';

    let count = 3;
    countNum.textContent = count;

    const interval = setInterval(() => {
        count--;
        if (count > 0) {
            countNum.textContent = count;
            countNum.style.animation = 'none'; void countNum.offsetWidth; // reinicia la animación
            countNum.style.animation = 'pop 0.6s ease-in-out';
        } else if (count === 0) {
            countNum.textContent = '¡Ya!';
            countNum.style.animation = 'none'; void countNum.offsetWidth;
            countNum.style.animation = 'pop 0.6s ease-in-out';
        } else {
            clearInterval(interval);
            countdown.style.display = 'none';
            lock = false;
            startTime = Date.now();
            timerInterval = setInterval(updateTimer, 1000);
        }
    }, 1000);
};



const motivacion = ["¡Muy bien!", "¡Excelente!", "¡Sigue así!", "¡Perfecto!", "¡Genial!"];

function showToast(msg){
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'),1500);
}

function showConfetti(){
    const canvas = document.getElementById('confetti-canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth; 
    canvas.height = window.innerHeight;

    const confs = [];
    for(let i = 0; i < 150; i++){
        confs.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            dx: Math.random() * 2 - 1,
            dy: Math.random() * 3 + 2,
            r: Math.random() * 6 + 2,
            color: `hsl(${Math.random()*50+40},100%,60%)`
        });
    }

    function animate(){
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        confs.forEach(c => {
            c.x += c.dx;
            c.y += c.dy;
            if(c.y > canvas.height) c.y = -10;
            if(c.x > canvas.width) c.x = 0;
            if(c.x < 0) c.x = canvas.width;
            ctx.fillStyle = c.color;
            ctx.beginPath();
            ctx.arc(c.x, c.y, c.r, 0, 2 * Math.PI);
            ctx.fill();
        });
        requestAnimationFrame(animate);
    }
    animate();
}
function sonidoBien(){
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const o = ctx.createOscillator();
    const g = ctx.createGain();
    o.frequency.value = 700; 
    o.type = "triangle";
    o.connect(g);
    g.connect(ctx.destination);
    o.start();
    g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
    o.stop(ctx.currentTime + 0.4);
}

function sonidoMal(){
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const o = ctx.createOscillator();
    const g = ctx.createGain();
    o.frequency.value = 200; 
    o.type = "square";
    o.connect(g);
    g.connect(ctx.destination);
    o.start();
    g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
    o.stop(ctx.currentTime + 0.4);
}

cards.forEach(card=>{
    card.addEventListener('click',()=>{
        if(lock || card.classList.contains('flipped')) return;
        card.classList.add('flipped');
        flipped.push(card);

        if(flipped.length===2){
            lock=true;
            const [c1,c2] = flipped;
if(c1.dataset.text===c2.dataset.pair || c2.dataset.text===c1.dataset.pair){
    c1.classList.add('correct');
    c2.classList.add('correct');
    sonidoBien();
    pairsFound++;
    showToast(motivacion[Math.floor(Math.random()*motivacion.length)]);
    flipped=[];
    lock=false;

    if(pairsFound === totalPairs){
        clearInterval(timerInterval);

        let tiempo = Math.floor((Date.now() - startTime) / 1000);
        let puntaje = Math.max(100 - Math.floor(tiempo / 2), 10);

fetch("guardar_memorama_2do.php", { 
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "puntaje=" + puntaje
});


        showConfetti();
        document.getElementById('restart-container').style.display = 'block';
        showToast(`¡Felicidades ${firstName}! Tiempo: ${tiempo}s • Puntaje: ${puntaje} 🎉`);
    }

} else {
    sonidoMal();
    setTimeout(()=>{
        c1.classList.remove('flipped');
        c2.classList.remove('flipped');
        flipped=[];
        lock=false;
    },800);
}

        }
    });
});
</script>

</body>
</html>
