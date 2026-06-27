<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 3 - Premium Cards</title>
    <style>
        /* Solide mobiele basis uit HJ2 met de nieuwe rood/oranje arcade stijl */
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
            font-size: 14px;
            font-weight: 900;
            color: #ff9500;
            letter-spacing: 1px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* 2x2 Grid speciaal voor een perfecte pasvorm op mobiel */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin: 15px 0;
            width: 100%;
        }

        /* De NIEUWE Speelkaarten: 3 rijen, grotere letters, geen overbodige tekst */
        .game-card {
            background: rgba(255, 255, 255, 0.03);
            border: 2px solid rgba(255, 255, 255, 0.1);
            height: 165px; /* Iets hoger gemaakt voor de 3 rijen en grote letters */
            border-radius: 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Verdeelt de 3 rijen perfect over de kaart */
            padding: 15px 10px;
            box-sizing: border-box;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        /* Actieve kaarten lichten fel op */
        .game-card.active {
            border-color: #ff2d55;
            box-shadow: 0 6px 20px rgba(255, 45, 85, 0.25);
        }
        .game-card.active:active {
            transform: scale(0.95);
            background: rgba(255, 45, 85, 0.12);
        }

        /* De 3 Rijen binnen de kaart met GROTERE letters */
        .row-year {
            font-size: 24px; /* Grote, dikke letters voor het jaar */
            font-weight: 900;
            color: #ff9500;
            text-shadow: 0 0 8px rgba(255, 149, 0, 0.4);
            margin: 0;
        }
        
        .row-artist {
            font-size: 16px; /* Duidelijke, grote letters voor de artiest */
            font-weight: 800;
            color: #ffffff;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.2;
        }
        
        .row-title {
            font-size: 12px; /* Iets compacter maar super stijlvol voor het liedje */
            font-weight: 600;
            color: #b3b3b3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Standaard zijn de details op de kaarten verborgen (?) */
        .game-card .row-year, 
        .game-card .row-artist, 
        .game-card .row-title {
            visibility: hidden;
        }

        /* Zodra de game start, tonen we ALLEEN de gemixte waarde die geraden moet worden */
        .game-card.active .show-mixed-value {
            visibility: visible !important;
            font-size: 18px; /* Extra groot gemaakt voor de speelronde! */
            font-weight: 900;
            color: #ffffff;
        }

        /* Resultaat states (The Full Reveal: toont alle 3 de rijen tegelijk!) */
        .card-correct { 
            background: linear-gradient(135deg, #122c16 0%, #0b1a0d 100%) !important; 
            border-color: #00ffcc !important; 
            box-shadow: 0 0 25px rgba(0, 255, 204, 0.5) !important;
        }
        .card-correct .row-year { color: #00ffcc !important; text-shadow: 0 0 10px rgba(0,255,204,0.5); }
        .card-correct .row-artist, .card-correct .row-title { visibility: visible !important; }
        
        .card-wrong { 
            background: linear-gradient(135deg, #2c1212 0%, #1a0b0b 100%) !important; 
            border-color: #ff2d55 !important; 
            box-shadow: 0 0 15px rgba(255, 45, 85, 0.3) !important;
            opacity: 0.4;
        }
        .card-wrong .row-year, .card-wrong .row-artist, .card-wrong .row-title { visibility: visible !important; }

        /* Centrale knop & VU Meter */
        .play-box { margin: 10px 0; }
        
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
            <div style="font-size: 14px; color: #ff9500; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;" id="instruction-text">Klik op start play</div>
        </div>

        <!-- De Luxe 3-Rijen Kaarten Grid -->
        <div class="cards-grid">
            
            <!-- Kaart 0 -->
            <div class="game-card" id="card-0" onclick="kiesKaart(0)">
                <div class="row-year" id="year-0">2000</div>
                <div class="row-artist" id="artist-0">Artiest</div>
                <div class="row-title" id="title-0">Liedje</div>
            </div>
            
            <!-- Kaart 1 -->
            <div class="game-card" id="card-1" onclick="kiesKaart(1)">
                <div class="row-year" id="year-1">2000</div>
                <div class="row-artist" id="artist-1">Artiest</div>
                <div class="row-title" id="title-1">Liedje</div>
            </div>
            
            <!-- Kaart 2 -->
            <div class="game-card" id="card-2" onclick="kiesKaart(2)">
                <div class="row-year" id="year-2">2000</div>
                <div class="row-artist" id="artist-2">Artiest</div>
                <div class="row-title" id="title-2">Liedje</div>
            </div>
            
            <!-- Kaart 3 -->
            <div class="game-card" id="card-3" onclick="kiesKaart(3)">
                <div class="row-year" id="year-3">2000</div>
                <div class="row-artist" id="artist-3">Artiest</div>
                <div class="row-title" id="title-3">Liedje</div>
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

        <div class="footer">HITJAM 3 ARCADE DESIGN</div>

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
                        
                        // Start de gloednieuwe 3-rijen kaarten opbouw
                        bouw3RijenKaarten();
                    } else {
                        document.getElementById('instruction-text').innerText = "Fout bij laden.";
                    }
                });
        }

        function bouw3RijenKaarten() {
            document.getElementById('instruction-text').innerText = "💥 VIND DE JUISTE MATCH!";

            // Kies welk type de winnende kaart laat zien (0 = Jaar, 1 = Artiest, 2 = Titel)
            const winnendType = Math.floor(Math.random() * 3);
            indexJuisteAntwoord = Math.floor(Math.random() * 4);

            // Vul alle 4 de kaarten met een unieke mix van data op de achtergrond
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.classList.add('active');

                // Elementen binnen deze specifieke kaart opzoeken
                const elYear = document.getElementById(`year-${i}`);
                const elArtist = document.getElementById(`artist-${i}`);
                const elTitle = document.getElementById(`title-${i}`);

                // Vul alvast de echte data in voor de Full Reveal strakjes
                elYear.innerText = rondeGegevens.year;
                elArtist.innerText = rondeGegevens.artist;
                elTitle.innerText = rondeGegevens.title;

                if (i === indexJuisteAntwoord) {
                    // Dit is de winnende kaart! Toon de gekozen waarde groot in het midden
                    if (winnendType === 0) {
                        elYear.classList.add('show-mixed-value');
                        elYear.innerText = rondeGegevens.year;
                    } else if (winnendType === 1) {
                        elArtist.classList.add('show-mixed-value');
                        elArtist.innerText = rondeGegevens.artist;
                    } else {
                        elTitle.classList.add('show-mixed-value');
                        elTitle.innerText = rondeGegevens.title;
                    }
                } else {
                    // Dit zijn de verliezende kaarten. We genereren willekeurige nep-data
                    const randomType = Math.floor(Math.random() * 3);
                    if (randomType === 0) {
                        const afwijking = (Math.floor(Math.random() * 14) - 7);
                        const nepJaar = (parseInt(rondeGegevens.year) + (afwijking === 0 ? 4 : afwijking)).toString();
                        elYear.innerText = nepJaar;
                        elYear.classList.add('show-mixed-value');
                    } else if (randomType === 1) {
                        const nepArtiesten = ["Linkin Park", "Dua Lipa", "Eminem", "Tiësto", "Rammstein", "Coldplay", "Rihanna"];
                        const nepArtist = nepArtiesten[Math.floor(Math.random() * nepArtiesten.length)];
                        elArtist.innerText = nepArtist;
                        elArtist.classList.add('show-mixed-value');
                    } else {
                        const nepTitels = ["In The End", "Levitating", "Lose Yourself", "The Business", "Du Hast", "Yellow", "Umbrella"];
                        const nepTitle = nepTitels[Math.floor(Math.random() * nepTitels.length)];
                        elTitle.innerText = nepTitle;
                        elTitle.classList.add('show-mixed-value');
                    }
                }
            }
        }

        function kiesKaart(gekozenIndex) {
            if (!gameActief || indexJuisteAntwoord === null) return;
            gameActief = false; // Voorkom dubbelklikken op mobiel

            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();

            // De Grote Onthulling op álle kaarten! Toon nu alle 3 de rijen tegelijk in vol ornaat
            for (let i = 0; i < 4; i++) {
                const kaart = document.getElementById(`card-${i}`);
                kaart.classList.remove('active');

                // Haal de specifieke test-stijl klassen weg zodat de kaart netjes rendert
                const elYear = document.getElementById(`year-${i}`);
                const elArtist = document.getElementById(`artist-${i}`);
                const elTitle = document.getElementById(`title-${i}`);

                elYear.classList.remove('show-mixed-value');
                elArtist.classList.remove('show-mixed-value');
                elTitle.classList.remove('show-mixed-value');

                // Zet de échte songgegevens er nu definitief in
                elYear.innerText = rondeGegevens.year;
                elArtist.innerText = rondeGegevens.artist;
                elTitle.innerText = rondeGegevens.title;

                // Maak alle tekstlagen zichtbaar op de kaarten
                elYear.style.visibility = 'visible';
                elArtist.style.style.visibility = 'visible';
                elArtist.style.visibility = 'visible';
                elTitle.style.visibility = 'visible';
            }

            const gekozenKaart = document.getElementById(`card-${gekozenIndex}`);
            const juisteKaart = document.getElementById(`card-${indexJuisteAntwoord}`);

            if (gekozenIndex === indexJuisteAntwoord) {
                // GOED GERADEN! 🎉
                gekozenKaart.classList.add('card-correct');
                huidigeScore += 10;
                document.getElementById('score-val').innerText = huidigeScore;
                document.getElementById('instruction-text').innerText = "🔥 BINGO! +10 PUNTEN";
                
                // Speel applaus luid af
                document.getElementById('sound-applause').currentTime = 0;
                document.getElementById('sound-applause').play();
            } else {
                // FOUT GERADEN! 😢
                gekozenKaart.classList.add('card-wrong');
                juisteKaart.classList.add('card-correct'); // Ligt groen op ter info
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
                
                const elYear = document.getElementById(`year-${i}`);
                const elArtist = document.getElementById(`artist-${i}`);
                const elTitle = document.getElementById(`title-${i}`);

                elYear.classList.remove('show-mixed-value');
                elArtist.classList.remove('show-mixed-value');
                elTitle.classList.remove('show-mixed-value');

                elYear.style.visibility = 'hidden';
                elArtist.style.visibility = 'hidden';
                elTitle.style.visibility = 'hidden';

                elYear.innerText = '';
                elArtist.innerText = '';
                elTitle.innerText = '';
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
