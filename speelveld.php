<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HitJam 3 - Speelveld</title>
    <!-- Inladen van Tailwind CSS via CDN -->
    <script src="https://tailwindcss.com"></script>
    <style>
        /* Verberg de scrollbar voor een strakker mobiel design */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#0b0c10] text-white font-sans min-h-screen flex flex-col justify-between">

    <!-- Top Bar -->
    <header class="p-4 bg-[#1f2833]/40 backdrop-blur-md border-b border-[#45f3ff]/20 flex justify-between items-center">
        <h1 class="text-xl font-black tracking-wider text-[#45f3ff]">HITJAM <span class="text-white">3</span></h1>
        <div class="flex items-center gap-2">
            <span class="bg-[#1f2833] text-[#45f3ff] px-3 py-1 rounded-full text-xs font-bold border border-[#45f3ff]/30">⚡ 3 Tokens</span>
        </div>
    </header>

    <!-- Centrale Audio Kaart -->
    <main class="flex-grow flex flex-col items-center justify-center p-6 gap-6">
        <div class="bg-[#1f2833]/60 border border-gray-800 p-8 rounded-3xl w-full max-w-sm text-center shadow-2xl relative overflow-hidden backdrop-blur-sm">
            <!-- Pulsing neon effect op de achtergrond tijdens het afspelen -->
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-[#45f3ff]/10 rounded-full blur-3xl animate-pulse"></div>
            
            <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold mb-2">Nu aan het luisteren...</p>
            <div class="w-24 h-24 bg-gradient-to-tr from-[#45f3ff] to-[#1f2833] rounded-full mx-auto flex items-center justify-center shadow-lg shadow-[#45f3ff]/20 animate-spin-[spin_3s_linear_infinite]">
                <span class="text-3xl">🎵</span>
            </div>
            
            <h2 class="text-lg font-bold mt-4 tracking-wide text-gray-200">Raad het jaartal!</h2>
            <p class="text-xs text-gray-400 mt-1">Plaats het nummer op de juiste plek in je tijdlijn hierboven of hieronder.</p>
        </div>
    </main>

    <!-- Interactieve Tijdlijn Sectie -->
    <footer class="p-6 bg-[#1f2833]/20 border-t border-gray-900 backdrop-blur-md">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 px-2">Jouw Tijdlijn (Chronologisch)</h3>
        
        <!-- Horizontale scrollbare container -->
        <div class="flex items-center gap-3 overflow-x-auto pb-4 no-scrollbar snap-x px-2" id="timeline-container">
            <!-- JavaScript vult dit dynamisch op basis van de array! -->
        </div>
    </footer>

    <script>
        // Fictieve speler-tijdlijn om het visuele ontwerp direct te testen.
        // In de uiteindelijke game halen we deze array live op uit player_timelines via PHP.
        let spelerTijdlijn = [
            { id: 10, artist: "Michael Jackson", title: "Thriller", year: 1982 },
            { id: 14, artist: "Nirvana", title: "Smells Like Teen Spirit", year: 1991 },
            { id: 22, artist: "Lady Gaga", title: "Bad Romance", year: 2009 }
        ];

        function tekenTijdlijn() {
            const container = document.getElementById('timeline-container');
            container.innerHTML = ''; // Maak leeg voor her-render

            // 1. Altijd een plusknop aan het héle begin (voor nummers ouder dan de eerste kaart)
            container.appendChild(maakPlusKnop(0, spelerTijdlijn[0]?.id || 0));

            // 2. Loop door de kaarten heen
            spelerTijdlijn.forEach((card, index) => {
                // Voeg de song-kaart toe
                container.appendChild(maakSongKaart(card));

                // Bepaal wat de volgende kaart is om de grenzen voor de plusknop mee te geven
                const volgendeKaart = spelerTijdlijn[index + 1];
                
                if (volgendeKaart) {
                    // Plusknop tussen twee kaarten in
                    container.appendChild(maakPlusKnop(card.id, volgendeKaart.id));
                } else {
                    // Plusknop aan het héle einde (voor nummers nieuwer dan de laatste kaart)
                    container.appendChild(maakPlusKnop(card.id, 0));
                }
            });
        }

        function maakSongKaart(card) {
            const div = document.createElement('div');
            div.className = "min-w-[120px] max-w-[120px] bg-gradient-to-b from-[#1f2833] to-[#0b0c10] border-2 border-gray-700 p-3 rounded-2xl text-center shadow-xl snap-center flex flex-col justify-between h-32";
            div.innerHTML = `
                <div class="text-[10px] font-bold text-gray-400 uppercase truncate">${card.artist}</div>
                <div class="text-xs font-semibold tracking-tight text-white line-clamp-2 my-1">${card.title}</div>
                <div class="text-sm font-black text-[#45f3ff] bg-[#45f3ff]/10 py-0.5 rounded-lg border border-[#45f3ff]/20 mt-auto">${card.year}</div>
            `;
            return div;
        }

        function maakPlusKnop(beforeId, afterId) {
            const btn = document.createElement('button');
            btn.className = "min-w-[44px] h-11 rounded-full bg-[#45f3ff]/10 hover:bg-[#45f3ff] border-2 border-dashed border-[#45f3ff]/40 hover:border-[#45f3ff] text-[#45f3ff] hover:text-black font-black text-xl flex items-center justify-center transition-all duration-200 transform hover:scale-110 active:scale-95 shadow-md flex-shrink-0 mx-1";
            btn.innerHTML = "+";
            btn.onclick = () => gokPlaatsen(beforeId, afterId);
            return btn;
        }

        function gokPlaatsen(beforeId, afterId) {
            alert(`Je gokt dat het huidige nummer geplaatst moet worden tussen Song ID: ${beforeId} en Song ID: ${afterId}!`);
            // Hier koppelen we in de volgende stap de FETCH POST naar hj3_check_answer.php aan!
        }

        // Teken direct de interface bij het laden van de pagina
        tekenTijdlijn();
    </script>
</body>
</html>
