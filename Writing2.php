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

$stmt2 = $conn->prepare("SELECT writing1 FROM puntajes WHERE user_id=?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$scoreData = $res2->fetch_assoc();
$currentScore = $scoreData ? $scoreData['writing1'] : 0;
$questions = [
    ['sentence' => 'The (⚽) is round', 'answer' => 'ball'],
    ['sentence' => 'The sky is (💙)', 'answer' => 'blue'],
    ['sentence' => 'I can (🏃‍♂️)', 'answer' => 'run'],
    ['sentence' => 'I see a (🦋)', 'answer' => 'butterfly'],
    ['sentence' => 'I like (🍎)', 'answer' => 'apples'],
    ['sentence' => 'A cow gives (🥛)', 'answer' => 'milk'],
    ['sentence' => 'The sun is (🌞)', 'answer' => 'happy'],
    ['sentence' => 'I have (👀)', 'answer' => 'eyes'],
    ['sentence' => 'This is my (👩‍🏫)', 'answer' => 'teacher'],
    ['sentence' => 'The grass is (💚)', 'answer' => 'green']
];
shuffle($questions);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Writing 1 - SpeakUp!</title>
<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;700&display=swap" rel="stylesheet">
<style>
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

body {font-family:'Comic Neue',sans-serif;background:linear-gradient(to top,#FFF9D9,#FFE57F);margin:0;padding:20px;text-align:center;overflow:hidden;}
.bubble{position:absolute;bottom:-150px;border-radius:50%;background:rgba(255,255,255,0.6);animation:floatUp 6s linear infinite;}
@keyframes floatUp {0%{transform:translateY(0);opacity:0.7;}50%{transform:translateY(-500px);opacity:1;}100%{transform:translateY(-1000px);opacity:0;}}
.b1{width:50px;height:50px;left:5%;animation-delay:0s;} .b2{width:30px;height:30px;left:25%;animation-delay:1s;} .b3{width:60px;height:60px;left:50%;animation-delay:0.5s;} .b4{width:40px;height:40px;left:70%;animation-delay:1.5s;} .b5{width:50px;height:50px;left:85%;animation-delay:2s;}
#word-box{font-size:45px;margin:30px 0;}
input{padding:10px;font-size:18px;border-radius:8px;border:2px solid #FFD600;text-align:center;transition:box-shadow 0.3s;}
button{padding:10px 20px;font-size:18px;background:#FFD600;border:none;border-radius:10px;cursor:pointer;margin-left:10px;}
.progress-container{width:50%;background:#eee;margin:20px auto;border-radius:10px;}
.progress-bar{height:20px;width:0%;background:#FFEB3B;border-radius:10px;transition:width 0.5s;}
.toast{position:absolute;top:80px;left:50%;transform:translateX(-50%);background:#FFB300;padding:12px 20px;border-radius:12px;font-weight:700;color:#4b2f00;opacity:0;transition:opacity 0.5s,transform 0.5s;pointer-events:none;}
.toast.show{opacity:1;transform:translateX(-50%) translateY(-10px);}
#confetti-canvas{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;}
#timer{font-size:20px;font-weight:bold;background:#FFB300;padding:8px 15px;border-radius:10px;display:inline-block;margin-top:20px;}
#start-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,243,172,0.92);display:flex;flex-direction:column;justify-content:center;align-items:center;z-index:9999;text-align:center;color:#4b2f00;}
#start-overlay h1{font-size:36px;font-weight:700;animation:bounce 1.5s infinite;padding:10px 25px;border-radius:20px;background:#FFB300;box-shadow:0 0 18px #FFDD67;border:3px solid #F7C843;}
@keyframes bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);}}

#countdown {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    display: flex;
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
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
#count-number {
    animation: pop 0.6s ease-in-out;
}
@keyframes pop {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.4); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
</head>
<body>

<h2>Puntaje actual: <b><?= $currentScore ?> pts</b></h2>

<div id="start-overlay">
    <img src="Logo_SpeakUp.png" style="width:140px;margin-bottom:15px;">
    <h1>Writing 2 - SpeakUp!</h1>
    <button id="start-btn" style="margin-top:20px;padding:14px 28px;background:#FFB300;border:none;border-radius:15px;font-size:20px;cursor:pointer;font-weight:bold;color:#4b2f00;">¡Empezar reto!</button>
    <a href="cursos_2do.php" style="margin-top:18px;background:#FFB300;padding:10px 20px;border-radius:12px;font-weight:700;color:#4b2f00;text-decoration:none;display:inline-block;box-shadow:0 4px 0 #d89a00;transition:0.2s;">⬅ Regresar al inicio</a>
</div>

<div id="countdown"><span id="count-number">3</span></div>

<div id="timer">Tiempo: 0s</div>
<div class="bubble b1"></div><div class="bubble b2"></div><div class="bubble b3"></div><div class="bubble b4"></div><div class="bubble b5"></div>

<h2 style="margin-top:50px;">Completa la frase en inglés...</h2>
<div id="word-box"></div>
<input type="text" id="answer-input" autocomplete="off" placeholder="Escribe la palabra faltante">
<p id="hint"></p>
<button id="submit-btn">Enviar</button>

<div class="progress-container"><div class="progress-bar" id="progress-bar"></div></div>
<canvas id="confetti-canvas"></canvas>
<div id="restart-container" style="display:none;"><a href="cursos_2do.php">Volver a cursos</a></div>
<div class="toast" id="toast"></div>

<script>
const questions = <?= json_encode($questions) ?>;
let current = 0, startTime, timerInterval, correctCount = 0, errors = 0;
const answerInput = document.getElementById('answer-input');
const progressBar = document.getElementById('progress-bar');
const toast = document.getElementById('toast');
const motivacion = ["¡Buen trabajo!", "¡Sigue así!", "¡Lo estás haciendo genial!", "¡Perfecto!", "¡Excelente!"];
const hints = ["Casi!!, empieza con ","Más cerca!!, se escribe: ","Ya casi!!, sigue así: "];

function sonidoBien(){
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const o = ctx.createOscillator(), g = ctx.createGain();
    o.frequency.value = 700; o.type="triangle";
    o.connect(g); g.connect(ctx.destination);
    o.start(); g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
    o.stop(ctx.currentTime + 0.4);
}
function sonidoMal(){
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const o = ctx.createOscillator(), g = ctx.createGain();
    o.frequency.value = 200; o.type="square";
    o.connect(g); g.connect(ctx.destination);
    o.start(); g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
    o.stop(ctx.currentTime + 0.4);
}

function updateTimer(){
    const now = Date.now();
    const elapsed = Math.floor((now - startTime) / 1000);
    document.getElementById('timer').textContent = `Tiempo: ${elapsed}s`;
}
function showToast(msg){
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'),1500);
}
function showConfetti(){
    const canvas = document.getElementById('confetti-canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const colors = ['#FFF176', '#FFEB3B', '#FFD600', '#FFB300', '#FFF59D', '#F4FF81', '#C6FF00'];
    const confs = [];
    for(let i = 0; i < 150; i++){
        confs.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            dx: Math.random() * 2 - 1,
            dy: Math.random() * 3 + 1,
            r: Math.random() * 6 + 3,
            color: colors[Math.floor(Math.random() * colors.length)],
            alpha: Math.random() * 0.8 + 0.4
        });
    }
    function animate(){
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        confs.forEach(c => {
            c.x += c.dx; c.y += c.dy;
            if (c.y > canvas.height) c.y = -10;
            if (c.x > canvas.width) c.x = 0;
            if (c.x < 0) c.x = canvas.width;
            ctx.beginPath();
            ctx.arc(c.x, c.y, c.r, 0, 2 * Math.PI);
            ctx.fillStyle = c.color;
            ctx.globalAlpha = c.alpha;
            ctx.fill();
            ctx.globalAlpha = 1;
        });
        requestAnimationFrame(animate);
    }
    animate();
}

function showQuestion(){
    if(current>=questions.length) return;
    const q=questions[current];
    document.getElementById('word-box').textContent=q.sentence;
    answerInput.value=''; errors=0;
    document.getElementById('hint').textContent='';
}
function updateProgress(){
    progressBar.style.width=`${(correctCount/questions.length)*100}%`;
}

document.getElementById('start-btn').onclick=()=>{
    document.getElementById('start-overlay').style.display='none';
    let count=3;
    const cd=document.getElementById('countdown');
    cd.style.display='flex'; cd.textContent=count;
    const interval=setInterval(()=>{
        cd.style.animation='none'; void cd.offsetWidth; cd.style.animation='pop 0.6s ease-in-out';
        count--; cd.textContent=count>0?count:"¡Ya!";
        if(count<0){
            clearInterval(interval);
            cd.style.display='none';
            startTime=Date.now();
            timerInterval=setInterval(updateTimer,1000);
            showQuestion();
        }
    },1000);
};

document.getElementById('submit-btn').onclick = () => {
    if (current >= questions.length) return;
    const q = questions[current];
    const ans = answerInput.value.toLowerCase().trim();

    if (ans === q.answer) {
        sonidoBien();
        answerInput.style.boxShadow = "0 0 15px 4px #4CAF50";
        setTimeout(() => answerInput.style.boxShadow = '', 1000);
        correctCount++; 
        updateProgress();
        showToast(motivacion[Math.floor(Math.random() * motivacion.length)]);
        current++;

        if (current >= questions.length) {
            clearInterval(timerInterval);
            showConfetti();
            const tiempo = Math.floor((Date.now() - startTime) / 1000);
            const puntaje = Math.max(100 - Math.floor(tiempo / 2), 10);
            fetch("guardar_writing1.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "puntaje=" + puntaje
            });
            document.getElementById('restart-container').style.display = 'block';
            showToast(`¡Felicidades ${puntaje} pts! Tiempo: ${tiempo}s 🎉`);
        } else {
            showQuestion();
        }
    } else {
        sonidoMal();
        const revealed = q.answer.substr(0, Math.min(errors + 1, q.answer.length));
        document.getElementById('hint').textContent = `Pista: ${revealed}...`;
        errors++;
    }
};
</script>

</body>
</html>
