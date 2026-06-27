<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HitJam 3 - Card Battle</title>
    <style>
        /* Premium Arcade Design (Zwart, Rood, Neon-Oranje) */
        body {
            background-color: #050508;
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
            background: linear-gradient(180deg, #100b0b 0%, #050508 100%);
            padding: 15px 20px;
            border-bottom: 2px solid #ff3333;
            box-shadow: 0 4px 20px rgba(255, 51, 51, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #ff3333;
            text-shadow: 0 0 10px rgba(255, 51, 51, 0.6);
        }
        header h1 span { color: #ff6600; text-shadow: 0 0 10px rgba(255, 102, 0, 0.6); }
        
        .score-board {
            font-size: 16px;
            font-weight: bold;
            color: #ff6600;
            border: 1px solid #ff6600;
            padding: 4px 15px;
            border-radius: 15px;
            background: rgba(255,102,0,0.1);
        }

        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            gap: 30px;
        }

        /* Het Speelveld met de 4 kaarten */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            width: 100%;
            max-width: 400px;
        }
        
        /* Op mobiel 2x2, op grotere schermen netjes 4 naast elkaar */
        @media(min-width: 500px) {
            .cards-grid { grid-template-columns: repeat(4, 1fr); max-width: 650px; }
        }

        /* De Speelkaarten */
        .game-card {
            background: #0d0d13;
            border: 2px solid #1a1a26;
            height: 150px;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0,0,0,0.6);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        /* Effect als de kaarten actief/onthuld zijn */
        .game-card.active {
            border-color: #ff6600;
            box-shadow: 0 0 15px rgba(255, 102, 0, 0.3);
        }
        .game-card.active:hover {
            transform: translateY(-5px);
            border-color: #ff3333;
            box-shadow: 0 0 25px rgba(255, 51, 51, 0.5);
        }

        /* Kaart teksten */
        .card-number {
            position: absolute;
            top: 8px;
            left: 10px;
            font-size: 11px;
            font-weight: bold;
            color: #444;
        }
        .game-card.active .card-number { color: #ff3333; }
        
        .card-text {
            font-size: 14px;
            font-weight: bold;
            color: #333; /* Donker als het nog niet gestart is */
            line-clamp: 3;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .game-card.active .card-text { color: #ffffff; }

        /* Status & Bediening */
        .controls-card {
            background: #0d0d13;
            border: 1px solid #1a1a26;
            padding: 20px;
            border-radius: 20px;
            width: 100%;
            max-width: 340px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        
        .round-instruction {
            font-size: 13px;
            text-transform: uppercase;
            color: #888;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .btn-play {
            background: linear-gradient(135deg, #ff3333 0%, #ff6600 100%);
            color: #ffffff;
            border: none;
            padding: 14px 40px;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(255, 51, 51, 0.4);
            transition: all 0.2s ease;
        }
        .btn-play:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 102, 0, 0.6);
        }

        /* VU Meter onder de knop */
        .vu-meter {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 3px;
            height: 25px;
            margin-top: 15px;
        }
        .vu-bar {
            width: 6px;
            height: 3px;
            background: linear-gradient(0deg, #ff6600 0%, #ff3333 100%);
            border-radius: 2px;
            transition: height 0.1s ease;
        }

        /* Kaart Goed/Fout Kleuren */
        .card-correct { background: #1b4d22 !important; border-color: #2e7d32 !important; box-shadow: 0 0 25px #2e7d32 !important; }
        .card-wrong { background: #4d1b1b !important; border-color: #c62828 !important; box-shadow: 0 0 25px #c62828 !important; }
    </style>
</head>
<body>

    <header>
        <h1>HITJAM <span>3</span></h1>
        <div class="score-board">🏆 SCORE: <span id="score-val">0</span></div>
    </header>

    <main>
        <!-- De 4 Speelkaarten -->
        <div class="cards-grid">
            <div class="game-card" id="card-0" onclick="kiesKaart(0)">
                <div class="card-number">🔹 CARD A</div>
                <div class="card-text">?</div>
            </div>
            <div class="game-card" id="card-1" onclick="kiesKaart(1)">
                <div class="card-number">🔹 CARD B</div>
                <div class="card-text">?</div>
            </div>
            <div class="game-card" id="card-2" onclick="kiesKaart(2)">
                <div class="card-number">🔹 CARD C</div>
                <div class="card-text">?</div>
            </div>
            <div class="game-card" id="card-3" onclick="kiesKaart(3)">
                <div class="card-number">🔹 CARD D</div>
                <div class="card-text">?</div>
            </div>
        </div>

        <!-- Bediening & VU Meter -->
        <div class="controls-card">
            <div class="round-instruction" id="instruction-text">Druk op start om te spelen</div>
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
        let kaartOpties = [];
        let indexJuisteAntwoord = null;
        let vuInterval = null;
        let huidigeScore = 0;
        let gameActief = false;

        document.getElementById('btn-start').addEventListener('click', startNieuwNummer);

        function startNieuwNummer() {
            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();
            resetKaarten();
            
            document.getElementById('btn-start').innerText = "⏳ LADEN...";
            document.getElementById('instruction-text').innerText = "Database doorzoeken...";

            // Haal willekeurig nummer op uit je Raspberry Pi backend
            fetch('hj3_get_next_song.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('btn-start').innerText = "⚡ START PLAY";
                    
                    if (data.status === 'success') {
                        rondeGegevens = data;
                        gameActief = true;
                        
                        // Start Apple audio stream direct via de browser
                        audioPlayer = new Audio(data.preview_url);
                        audioPlayer.play();
                        startVuMeter();
                        
                        // Genereer de kaarten op basis van een willekeurig type
                        bouwKaartenRonde();
                    } else {
                        document.getElementById('instruction-text').innerText = "Fout bij laden.";
                    }
                });
        }

        function bouwKaartenRonde() {
            // Kies willekeurig type: 0 = Jaar, 1 = Artiest, 2 = Titel
            const rondeType = Math.floor(Math.random() * 3);
            let juisteWaarde = "";
            let instructie = "";

            if (rondeType === 0) {
                juisteWaarde = rondeGegevens.year.toString();
                instructie = "🔎 Welk JAARTAL hoor je?";
            } else if (rondeType === 1) {
                juisteWaarde = rondeGegevens.artist;
                instructie = "🎤 Welke ARTIEST hoor je?";
            } else {
                juisteWaarde = rondeGegevens.title;
                instructie = "🎵 Welke SONGTITEL hoor je?";
            }

            document.getElementById('instruction-text').innerText = instructie;

            // We maken een set met opties om duplicaten te voorkomen
            let optiesSet = new Set();
            optiesSet.add(juisteWaarde);

            // Genereer 3 nep-opties (voor het gemak gebruiken we hier variaties of jaartallen)
            while (optiesSet.size < 4) {
                if (rondeType === 0) {
                    // Nep jaartal rondom het echte jaar
                    const afwijking = Math.floor(Math.random() * 15) - 7;
                    optiesSet.add((parseInt(rondeGegevens.year) + afwijking).toString());
                } else if (rondeType === 1) {
                    // Fictieve artiesten-fallbacks voor de test
                    const nepArtiesten = ["Billy Talent", "Michael Jackson", "Nirvana", "Wellhello", "Linkin Park", "Green Day", "Metallica"];
                    optiesSet.add(nepArtiesten[Math.floor(Math.random() * nepArtiesten.length)]);
                } else {
                    // Fictieve song-fallbacks voor de test
                    const nepTitels = ["Red Flag", "Thriller", "Smells Like Teen Spirit", "Apuved meg", "In The End", "Basket Case", "Enter Sandman"];
                    optiesSet.add(nepTitels[Math.floor(Math.random() * nepTitels.length)]);
                }
            }

            // Maak er een array van en hussel de volgorde
            kaartOpties = Array.from(optiesSet).sort(() => Math.random() - 0.5);
            indexJuisteAntwoord = kaartOpties.indexOf(juisteWaarde);

            // Vul de kaarten visueel in de HTML en activeer ze
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.classList.add('active');
                kaart.querySelector('.card-text').innerText = kaartOpties[i];
            }
        }

        function kiesKaart(gekozenIndex) {
            if (!gameActief || indexJuisteAntwoord === null) return;
            gameActief = false; // Voorkom dubbel klikken

            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();

            const gekozenKaart = document.getElementById(`card-${gekozenIndex}`);
            const juisteKaart = document.getElementById(`card-${indexJuisteAntwoord}`);

            if (gekozenIndex === indexJuisteAntwoord) {
                // GOED GERADEN!
                gekozenKaart.classList.add('card-correct');
                huidigeScore += 10;
                document.getElementById('score-val').innerText = huidigeScore;
                document.getElementById('instruction-text').innerText = "🔥 GEWELDIG! +10 PUNTEN";
            } else {
                // FOUT GERADEN!
                gekozenKaart.classList.add('card-wrong');
                juisteKaart.classList.add('card-correct'); // Toon welk antwoord het moest zijn
                document.getElementById('instruction-text').innerText = "❌ HELAAS, VOLGENDE KEER BETER!";
            }

            // Deactiveer de hovers op alle kaarten direct na de keuze
            for (let i = 0; i < 4; i++) {
                document.getElementById(`card-${i}`).classList.remove('active');
            }
        }

        function resetKaarten() {
            kaartOpties = [];
            indexJuisteAntwoord = null;
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.className = 'game-card'; // Reset alle extra classes
                kaart.querySelector('.card-text').innerText = '?';
            }
        }

        function startVuMeter() {
            const bars = document.querySelectorAll('.vu-bar');
            vuInterval = setInterval(() => {
                bars.forEach(bar => {
                    const randomHoogte = Math.floor(Math.random() * 22) + 3;
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
