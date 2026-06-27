<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HitJam 3 - Speelveld</title>
    <style>
        /* CSS Direct in het bestand: werkt altijd super snel op je Pi! */
        body {
            background-color: #0b0c10;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Top Bar */
        header {
            background-color: rgba(31, 40, 51, 0.6);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(69, 243, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #45f3ff;
        }
        header h1 span { color: #ffffff; }
        .token-badge {
            background-color: #1f2833;
            color: #45f3ff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid rgba(69, 243, 255, 0.3);
        }

        /* Centrale Audio Kaart */
        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .audio-card {
            background-color: rgba(31, 40, 51, 0.5);
            border: 1px solid #222;
            padding: 30px;
            border-radius: 24px;
            width: 100%;
            max-width: 340px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .music-vinyl {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #45f3ff, #1f2833);
            border-radius: 50%;
            margin: 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            box-shadow: 0 0 20px rgba(69, 243, 255, 0.2);
        }
        .btn-main {
            background-color: #45f3ff;
            color: #0b0c10;
            border: none;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 10px;
        }
        .btn-main:hover { transform: scale(1.05); }

        /* Feedback meldingen */
        .feedback {
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            font-weight: bold;
            display: none;
        }
        .correct { background-color: #2e7d32; color: white; }
        .wrong { background-color: #c62828; color: white; }

        /* Tijdlijn Sectie onderaan */
        footer {
            background-color: rgba(31, 40, 51, 0.2);
            border-top: 1px solid #111;
            padding: 20px;
        }
        footer h3 {
            margin: 0 0 12px 5px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
        }
        .timeline-container {
            display: flex;
            align-items: center;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        /* Verberg scrollbar */
        .timeline-container::-webkit-scrollbar { display: none; }
        .timeline-container { -ms-overflow-style: none; scrollbar-width: none; }

        /* De Song Kaarten in de tijdlijn */
        .song-card {
            min-width: 110px;
            max-width: 110px;
            height: 110px;
            background: linear-gradient(#1f2833, #0b0c10);
            border: 2px solid #444;
            padding: 10px;
            border-radius: 16px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .song-artist { font-size: 9px; font-weight: bold; color: #aaa; text-transform: uppercase; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .song-title { font-size: 11px; font-weight: 600; color: #fff; margin: 4px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .song-year { font-size: 13px; font-weight: 900; color: #45f3ff; background-color: rgba(69, 243, 255, 0.1); border: 1px solid rgba(69, 243, 255, 0.2); padding: 2px; border-radius: 6px; }

        /* Plus Knoppen */
        .plus-btn {
            min-width: 40px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(69, 243, 255, 0.1);
            border: 2px dashed rgba(69, 243, 255, 0.4);
            color: #45f3ff;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .plus-btn:hover {
            background-color: #45f3ff;
            color: #0b0c10;
            border-style: solid;
            transform: scale(1.1);
        }
        
        /* CSS Animatie voor de vinyl disc */
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <header>
        <h1>HITJAM <span>3</span></h1>
        <div class="token-badge">⚡ 3 Tokens</div>
    </header>

    <main>
        <div class="audio-card">
            <p style="font-size: 11px; color: #888; text-transform: uppercase; margin: 0;">HitJam3 Audio Speler</p>
            <div class="music-vinyl" id="vinyl">🎵</div>
            
            <button class="btn-main" id="btn-play">▶️ Volgend Nummer</button>
            
            <div id="feedback" class="feedback"></div>
        </div>
    </main>

    <footer>
        <h3>Jouw Tijdlijn</h3>
        <div class="timeline-container" id="timeline-container">
            <!-- Wordt gevuld door JavaScript uit deel 2 -->
        </div>
    </footer>
    <script>
        let audioPlayer = null;
        let huidigSongId = null;

        // Startpunt van de speler: alvast 1 kaart cadeau om mee te beginnen
        let spelerTijdlijn = [
            { id: 9999, artist: "Basis Jaar", title: "Startpunt", year: 2000 }
        ];

        document.getElementById('btn-play').addEventListener('click', startNieuwNummer);

        function startNieuwNummer() {
            if (audioPlayer) { audioPlayer.pause(); }
            document.getElementById('feedback').style.display = 'none';
            document.getElementById('btn-play').innerText = "⏳ Laden...";

            fetch('hj3_get_next_song.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('btn-play').innerText = "▶️ Volgend Nummer";
                    if (data.status === 'success') {
                        huidigSongId = data.id;
                        
                        // Start Apple audio stream direct
                        audioPlayer = new Audio(data.preview_url);
                        audioPlayer.play();
                        
                        // Laat de vinyl-disc visueel spinnen
                        document.getElementById('vinyl').style.animation = "spin 2s linear infinite";
                    }
                });
        }

        function tekenTijdlijn() {
            const container = document.getElementById('timeline-container');
            container.innerHTML = '';

            // Sorteer de kaarten altijd netjes chronologisch
            spelerTijdlijn.sort((a, b) => a.year - b.year);

            // Eerste plusknop aan de linkerkant
            container.appendChild(maakPlusKnop(0, spelerTijdlijn[0].id));

            spelerTijdlijn.forEach((card, index) => {
                // Maak de songkaart aan
                const cardDiv = document.createElement('div');
                cardDiv.className = 'song-card';
                cardDiv.innerHTML = `
                    <div class="song-artist">${card.artist}</div>
                    <div class="song-title">${card.title}</div>
                    <div class="song-year">${card.year}</div>
                `;
                container.appendChild(cardDiv);

                // Plusknop tussen de kaarten of helemaal aan het einde
                const volgende = spelerTijdlijn[index + 1];
                if (volgende) {
                    container.appendChild(maakPlusKnop(card.id, volgende.id));
                } else {
                    container.appendChild(maakPlusKnop(card.id, 0));
                }
            });
        }

        function maakPlusKnop(beforeId, afterId) {
            const btn = document.createElement('button');
            btn.className = 'plus-btn';
            btn.innerText = '+';
            btn.onclick = () => controleerGok(beforeId, afterId);
            return btn;
        }

        function controleerGok(beforeId, afterId) {
            if (!huidigSongId) { alert("Start eerst een nummer!"); return; }

            let formData = new FormData();
            formData.append('player_id', 1); // Testen als speler 1
            formData.append('current_song_id', huidigSongId);
            formData.append('song_before_id', beforeId);
            formData.append('song_after_id', afterId);

            fetch('hj3_check_answer.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    const fb = document.getElementById('feedback');
                    fb.style.display = 'block';
                    
                    if (audioPlayer) { audioPlayer.pause(); }
                    document.getElementById('vinyl').style.animation = "none";

                    if (data.result === 'correct') {
                        fb.className = "feedback correct";
                        fb.innerHTML = `🎉 GOED! Het jaar was ${data.year}.`;
                        
                        // Voeg het zojuist geraden nummer toe aan de lokale tijdlijn
                        spelerTijdlijn.push({
                            id: huidigSongId,
                            artist: "Geraden",
                            title: "Liedje",
                            year: data.year
                        });
                        
                        huidigSongId = null; // Reset ronde
                        tekenTijdlijn();    // Teken de tijdlijn direct opnieuw!
                    } else {
                        fb.className = "feedback wrong";
                        fb.innerHTML = `😢 FOUT! Het jaar was ${data.year}.`;
                        huidigSongId = null;
                    }
                });
        }

        // Teken de start-tijdlijn direct in beeld
        tekenTijdlijn();
    </script>
</body>
</html>
