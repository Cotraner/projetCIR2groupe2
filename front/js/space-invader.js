document.addEventListener("DOMContentLoaded", () => {// Initialisation du jeu Konami
    const container = document.getElementById("jeu-konami");

    const canvas = document.createElement("canvas");
    canvas.width = 860;
    canvas.height = 560;
    container.appendChild(canvas);

    const ctx = canvas.getContext("2d");

    // Dimensions fixes
    const PLAYER_WIDTH = 50;
    const PLAYER_HEIGHT = 80;
    const ALIEN_WIDTH = 50;
    const ALIEN_HEIGHT = 80;
    const BONUS_SIZE = 70;

    const player = {// Position et vitesse du joueur
        x: canvas.width / 2 - PLAYER_WIDTH / 2,
        y: canvas.height - PLAYER_HEIGHT - 10,
        speed: 5
    };

    let bullets = [];
    let enemies = [];
    let score = 0;
    let lives = 3;
    let isGameOver = false;
    let level = 1;
    let bonus = null;
    let keys = {};

    let stars = Array.from({ length: 100 }, () => ({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        size: Math.random() * 2 + 1,
        speed: Math.random() * 0.5 + 0.2
    }));

    let enemyDirection = 1;
    let enemySpeed = 0.5;

    // Sprites
    const imgVaisseau = new Image();
    imgVaisseau.src = "../../images/vaisseau.png";

    const imgAlien = new Image();
    imgAlien.src = "../../images/alien.png";

    const imgHeart = new Image();
    imgHeart.src = "../../images/heart.png";

    function initEnemies() {
        enemies = [];
        for (let row = 0; row < 3; row++) {
            for (let col = 0; col < 8; col++) {
                enemies.push({
                    x: 60 + col * 60,
                    y: 40 + row * 40,
                    alive: true
                });
            }
        }
    }

    initEnemies();

    document.addEventListener("keydown", e => {// Gestion des touches
        if (["ArrowLeft", "ArrowRight", " "].includes(e.key)) {
            e.preventDefault();
        }

        keys[e.key] = true;

        if (e.key === " " && bullets.length < 3) {
            bullets.push({
                x: player.x + PLAYER_WIDTH / 2 - 2,
                y: player.y,
                width: 4,
                height: 10,
                speed: 6
            });
        }
    });

    document.addEventListener("keyup", e => {// Gestion des touches
        keys[e.key] = false;
    });

    function updateStars() {
        stars.forEach(s => {// Mise à jour de la position des étoiles
            s.y += s.speed;
            if (s.y > canvas.height) s.y = 0;
        });
    }

    function checkCollision(a, b) {// Vérification de collision entre deux objets
        const bw = b.width || ALIEN_WIDTH;
        const bh = b.height || ALIEN_HEIGHT;
        return (
            a.x < b.x + bw &&
            a.x + (a.width || 0) > b.x &&
            a.y < b.y + bh &&
            a.y + (a.height || 0) > b.y
        );
    }

    function spawnBonus(x, y) {// Génération d'un bonus
        bonus = { x, y, speed: 1.5 };
    }

    function updateEnemies() {// Mise à jour de la position des ennemis
        let shouldReverse = false;

        enemies.forEach(e => {
            if (!e.alive) return;
            e.x += enemySpeed * enemyDirection;

            if (e.x <= 0 || e.x + ALIEN_WIDTH >= canvas.width) {
                shouldReverse = true;
            }
        });

        if (shouldReverse) {
            enemies.forEach(e => {// Inversion de la direction des ennemis
                e.y += 20;
                if (e.alive && e.y + ALIEN_HEIGHT >= player.y) {
                    lives--;
                    if (lives <= 0) isGameOver = true;
                }
            });
            enemyDirection *= -1;
        }
    }

    function update() {// Mise à jour de l'état du jeu
        if (isGameOver) return;

        if (keys["ArrowLeft"] && player.x > 0) player.x -= player.speed;
        if (keys["ArrowRight"] && player.x < canvas.width - PLAYER_WIDTH) player.x += player.speed;

        bullets.forEach(b => b.y -= b.speed);
        bullets = bullets.filter(b => b.y > -10);

        enemies.forEach(enemy => {
            bullets.forEach(b => {
                if (enemy.alive && checkCollision(b, enemy)) {
                    enemy.alive = false;
                    b.y = -10;
                    score++;
                    if (Math.random() < 0.1) spawnBonus(enemy.x, enemy.y);
                }
            });
        });

        if (bonus) {// Mise à jour de la position du bonus
            bonus.y += bonus.speed;
            if (checkCollision(bonus, player)) {
                lives++;
                bonus = null;
            } else if (bonus.y > canvas.height) {
                bonus = null;
            }
        }

        if (enemies.every(e => !e.alive)) {// Vérification si tous les ennemis sont détruits
            level++;
            enemySpeed = 0.5 + level * 0.2;
            initEnemies();
        }

        updateEnemies();
        updateStars();
    }

    function draw() {// Dessin des éléments sur le canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = "black";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Étoiles
        ctx.fillStyle = "white";
        stars.forEach(s => {
            ctx.beginPath();
            ctx.arc(s.x, s.y, s.size, 0, Math.PI * 2);
            ctx.fill();
        });

        // Joueur
        ctx.drawImage(imgVaisseau, player.x, player.y, PLAYER_WIDTH, PLAYER_HEIGHT);

        // Tirs
        ctx.fillStyle = "#F3A829";
        bullets.forEach(b => ctx.fillRect(b.x, b.y, b.width, b.height));

        // Ennemis
        enemies.forEach(e => {
            if (e.alive) {
                ctx.drawImage(imgAlien, e.x, e.y, ALIEN_WIDTH, ALIEN_HEIGHT);
            }
        });

        // Bonus
        if (bonus) {
            ctx.drawImage(imgHeart, bonus.x, bonus.y, BONUS_SIZE, BONUS_SIZE);
        }

        // HUD
        ctx.fillStyle = "#fff";
        ctx.font = "16px Itim, cursive";
        ctx.fillText("Score : " + score, 10, 20);
        ctx.fillText("Vies : " + lives, canvas.width - 100, 20);
        ctx.fillText("Niveau : " + level, canvas.width / 2 - 40, 20);

        if (isGameOver) {// Affichage du message de fin de jeu
            ctx.fillStyle = "black";
            ctx.font = "40px Itim, cursive";
            ctx.fillText("GAME OVER", canvas.width / 2 - 120, canvas.height / 2);
        }
    }

    function loop() {// Boucle principale du jeu
        update();
        draw();
        requestAnimationFrame(loop);
    }

    loop();
});
