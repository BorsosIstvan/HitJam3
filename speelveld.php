<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 3 - Arcade Battle</title>
    <style>
        /* Exact dezelfde solide mobiele basis als HJ2, maar met de premium rode/oranje arcade look */
        body { 
            font-family: 'Segoe UI', -apple-system, sans-serif; 
            margin: 0; 
            background-color: #0b0c10; 
            color: #ffffff; 
            display: flex; 
            justify-content: center; 
            min-height: 100vh; 
        }
        
        .app-container { 
            width: 100%; 
            max-width: 450px; 
            background: linear-gradient(180deg, #160c13 0%, #0b0c10 100%); 
            padding: 20px; 
            box-sizing: border-box; 
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
            box-shadow: 0 0 35px rgba(0,0,0,0.8); 
            text-align: center; 
        }

        .logo { 
            font-size: 32px; 
            font-weight: 900; 
            background: linear-gradient(45deg, #ff2d55, #ff9500); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            text-transform: uppercase; 
            margin: 0 0 5px 0; 
            letter-spacing: 1px;
        }

        .score-board {
            font-size: 13px;
            font-weight: bold;
            color: #ff9500;
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        /* 2x2 Grid geoptimaliseerd voor het telefoonscherm */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 15px 0;
            width: 100%;
        }

        /* De Speelkaarten met subtiele fotolook-achtergrond */
        .game-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 130px;
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            box-sizing: border-box;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        /* Actieve kaarten lichten prachtig rood/oranje op */
        .game-card.active {
            border-color: #ff2d55;
            box-shadow: 0 4px 15px rgba(255, 45, 85, 0.2);
        }
        .game-card.active:active {
            transform: scale(0.95);
            background: rgba(255, 45, 85, 0.1);
        }

        .card-badge {
            position: absolute;
            top: 8px;
            font-size: 9px;
            font-weight: bold;
            color: #4f4f4f;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .game-card.active .card-badge { color: #ff9500; }

        .card-text {
            font-size: 13px;
            font-weight: 800;
            color: rgba(255,255,255,0.15); /* Gedimd vooraf */
            line-height: 1.3;
            word-break: break-word;
        }
        .game-card.active .card-text { color: #ffffff; }

        /* Uitgeklapte full reveal details */
        .reveal-details {
            display: none;
            font-size: 10px;
            margin-top: 5px;
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 4px;
            color: #b3b3b3;
            width: 100%;
        }

        /* Resultaat states */
        .card-correct { 
            background: linear-gradient(135deg, #122c16 0%, #0b1a0d 100%) !important; 
            border-color: #00ffcc !important; 
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.4) !important;
        }
        .card-correct .card-text { color: #00ffcc !important; }
        
        .card-wrong { 
            background: linear-gradient(135deg, #2c1212 0%, #1a0b0b 100%) !important; 
            border-color: #ff2d55 !important; 
            box-shadow: 0 0 15px rgba(255, 45, 85, 0.3) !important;
            opacity: 0.5;
        }

        /* Centrale knop & VU Meter */
        .play-box { margin: 15px 0; }
        
        .btn-audio { 
            width: 100%; 
            padding: 16px; 
            border-radius: 14px; 
            font-size: 16px; 
            font-weight: bold; 
            border: none; 
            cursor: pointer; 
            text-transform: uppercase;
            background: linear-gradient(90deg, #ff2d55, #ff9500); 
            color: white; 
            box-shadow: 0 6px 20px rgba(255, 45, 85, 0.35); 
            transition: all 0.2s; 
        }
        .btn-audio:active { transform: scale(0.97); }

        /* Mobiele VU-Meter Equalizer */
        .vu-meter {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 4px;
            height: 24px;
            margin-top: 15px;
        }
        .vu-bar {
            width: 6px;
            height: 3px;
            background: linear-gradient(0deg, #ff9500 0%, #ff2d55 100%);
            border-radius: 2px;
            transition: height 0.1s ease;
        }

        .footer {
            font-size: 11px;
            color: #4f4f4f;
            text-align: center;
            letter-spacing: 1px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Native Google Audio FX bibliotheek -->
    <audio id="sound-applause" src="https://google.com" preload="auto"></audio>
    <audio id="sound-wrong" src="https://google.com" preload="auto"></audio>

    <div class="app-container">
        
        <div>
            <h1 class="logo">HitJam <span>3</span></h1>
            <div class="score-board">🏆 SCORE: <span id="score-val">0</span></div>
            <div style="font-size: 13px; color: #ff9500; font-weight: bold; text-transform: uppercase;" id="instruction-text">Klik op start play</div>
        </div>

        <!-- De 2x2 Telefoon Grid -->
        <div class="cards-grid">
            <div class="game-card" id="card-0" onclick="kiesKaart(0)">
                <div class="card-badge">Card A</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-0"></div>
            </div>
            <div class="game-card" id="card-1" onclick="kiesKaart(1)">
                <div class="card-badge">Card B</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-1"></div>
            </div>
            <div class="game-card" id="card-2" onclick="kiesKaart(2)">
                <div class="card-badge">Card C</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-2"></div>
            </div>
            <div class="game-card" id="card-3" onclick="kiesKaart(3)">
                <div class="card-badge">Card D</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-3"></div>
            </div>
        </div>

        <div class="play-box">
            <button class="btn-audio" id="btn-start">⚡ START PLAY</button>
            
            <div class="vu-meter">
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
            </div>
        </div>

        <div class="footer">HITJAM 3 PREMIUM MOBILE</div>

    </div>
    <script>
        let audioPlayer = null;
        let rondeGegevens = null;
        let indexJuisteAntwoord = null;
        let vuInterval = null;
        let huidigeScore = 0;
        let gameActief = false;

        document.getElementById('btn-start').addEventListener('click', startNieuwNummer);

        function startNieuwNummer() {
            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();
            resetKaarten();
            
            document.getElementById('btn-start').innerText = "⏳ SCANNING...";
            document.getElementById('instruction-text').innerText = "Inladen...";

            fetch('hj3_get_next_song.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('btn-start').innerText = "⚡ START PLAY";
                    
                    if (data.status === 'success') {
                        rondeGegevens = data;
                        gameActief = true;
                        
                        // Start Apple audio stream direct
                        audioPlayer = new Audio(data.preview_url);
                        audioPlayer.play();
                        startVuMeter();
                        
                        // Start de gemixte kaarten-opbouw
                        bouwGemixtKaarten();
                    } else {
                        document.getElementById('instruction-text').innerText = "Fout bij laden.";
                    }
                });
        }

        function bouwGemixtKaarten() {
            document.getElementById('instruction-text').innerText = "💥 VIND DE JUISTE MATCH!";

            // Kies welke eigenschap de winnende kaart laat zien (0 = Jaar, 1 = Artiest, 2 = Titel)
            const winnendType = Math.floor(Math.random() * 3);
            let juisteWaarde = "";
            if (winnendType === 0) juisteWaarde = rondeGegevens.year.toString();
            else if (winnendType === 1) juisteWaarde = rondeGegevens.artist;
            else juisteWaarde = rondeGegevens.title;

            // Bepaal de winnende kaart (0 t/m 3)
            indexJuisteAntwoord = Math.floor(Math.random() * 4);

            // Vul alle kaarten met een unieke mix van informatie
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                const tekstElement = kaart.querySelector('.card-text');
                kaart.classList.add('active');

                if (i === indexJuisteAntwoord) {
                    tekstElement.innerText = juisteWaarde;
                } else {
                    // Genereer willekeurige foute mix-data voor de verliezende kaarten
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
            gameActief = false; // Voorkom dubbelklikken op mobiel

            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();

            // De Grote Onthulling op álle kaarten!
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
                // WINNAAR! 🎉
                gekozenKaart.classList.add('card-correct');
                huidigeScore += 10;
                document.getElementById('score-val').innerText = huidigeScore;
                document.getElementById('instruction-text').innerText = "🔥 BINGO! +10 PUNTEN";
                
                // Speel applaus luid af
                //document.getElementById('sound-applause').currentTime = 0;
                //document.getElementById('sound-applause').play();
            } else {
                // VERLIEZER! 😢
                gekozenKaart.classList.add('card-wrong');
                juisteKaart.classList.add('card-correct');
                document.getElementById('instruction-text').innerText = "❌ FOUTE KAART!";
                
                // Speel fout-sound luid af
                //document.getElementById('sound-wrong').currentTime = 0;
                //document.getElementById('sound-wrong').play();
            }
        }

        function resetKaarten() {
            indexJuisteAntwoord = null;
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.className = 'game-card';
                kaart.querySelector('.card-text').innerText = '?';
                document.getElementById(`reveal-${i}`).style.display = 'none';
                document.getElementById(`reveal-${i}`).innerText = '';
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
            const bars = document.querySelectorAll('.vu-bar');
            bars.forEach(bar => { bar.style.height = '3px'; });
        }
    </script>
</body>
</html>
