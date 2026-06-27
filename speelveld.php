<?php
require_once('hj3_db.php');
// Controleer of de gebruiker netjes is ingelogd via login.php
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit; }

// Bepaal de rol van de ingelogde gebruiker
$is_host = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? true : false;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 3 - Multiplayer Arena</title>
    <style>
        /* Premium Mobiel Design (Zwart, Rood, Neon-Oranje) */
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #0b0c10; color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #160c13 0%, #0b0c10 100%); padding: 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 35px rgba(0,0,0,0.8); text-align: center; }
        .logo { font-size: 32px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        
        .role-badge { font-size: 11px; font-weight: bold; color: #ff9500; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 10px; }
        .user-welcome { font-size: 14px; color: #b3b3b3; margin-bottom: 5px; }

        /* 2x2 Grid voor mobiel */
        .cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin: 15px 0; width: 100%; }
        .game-card { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); height: 130px; border-radius: 18px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 10px; box-sizing: border-box; cursor: pointer; position: relative; overflow: hidden; transition: all 0.2s ease; }
        .game-card.active { border-color: #ff2d55; box-shadow: 0 4px 15px rgba(255, 45, 85, 0.2); }
        .game-card.active:active { transform: scale(0.95); background: rgba(255, 45, 85, 0.1); }
        .card-badge { position: absolute; top: 8px; font-size: 9px; font-weight: bold; color: #4f4f4f; text-transform: uppercase; }
        .game-card.active .card-badge { color: #ff9500; }
        .card-text { font-size: 13px; font-weight: 800; color: rgba(255,255,255,0.15); line-height: 1.3; word-break: break-word; }
        .game-card.active .card-text { color: #ffffff; }
        .reveal-details { display: none; font-size: 10px; margin-top: 5px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 4px; color: #b3b3b3; width: 100%; }

        /* Resultaat kleuren */
        .card-correct { background: linear-gradient(135deg, #122c16 0%, #0b1a0d 100%) !important; border-color: #00ffcc !important; box-shadow: 0 0 20px rgba(0, 255, 204, 0.4) !important; }
        .card-correct .card-text { color: #00ffcc !important; }
        .card-wrong { background: linear-gradient(135deg, #2c1212 0%, #1a0b0b 100%) !important; border-color: #ff2d55 !important; box-shadow: 0 0 15px rgba(255, 45, 85, 0.3) !important; opacity: 0.5; }

        /* Bediening, Timer & VU Meter */
        .play-box { margin: 15px 0; }
        .btn-audio { width: 100%; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; text-transform: uppercase; background: linear-gradient(90deg, #ff2d55, #ff9500); color: white; box-shadow: 0 6px 20px rgba(255, 45, 85, 0.35); transition: all 0.2s; }
        .btn-audio:active { transform: scale(0.97); }
        
        .timer-container { width: 100%; background-color: #222; height: 6px; border-radius: 3px; margin-top: 15px; overflow: hidden; display: none; }
        .timer-bar { width: 100%; height: 100%; background: linear-gradient(90deg, #ff9500, #ff2d55); transition: width 0.1s linear; }

        .vu-meter { display: flex; justify-content: center; align-items: flex-end; gap: 4px; height: 24px; margin-top: 15px; }
        .vu-bar { width: 6px; height: 3px; background: linear-gradient(0deg, #ff9500 0%, #ff2d55 100%); border-radius: 2px; transition: height 0.1s ease; }
        .footer { font-size: 11px; color: #4f4f4f; text-align: center; letter-spacing: 1px; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <div>
            <h1 class="logo">HitJam <span>3</span></h1>
            <div class="user-welcome">Sessie: <strong><?php echo $_SESSION['username']; ?></strong></div>
            <div class="role-badge"><?php echo $is_host ? "👑 SPELLEIDER (HOST)" : "🎮 SPELER"; ?></div>
            <div style="font-size: 14px; color: #ff9500; font-weight: bold; text-transform: uppercase;" id="instruction-text">Wachten op de host...</div>
        </div>

        <!-- Kaarten Grid -->
        <div class="cards-grid">
            <div class="game-card" id="card-0" onclick="kiesKaart(0)"><div class="card-badge">A</div><div class="card-text">?</div><div class="reveal-details" id="reveal-0"></div></div>
            <div class="game-card" id="card-1" onclick="kiesKaart(1)"><div class="card-badge">B</div><div class="card-text">?</div><div class="reveal-details" id="reveal-1"></div></div>
            <div class="game-card" id="card-2" onclick="kiesKaart(2)"><div class="card-badge">C</div><div class="card-text">?</div><div class="reveal-details" id="reveal-2"></div></div>
            <div class="game-card" id="card-3" onclick="kiesKaart(3)"><div class="card-badge">D</div><div class="card-text">?</div><div class="reveal-details" id="reveal-3"></div></div>
        </div>

        <div class="play-box">
            <!-- Toon de startknop ALLEEN als de gebruiker de Host/Admin is! -->
            <?php if ($is_host): ?>
                <button class="btn-audio" id="btn-start">⚡ START VOLGENDE RONDE</button>
            <?php else: ?>
                <div style="color: #4f4f4f; font-size: 13px; font-weight: bold; text-transform: uppercase;">Luister live mee via de boxen...</div>
            <?php endif; ?>
            
            <div class="timer-container" id="timer-box">
                <div class="timer-bar" id="timer-bar"></div>
            </div>

            <div class="vu-meter">
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
            </div>
        </div>

        <div class="footer">HITJAM 3 LOCAL MULTIPLAYER</div>
    </div>
    <script>
        // Neem de host-rol over vanuit PHP naar JavaScript
        const isHost = <?php echo $is_host ? 'true' : 'false'; ?>;
        
        let audioPlayer = null;
        let rondeGegevens = null;
        let indexJuisteAntwoord = null;
        let vuInterval = null;
        let timerTimeout = null;
        let timerInterval = null;
        let gameActief = false;

        // Als de gebruiker de Host is, mag hij de startknop bedienen
        if (isHost) {
            document.getElementById('btn-start').addEventListener('click', activeerNieuweRondeViaPi);
        }

        // 1. DE MOTOR: Host drukt op start -> activeert het PHP script op de Pi
        function activeerNieuweRondeViaPi() {
            document.getElementById('btn-start').innerText = "⏳ GEGEVENS IMPORTEREN...";
            
            // We roepen het backend script aan dat een willekeurige song kiest en in game_status zet
            fetch('hj3_get_next_song.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log("Nieuw nummer succesvol geactiveerd in de Pi database!");
                    } else {
                        alert("Fout bij activeren: " + data.message);
                        document.getElementById('btn-start').innerText = "⚡ START VOLGENDE RONDE";
                    }
                });
        }

        // Houd bij welk nummer er nu op de telefoon van deze speler draait
        let lokaalSongId = 0; 

        // Start een timer die ELKE SECONDE (1000 ms) geruisloos aan de Pi vraagt wat de status is
        setInterval(controleerSpelStatusMetPi, 1000);

        function controleerSpelStatusMetPi() {
            fetch('hj3_check_sync.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Is het nummer op de Pi anders dan wat deze telefoon nu laat zien?
                        // Dan betekent het dat de Host een nieuwe ronde is gestart!
                        if (data.current_song_id !== lokaalSongId && data.current_song_id > 0) {
                            
                            console.log("🚀 Nieuw nummer ontdekt op de Pi! ID: " + data.current_song_id);
                            lokaalSongId = data.current_song_id; // Update onze lokale status
                            
                            // Reset het scherm en stop oude muziek
                            if (audioPlayer) { audioPlayer.pause(); }
                            stopVuMeter();
                            resetKaarten();

                            // Haal direct de speeldata op (preview_url, jaar, artiest, titel)
                            fetch('hj3_get_next_song.php')
                                .then(res => res.json())
                                .then(songData => {
                                    if (songData.status === 'success') {
                                        rondeGegevens = songData;
                                        gameActief = true;

                                        if (isHost) {
                                            document.getElementById('btn-start').innerText = "⚡ RONDE IS ACTIEF";
                                        }
                                        
                                        // Speel de muziek af op de telefoon van de speler
                                        audioPlayer = new Audio(songData.preview_url);
                                        audioPlayer.play().catch(e => {
                                            console.log("Autoplay geblokkeert door browser. Klik op het scherm.");
                                        });

                                        // Start de animaties, timers en kaartenmix!
                                        startVuMeter();
                                        bouwGemixtKaarten();
                                        startMultiplayerTimer();
                                    }
                                });
                        }
                    }
                });
        }


        // 3. GAMEPLAY LOGICA (Zelfde stabiele werking als voorheen)
        function bouwGemixtKaarten() {
            document.getElementById('instruction-text').innerText = "💥 VIND DE JUISTE MATCH!";

            const winnendType = Math.floor(Math.random() * 3);
            indexJuisteAntwoord = Math.floor(Math.random() * 4);

            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                const tekstElement = kaart.querySelector('.card-text');
                kaart.classList.add('active');

                if (i === indexJuisteAntwoord) {
                    if (winnendType === 0) tekstElement.innerText = rondeGegevens.year.toString();
                    else if (winnendType === 1) tekstElement.innerText = rondeGegevens.artist;
                    else tekstElement.innerText = rondeGegevens.title;
                } else {
                    const randomType = Math.floor(Math.random() * 3);
                    if (randomType === 0) {
                        const afwijking = (Math.floor(Math.random() * 14) - 7);
                        tekstElement.innerText = (parseInt(rondeGegevens.year) + (afwijking === 0 ? 3 : afwijking)).toString();
                    } else if (randomType === 1) {
                        const nepArtiesten = ["Linkin Park", "Dua Lipa", "Eminem", "Tiësto", "Rammstein", "Coldplay", "Rihanna"];
                        tekstElement.innerText = nepArtiesten[Math.floor(Math.random() * nepArtiesten.length)];
                    } else {
                        const nepTitels = ["In The End", "Levitating", "Lose Yourself", "The Business", "Du Hast", "Yellow", "Umbrella"];
                        tekstElement.innerText = nepTitels[Math.floor(Math.random() * nepTitels.length)];
                    }
                }
            }
        }

        function kiesKaart(gekozenIndex) {
            if (!gameActief || indexJuisteAntwoord === null) return;
            gameActief = false;

            clearTimeout(timerTimeout);
            clearInterval(timerInterval);

            // De Full Onthulling
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.classList.remove('active');
                const revealDiv = document.getElementById(`reveal-${i}`);
                revealDiv.style.display = 'block';
                revealDiv.innerHTML = `<strong>${rondeGegevens.artist}</strong><br>${rondeGegevens.title}<br>📅 ${rondeGegevens.year}`;
            }

            const gekozenKaart = document.getElementById(`card-${gekozenIndex}`);
            const juisteKaart = document.getElementById(`card-${indexJuisteAntwoord}`);

            if (gekozenIndex === indexJuisteAntwoord) {
                gekozenKaart.classList.add('card-correct');
                document.getElementById('instruction-text').innerText = "🔥 BINGO! GOED GEDAAN!";
            } else {
                gekozenKaart.classList.add('card-wrong');
                juisteKaart.classList.add('card-correct');
                document.getElementById('instruction-text').innerText = "❌ HELAAS, VERKEERDE KAART!";
            }

            if (isHost) {
                document.getElementById('btn-start').innerText = "⚡ START VOLGENDE RONDE";
            }
        }

        // 4. ANIMATIES & TIMERS
        function startMultiplayerTimer() {
            const timerBox = document.getElementById('timer-box');
            const timerBar = document.getElementById('timer-bar');
            
            timerBox.style.display = 'block';
            timerBar.style.width = '100%';

            let startTijd = Date.now();
            let totaleDuur = 30000; // 30 seconden

            timerInterval = setInterval(() => {
                let verstreken = Date.now() - startTijd;
                let percentage = Math.max(0, 100 - (verstreken / totaleDuur) * 100);
                timerBar.style.width = percentage + '%';
            }, 100);

            timerTimeout = setTimeout(() => {
                clearInterval(timerInterval);
                gameActief = false;
                
                if (audioPlayer) { audioPlayer.pause(); }
                stopVuMeter();
                
                document.getElementById('instruction-text').innerText = "⏰ DE TIJD IS OM!";
                
                for (let i = 0; i < 4; i++) {
                    document.getElementById(`card-${i}`).classList.remove('active');
                    document.getElementById(`reveal-${i}`).style.display = 'block';
                    document.getElementById(`reveal-${i}`).innerHTML = `<strong>${rondeGegevens.artist}</strong><br>${rondeGegevens.title}<br>📅 ${rondeGegevens.year}`;
                }
                document.getElementById(`card-${indexJuisteAntwoord}`).classList.add('card-correct');
                
                if (isHost) {
                    document.getElementById('btn-start').innerText = "⚡ START VOLGENDE RONDE";
                }
            }, totaleDuur);
        }

        function resetKaarten() {
            indexJuisteAntwoord = null;
            document.getElementById('timer-box').style.display = 'none';
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.className = 'game-card';
                kaart.querySelector('.card-text').innerText = '?';
                document.getElementById(`reveal-${i}`).style.display = 'none';
            }
        }

        function startVuMeter() {
            const bars = document.querySelectorAll('.vu-bar');
            vuInterval = setInterval(() => {
                bars.forEach(bar => {
                    const randomHoogte = Math.floor(Math.random() * 21) + 3;
                    bar.style.height = randomHoogte + 'px';
                });
            }, 80);
        }

        function stopVuMeter() {
            clearInterval(vuInterval);
            document.querySelectorAll('.vu-bar').forEach(bar => { bar.style.height = '3px'; });
        }
    </script>
</body>
</html>
