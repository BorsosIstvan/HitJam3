<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Functie om snel het jaartal van een song-ID op te halen
function getYear($db, $song_id) {
    if (!$song_id || $song_id == 0) return null;
    $stmt = $db->prepare("SELECT year FROM game_songs WHERE id = ?");
    $stmt->execute([$song_id]);
    return $stmt->fetchColumn();
}

try {
    $db_path = '/var/www/html/HitData/hitjam3.db';
    $db = new PDO("sqlite:" .$db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Haal de input op van de JavaScript-frontend
    $player_id      = isset($_POST['player_id']) ? (int)$_POST['player_id'] : 0;
    $current_song_id= isset($_POST['current_song_id']) ? (int)$_POST['current_song_id'] : 0;
    $song_before_id = isset($_POST['song_before_id']) ? (int)$_POST['song_before_id'] : 0;
    $song_after_id  = isset($_POST['song_after_id']) ? (int)$_POST['song_after_id'] : 0;

    if (!$player_id || !$current_song_id) {
        echo json_encode(['status' => 'error', 'message' => 'Onvolledige gegevens ontvangen.']);
        exit;
    }

    // 1. Haal de jaartallen op uit de database
    $current_year = getYear($db, $current_song_id);
    $year_before  = getYear($db, $song_before_id); // Geeft null als dit de allereerste plek is
    $year_after   = getYear($db, $song_after_id);  // Geeft null als dit de allerlaatste plek is

    // 2. Stel de chronologische grenzen in
    $min_year = ($year_before !== null) ? $year_before : 0;       // Geen ondergrens? Dan jaar 0
    $max_year = ($year_after !== null) ? $year_after : 3000;     // Geen bovengrens? Dan jaar 3000

    // 3. De Grote Chronologische Check
    $is_correct = ($current_year >= $min_year && $current_year <= $max_year);

    if ($is_correct) {
        // GOED GERADEN! Voeg het nummer permanent toe aan de tijdlijn van deze speler
        $insert = $db->prepare("INSERT INTO player_timelines (player_id, song_id) VALUES (?, ?)");
        $insert->execute([$player_id, $current_song_id]);

        echo json_encode([
            'status' => 'success',
            'result' => 'correct',
            'message' => 'Liedje is correct geplaatst!',
            'year' => $current_year
        ]);
    } else {
        // FOUT GERADEN! Het liedje wordt niet toegevoegd
        echo json_encode([
            'status' => 'success',
            'result' => 'wrong',
            'message' => 'Helaas, dat is niet de juiste plek in de tijdlijn.',
            'year' => $current_year,
            'boundaries' => ['moest_tussen' => $min_year . ' en ' . $max_year]
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
