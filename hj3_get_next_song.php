<?php
session_start();
// Optioneel: Zet je admin check hier terug als je dit alleen voor de host wilt toestaan
// if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') { header('HTTP/1.1 403 Forbidden'); exit; }

require_once('hj3_db.php'); // Zorg dat dit script verbinding maakt met hitjam3.db
session_write_close();

header('Content-Type: application/json; charset=utf-8');

try {
    // Open verbinding met HitJam3 SQLite database
    $db_path = '/var/www/html/HitData/hitjam3.db';
    $db = new PDO("sqlite:" .$db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Kies willekeurig één liedje uit de database
    $stmt = $db->query("SELECT id, artist, title, year, preview_url FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$song) {
        echo json_encode(['error' => 'Geen liedjes gevonden in de database.']);
        exit;
    }

    $preview_url = $song['preview_url'];

    // 2. Als er nog geen preview_url gecachet is, halen we hem nu éénmalig op bij Apple
    if (empty($preview_url) or $preview_url=='NOT_FOUND') {
        // Maak de zoekterm schoon voor de URL
        $schone_artiest = str_replace('&', ' ', $song['artist']);
        $zoekterm = urlencode($schone_artiest . " " . $song['title']);
        $api_url = "https://itunes.apple.com/search?term=" . $zoekterm . "&limit=1&entity=song";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Lekker strakke timeout van 5 seconden
        $response = curl_exec($ch);
        curl_close($ch);

        $preview_url = "";

        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['results'][0]['previewUrl'])) {
                $preview_url = $json['results'][0]['previewUrl'];

                // Sla de gevonden link direct op in de database voor de volgende keer!
                $update_stmt = $db->prepare("UPDATE game_songs SET preview_url = ? WHERE id = ?");
                $update_stmt->execute([$preview_url, $song['id']]);
            }
        }
    }

    // 3. Update de huidige game_status zodat de database weet welk nummer er nu draait
    // (We gaan er hier vanuit dat we met één actieve game-sessie op ID 1 testen)
    $status_stmt = $db->prepare("UPDATE game_status SET current_song_id = ?, music_started = 1, start_time = ? WHERE id = 1");
    $status_stmt->execute([$song['id'], microtime(true)]);

    // 4. Stuur de gegevens netjes terug als JSON
    // LET OP: Voor de spelers wil je het jaartal geheim houden, maar voor de admin/host sturen we hem mee
    echo json_encode([
        'status' => 'success',
        'id' => $song['id'],
        'artist' => $song['artist'],
        'title' => $song['title'],
        'preview_url' => $preview_url,
        'year' => $song['year'] // Handig voor de host-view om te controleren!
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
