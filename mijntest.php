<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HitJam3 - Technische Frontend Test</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #121212; color: #fff; max-width: 600px; margin: 0 auto; padding: 20px; text-align: center; }
        .card { background: #1e1e1e; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); margin-bottom: 20px; border: 1px solid #333; }
        button { background: #1DB954; color: white; border: none; padding: 12px 24px; font-size: 16px; font-weight: bold; border-radius: 25px; cursor: pointer; margin: 5px; transition: transform 0.2s, background 0.2s; }
        button:hover { background: #1ed760; transform: scale(1.05); }
        button.secondary { background: #333; border: 1px solid #555; }
        button.secondary:hover { background: #444; }
        button.danger { background: #e63946; }
        button.danger:hover { background: #ff4d6d; }
        .song-info { font-size: 18px; margin: 15px 0; color: #b3b3b3; display: none; }
        .alert { padding: 10px; border-radius: 6px; margin: 15px 0; font-weight: bold; display: none; }
        .success { background: #2e7d32; color: #fff; }
        .error { background: #c62828; color: #fff; }
        .timeline-mock { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>🎵 HitJam 3 Prototype</h1>
    <p>Testpaneel voor Audio &amp; Tijdlijn Logica</p>

    <!-- KAART 1: Muziek Bediening -->
    <div class="card">
        <h3>1. Muziek Motor</h3>
        <button id="btn-play">▶️ Speel willekeurig nummer</button>
        <button id="btn-fallback" class="danger" style="display:none;">Oeps, geen audio? Volgende!</button>
        <button id="btn-stop" class="secondary">⏸️ Stop Audio</button>
        
        <div id="song-display" class="song-info">
            Nu aan het laden...
        </div>
    </div>
    <script>
        let audioPlayer = null;

        document.getElementById('btn-play').addEventListener('click', haalNummerOp);
        document.getElementById('btn-stop').addEventListener('click', stopAudio);

        function haalNummerOp() {
            const songDisplay = document.getElementById('song-display');
            stopAudio(); // Stop eventuele oude muziek
            songDisplay.style.display = 'block';

            // Haal asynchroon (AJAX) data op bij je Raspberry Pi script [1]
            fetch('hj3_get_next_song.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        huidigSongId = data.id;
                        
                        // Toon de info (voor de admin/testomgeving)
                        songDisplay.innerHTML = `<strong>${data.artist}</strong> - ${data.title} <br><small>Echt jaar: ${data.year}</small>`;
                        //answeringCard.style.display = 'block';

                        // Check of er een werkende iTunes link is meegekomen
                        if (data.preview_url) {
                            // Start de native browser audioplayer direct op met de Apple link!
                            audioPlayer = new Audio(data.preview_url);
                            audioPlayer.play().catch(e => {
                                toonFeedback("Klik ergens op de pagina om audio af te spelen (Browser beveiliging)", "error");
                            });
                        } else {
                            songDisplay.innerHTML += "<br><span style='color:#e63946;'>❌ Geen preview_url gevonden voor dit nummer bij Apple!</span>";
                            fallbackBtn.style.display = 'inline-block'; // Toon de skip-knop
                        }
                    } else {
                        songDisplay.innerHTML = "Fout: " + data.message;
                    }
                })
                .catch(error => {
                    songDisplay.innerHTML = "Kon geen verbinding maken met de Pi backend.";
                    console.error(error);
                });
        }

        function stopAudio() {
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer = null;
            }
        }
        </script>

</body>
</html>