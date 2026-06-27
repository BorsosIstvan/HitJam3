<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HitJam 3 - Mixed Card Battle</title>
    <style>
        /* Professioneel diepzwart design met gloeiend rood en neon-oranje */
        body {
            background-color: #040406;
            color: #ffffff;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        header {
            background: linear-gradient(180deg, #120909 0%, #040406 100%);
            padding: 15px 20px;
            border-bottom: 3px solid #ff3333;
            box-shadow: 0 4px 25px rgba(255, 51, 51, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #ff3333;
            text-shadow: 0 0 12px rgba(255, 51, 51, 0.7);
        }
        header h1 span { color: #ff6600; text-shadow: 0 0 12px rgba(255, 102, 0, 0.7); }
        
        .score-board {
            font-size: 16px;
            font-weight: 900;
            color: #ff6600;
            border: 2px solid #ff6600;
            padding: 5px 18px;
            border-radius: 18px;
            background: rgba(255,102,0,0.15);
            box-shadow: 0 0 10px rgba(255,102,0,0.2);
        }

        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 25px;
            gap: 25px;
        }

        /* 4 Luxe speelkaarten (2x2 op mobiel, 4 naast elkaar op computer) */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            width: 100%;
            max-width: 420px;
        }
        @media(min-width: 600px) {
            .cards-grid { grid-template-columns: repeat(4, 1fr); max-width: 750px; }
        }

        /* De Speelkaarten met decoratie-achtergrondlaag */
        .game-card {
            background-color: #0b0b12;
            background-image: linear-gradient(135deg, rgba(26,26,38,0.5) 0%, rgba(10,10,15,0.8) 100%);
            border: 2px solid #1a1a2b;
            height: 170px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0,0,0,0.7);
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        /* Kaart lichten op zodra game start */
        .game-card.active {
            border-color: #ff6600;
            box-shadow: 0 0 15px rgba(255, 102, 0, 0.25);
        }
        .game-card.active:hover {
            transform: translateY(-8px) scale(1.03);
            border-color: #ff3333;
            box-shadow: 0 0 30px rgba(255, 51, 51, 0.6);
        }

        .card-badge {
            position: absolute;
            top: 10px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #444;
            text-transform: uppercase;
        }
        .game-card.active .card-badge { color: #ff6600; }
        
        /* De tekst op de kaart (Jaar, Artiest of Titel) */
        .card-text {
            font-size: 15px;
            font-weight: 800;
            color: #2b2b36; /* Donker van tevoren */
            line-height: 1.4;
            transition: color 0.2s;
        }
        .game-card.active .card-text { color: #ffffff; }

        /* De Onthullings-weergave (The Full Reveal) */
        .reveal-details {
            display: none;
            font-size: 11px;
            margin-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 6px;
            color: #bbb;
            width: 100%;
        }

        /* Resultaat-kleuren na het kiezen */
        .card-correct { 
            background: linear-gradient(135deg, #143d1a 0%, #0b220f 100%) !important; 
            border-color: #2e7d32 !important; 
            box-shadow: 0 0 30px #2e7d32 !important;
        }
        /* De winnende kaart krijgt extra gouden sterrendecoratie */
        .card-correct::after {
            content: '⭐ WINNER';
            position: absolute;
            bottom: 8px;
            font-size: 9px;
            font-weight: 900;
            color: #ffd700;
            letter-spacing: 1px;
        }
        .card-wrong { 
            background: linear-gradient(135deg, #441616 0%, #220b0b 100%) !important; 
            border-color: #c62828 !important; 
            box-shadow: 0 0 25px #c62828 !important;
            opacity: 0.6;
        }

        /* Bedieningspaneel onderaan */
        .controls-card {
            background: #0d0d15;
            border: 2px solid #161622;
            padding: 22px;
            border-radius: 24px;
            width: 100%;
            max-width: 350px;
            text-align: center;
            box-shadow: 0 12px 30px rgba(0,0,0,0.6);
        }
        
        .instruction-banner {
            font-size: 14px;
            text-transform: uppercase;
            color: #8a8a9e;
            font-weight: 800;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
        }

        .btn-play {
            background: linear-gradient(135deg, #ff3333 0%, #ff6600 100%);
            color: #ffffff;
            border: none;
            padding: 14px 45px;
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 35px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(255, 51, 51, 0.45);
            transition: all 0.2s ease;
        }
        .btn-play:hover {
            transform: scale(1.04);
            box-shadow: 0 10px 30px rgba(255, 102, 0, 0.65);
        }

        /* Actieve VU-Meter */
        .vu-meter {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 4px;
            height: 28px;
            margin-top: 18px;
        }
        .vu-bar {
            width: 7px;
            height: 4px;
            background: linear-gradient(0deg, #ff6600 0%, #ff3333 100%);
            border-radius: 3px;
            transition: height 0.1s ease;
        }
    </style>
</head>
<body>

    <!-- Onzichtbare audio-elementen voor de geluidseffecten -->
    <audio id="sound-applause" src="https://google.com" preload="auto"></audio>
    <audio id="sound-wrong" src="https://google.com" preload="auto"></audio>

    <header>
        <h1>HITJAM <span>3</span></h1>
        <div class="score-board">🏆 SCORE: <span id="score-val">0</span></div>
    </header>

    <main>
        <!-- De 4 Gemixt Arcade Speelkaarten -->
        <div class="cards-grid">
            <div class="game-card" id="card-0" onclick="kiesKaart(0)">
                <div class="card-badge">♠️ Card A</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-0"></div>
            </div>
            <div class="game-card" id="card-1" onclick="kiesKaart(1)">
                <div class="card-badge">♥️ Card B</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-1"></div>
            </div>
            <div class="game-card" id="card-2" onclick="kiesKaart(2)">
                <div class="card-badge">♣️ Card C</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-2"></div>
            </div>
            <div class="game-card" id="card-3" onclick="kiesKaart(3)">
                <div class="card-badge">♦️ Card D</div>
                <div class="card-text">?</div>
                <div class="reveal-details" id="reveal-3"></div>
            </div>
        </div>

        <!-- Bedieningspaneel & VU Meter -->
        <div class="controls-card">
            <div class="instruction-banner" id="instruction-text">Druk op START PLAY</div>
            <button class="btn-play" id="btn-start">⚡ START PLAY</button>
            
            <div class="vu-meter">
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
                <div class="vu-bar"></div><div class="vu-bar"></div><div class="vu-bar"></div>
            </div>
        </div>
    </main>
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
            document.getElementById('instruction-text').innerText = "Muziek inladen...";

            fetch('hj3_get_next_song.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('btn-start').innerText = "⚡ START PLAY";
                    
                    if (data.status === 'success') {
                        rondeGegevens = data;
                        gameActief = true;
                        
                        // Start Apple audio stream
                        audioPlayer = new Audio(data.preview_url);
                        audioPlayer.play();
                        startVuMeter();
                        
                        // Start de unieke gemixt kaarten-opbouw!
                        bouwGemixtKaarten();
                    } else {
                        document.getElementById('instruction-text').innerText = "Fout bij laden.";
                    }
                });
        }

        function bouwGemixtKaarten() {
            document.getElementById('instruction-text').innerText = "💥 Vind de juiste match!";

            // We kiezen willekeurig welke eigenschap de winnende kaart laat zien
            // 0 = Jaar, 1 = Artiest, 2 = Titel
            const winnendType = Math.floor(Math.random() * 3);
            let juisteWaarde = "";
            if (winnendType === 0) juisteWaarde = rondeGegevens.year.toString();
            else if (winnendType === 1) juisteWaarde = rondeGegevens.artist;
            else juisteWaarde = rondeGegevens.title;

            // Bepaal op welke kaart (0 t/m 3) het juiste antwoord komt te staan
            indexJuisteAntwoord = Math.floor(Math.random() * 4);

            // Vul alle kaarten met een unieke mix van informatie
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                const tekstElement = kaart.querySelector('.card-text');
                kaart.classList.add('active');

                if (i === indexJuisteAntwoord) {
                    // Dit is de winnende kaart!
                    tekstElement.innerText = juisteWaarde;
                } else {
                    // Dit zijn de 3 verliezende kaarten. We vullen ze met willekeurige foute mix-data.
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
            gameActief = false; // Voorkom dubbelklikken

            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();

            // De Grote Onthulling: Toon op álle kaarten het volledige nummer-pakket!
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
                document.getElementById('sound-applause').currentTime = 0;
                document.getElementById('sound-applause').play();
            } else {
                // VERLIEZER! 😢
                gekozenKaart.classList.add('card-wrong');
                juisteKaart.classList.add('card-correct'); // Laat zien welke het wel was
                document.getElementById('instruction-text').innerText = "❌ FOUTE KAART!";
                
                // Speel wrong luid af
                document.getElementById('sound-wrong').currentTime = 0;
                document.getElementById('sound-wrong').play();
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
                    const randomHoogte = Math.floor(Math.random() * 24) + 4;
                    bar.style.height = randomHoogte + 'px';
                });
            }, 80);
        }

        function stopVuMeter() {
            clearInterval(vuInterval);
            const bars = document.querySelectorAll('.vu-bar');
            bars.forEach(bar => { bar.style.height = '4px'; });
        }
    </script>
</body>
</html>
