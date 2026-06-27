<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HitJam 3 - Premium Arcade</title>
    <style>
        /* Professioneel donker design met rood en neon-oranje accenten */
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
            overflow-x: hidden;
        }

        /* Premium Header */
        header {
            background: linear-gradient(180deg, #100b0b 0%, #050508 100%);
            padding: 18px 20px;
            border-bottom: 2px solid #ff3333;
            box-shadow: 0 4px 20px rgba(255, 51, 51, 0.15);
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

        /* Centrale Game Container */
        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            gap: 25px;
        }

        /* De platen- en animatiekaart */
        .arcade-card {
            background: #0d0d13;
            border: 2px solid #1a1a26;
            padding: 30px;
            border-radius: 28px;
            width: 100%;
            max-width: 340px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.8), inset 0 0 20px rgba(255,102,0,0.05);
            position: relative;
        }

        /* Gloeiende Vinyl Schijf met Retro Groeven */
        .vinyl-container {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 10px auto 25px auto;
        }
        .music-vinyl {
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, #ff6600 0%, #111 10%, #000 11%, #151515 30%, #000 31%, #1c1c1c 50%, #000 70%, #ff3333 100%);
            border-radius: 50%;
            box-shadow: 0 0 25px rgba(255, 51, 51, 0.3), inset 0 0 10px #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        /* Het middengat van de single */
        .vinyl-center {
            width: 35px;
            height: 35px;
            background-color: #0d0d13;
            border: 3px solid #ff6600;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Geanimeerde VU-Meter (Equalizer) */
        .vu-meter {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 4px;
            height: 45px;
            margin: 15px 0;
            padding: 0 10px;
        }
        .vu-bar {
            width: 8px;
            height: 5px; /* Start laag */
            background: linear-gradient(0deg, #ff6600 0%, #ff3333 80%, #ff0000 100%);
            border-radius: 3px;
            transition: height 0.1s ease;
        }

        /* Grote Actieknop */
        .btn-arcade {
            background: linear-gradient(135deg, #ff3333 0%, #ff6600 100%);
            color: #ffffff;
            border: none;
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(255, 51, 51, 0.4);
            transition: all 0.2s ease;
        }
        .btn-arcade:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 102, 0, 0.6);
        }

        /* Jaartal Kiezer (HitJam2-stijl) */
        .year-selector-section {
            width: 100%;
            max-width: 340px;
            background: #0d0d13;
            border: 2px solid #1a1a26;
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            display: none; /* Komt pas in beeld als muziek speelt */
        }
        .year-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        .year-btn {
            background-color: #161622;
            border: 1px solid #2d2d3f;
            color: #fff;
            padding: 10px 0;
            font-weight: bold;
            font-size: 14px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .year-btn:hover {
            background-color: #ff6600;
            border-color: #ff6600;
            color: #000;
        }

        /* Feedback Popups */
        .alert-box {
            padding: 12px;
            border-radius: 14px;
            font-weight: bold;
            margin-top: 15px;
            display: none;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .alert-success { background-color: rgba(46, 125, 50, 0.2); border: 2px solid #2e7d32; color: #4caf50; text-shadow: 0 0 5px #2e7d32; }
        .alert-error { background-color: rgba(198, 40, 40, 0.2); border: 2px solid #c62828; color: #ef5350; text-shadow: 0 0 5px #c62828; }

        /* Draai-animatie voor vinyl */
        @keyframes spin-vinyl {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <header>
        <h1>HITJAM <span>3</span></h1>
        <div style="color: #ff6600; font-weight: bold;">⚡ ARCADE MODE</div>
    </header>

    <main>
        <!-- De Muziek / Animatie Kaart -->
        <div class="arcade-card">
            <div class="vinyl-container">
                <div class="music-vinyl" id="vinyl-disc">
                    <div class="vinyl-center">💿</div>
                </div>
            </div>

            <!-- De fysieke VU-meter met 9 bewegende balkjes -->
            <div class="vu-meter" id="vu-meter">
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
                <div class="vu-bar"></div>
            </div>
            
            <button class="btn-arcade" id="btn-play">▶️ Start Muziek</button>
            <div id="game-feedback" class="alert-box"></div>
        </div>

        <!-- Jaartal Kiezer (HitJam2 Stijl) -->
        <div class="year-selector-section" id="year-section">
            <span style="font-size: 12px; text-transform: uppercase; color: #888; font-weight: bold; tracking-letter: 1px;">Kies het juiste jaartal:</span>
            <div class="year-grid" id="year-grid">
                <!-- Wordt dynamisch gegenereerd in deel 2 -->
            </div>
        </div>
    </main>
    <script>
        let audioPlayer = null;
        let huidigSongId = null;
        let echtJaar = null;
        let vuInterval = null;

        document.getElementById('btn-play').addEventListener('click', startNieuwNummer);

        function startNieuwNummer() {
            // Reset oude status
            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();
            document.getElementById('game-feedback').style.display = 'none';
            document.getElementById('year-section').style.display = 'none';
            document.getElementById('vinyl-disc').style.animation = "none";
            document.getElementById('btn-play').innerText = "⏳ Laden...";

            // Haal willekeurig nummer op uit je Raspberry Pi backend
            fetch('hj3_get_next_song.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('btn-play').innerText = "▶️ Volgend Nummer";
                    
                    if (data.status === 'success') {
                        huidigSongId = data.id;
                        echtJaar = parseInt(data.year);
                        
                        // Start Apple audio stream direct via de browser
                        audioPlayer = new Audio(data.preview_url);
                        audioPlayer.play();
                        
                        // Activeer de animaties!
                        document.getElementById('vinyl-disc').style.animation = "spin-vinyl 3s linear infinite";
                        startVuMeter();
                        
                        // Genereer de HitJam keuzeknoppen (HitJam2-stijl)
                        genereerJaarKnoppen(echtJaar);
                    } else {
                        alert("Fout bij ophalen nummer: " + data.message);
                    }
                });
        }

        // De VU-Meter Animatie logica
        function startVuMeter() {
            const bars = document.querySelectorAll('.vu-bar');
            
            // Verander de hoogte van de balkjes elke 80 milliseconden voor een vloeiend ritme
            vuInterval = setInterval(() => {
                bars.forEach(bar => {
                    // Genereer een willekeurige hoogte tussen de 10px en 45px
                    const randomHoogte = Math.floor(Math.random() * 35) + 10;
                    bar.style.height = randomHoogte + 'px';
                });
            }, 80);
        }

        function stopVuMeter() {
            clearInterval(vuInterval);
            const bars = document.querySelectorAll('.vu-bar');
            bars.forEach(bar => { bar.style.height = '5px'; }); // Reset naar laagste stand
        }

        // Genereert 4 knoppen: 1 juiste en 3 willekeurige jaren in de buurt
        function genereerJaarKnoppen(correctJaar) {
            const grid = document.getElementById('year-grid');
            grid.innerHTML = ''; // Maak leeg
            
            let jarenSet = new Set();
            jarenSet.add(correctJaar);

            // Voeg 3 willekeurige valse jaren toe (binnen een straal van 8 jaar van het echte jaar)
            while (jarenSet.size < 4) {
                const afwijking = Math.floor(Math.random() * 17) - 8; // Tussen -8 en +8
                const nepJaar = correctJaar + afwijking;
                if (nepJaar > 1950 && nepJaar <= 2026) {
                    jarenSet.add(nepJaar);
                }
            }

            // Maak er een array van en schud de volgorde willekeurig door elkaar
            let keuzes = Array.from(jarenSet).sort(() => Math.random() - 0.5);

            // Bouw de fysieke knoppen in de HTML
            keuzes.forEach(jaar => {
                const btn = document.createElement('button');
                btn.className = 'year-btn';
                btn.innerText = jaar;
                btn.onclick = () => controleerJaarGok(jaar);
                grid.appendChild(btn);
            });

            // Toon het hele keuzepanepel met een vloeiende overgang
            document.getElementById('year-section').style.display = 'block';
        }

        function controleerJaarGok(gekozenJaar) {
            const fb = document.getElementById('game-feedback');
            fb.style.display = 'block';
            
            // Stop direct de muziek en de dansende VU-meter bij een antwoord
            if (audioPlayer) { audioPlayer.pause(); }
            stopVuMeter();
            document.getElementById('vinyl-disc').style.animationPlayState = 'paused';
            document.getElementById('year-section').style.display = 'none';

            if (gekozenJaar === echtJaar) {
                fb.className = "alert-box alert-success";
                fb.innerHTML = `🔥 LEKKER! ${gekozenJaar} IS CORRECT!<br><small>+10 Punten</small>`;
            } else {
                fb.className = "alert-box alert-error";
                fb.innerHTML = `❌ HELAAS! HET WAS ${echtJaar}.<br><small>Je koos ${gekozenJaar}</small>`;
            }
        }
    </script>
</body>
</html>
