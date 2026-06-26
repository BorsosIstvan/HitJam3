<?php
session_start();
require_once('hj3_db.php');

header('Content-Type: application/json; charset=utf-8');

// We stoppen de logica in een functie zodat we deze makkelijk kunnen herhalen 
// als een nummer 'NOT_FOUND' blijkt te zijn bij Apple.
function haalWillekeurigNummer($db) {
    // 1. Kies willekeurig één liedje dat NIET gemarkeerd is als onvindbaar
    $stmt = $db->query("SELECT id, artist, title, year, preview_url FROM game_songs WHERE preview_url IS NOT 'NOT_FOUND' ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$song) {
        return ['status' => 'error', 'message' => 'Geen speelbare liedjes gevonden.'];
    }

    $preview_url = $song['preview_url'];

    // 2. Als de preview_url nog NULL (leeg) is, gaan we deze NU live zoeken en opslaan!
    if (empty($preview_url)) {
        $schone_artiest = str_replace('&', ' ', $song['artist']);
        $zoekterm = urlencode($schone_artiest . " " . $song['title']);
        $api_url = "https://apple.com" . $zoekterm . "&limit=1&entity=song";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 4); // Snelle timeout
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $json = json_decode($response, true);
            // Gewijzigd naar correcte iTunes API response structuur indexering [0]
            if (isset($json['results'][0]['previewUrl'])) {
                $preview_url = $json['results'][0]['previewUrl'];
            }
        }

        // 3. Update de database met het resultaat
        if (!empty($preview_url)) {
            // Link gevonden! Sla op voor de volgende keer
            $update_stmt = $db->prepare("UPDATE game_songs SET preview_url = ? WHERE id = ?");
            $update_stmt->execute([$preview_url, $song['id']]);
        } else {
            // Niet gevonden bij Apple? Markeer als NOT_FOUND zodat we dit nummer nooit meer proberen te laden
            $update_stmt = $db->prepare("UPDATE game_songs SET preview_url = 'NOT_FOUND' WHERE id = ?");
            $update_stmt->execute([$song['id']]);
            
            // Omdat dit nummer onspeelbaar is, roepen we de functie direct NOG een keer aan
            // voor een nieuw willekeurig nummer. De speler merkt hier niks van!
            return haalWillekeurigNummer($db);
        }
    }

    // 4. Update de huidige actieve spelstatus op de Pi
    $status_stmt = $db->prepare("UPDATE game_status SET current_song_id = ?, music_started = 1, start_time = ? WHERE id = 1");
    $status_stmt->execute([$song['id'], microtime(true)]);

    // 5. Geef de schone data terug voor de frontend
    return [
        'status' => 'success',
        'id' => $song['id'],
        'artist' => $song['artist'],
        'title' => $song['title'],
        'preview_url' => $preview_url,
        'year' => $song['year']
    ];
}

try {
    $db_path = '/var/www/html/HitData/hitjam3.db';
    $db = new PDO("sqlite:" .$db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $resultaat = haalWillekeurigNummer($db);
    echo json_encode($resultaat);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
