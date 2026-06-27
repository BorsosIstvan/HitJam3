<?php
// Vertel de browser dat dit een oneindige live data-stroom is
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

require_once('hj3_db.php');

// Schakel PHP-buffer uit zodat data direct wordt verzonden
while (ob_get_level() > 0) { ob_end_flush(); }
flush();

$laatste_song_id = 0;
$laatste_status = '';

// Oneindige loop op de Pi die luistert naar veranderingen
for ($i = 0; $i < 30; $i++) { // Loopt 30 seconden, daarna herlaadt de browser de stream veilig
    try {
        // Haal de huidige status op uit de database
        $stmt = $db->query("SELECT current_song_id, status FROM game_status WHERE id = 1");
        $game = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($game) {
            // Is er een nieuw nummer gestart?
            if ($game['current_song_id'] != $laatste_song_id || $game['status'] != $laatste_status) {
                $laatste_song_id = $game['current_song_id'];
                $laatste_status = $game['status'];

                // Haal de audio link op van het actieve nummer
                $song_stmt = $db->prepare("SELECT preview_url FROM game_songs WHERE id = ?");
                $song_stmt->execute([$laatste_song_id]);
                $preview_url = $song_stmt->fetchColumn();

                // Stuur het live JSON-signaal naar de telefoons
                echo "data: " . json_encode([
                    'action' => 'new_song',
                    'song_id' => $laatste_song_id,
                    'preview_url' => $preview_url,
                    'status' => $laatste_status
                ]) . "\n\n";
                
                flush();
            }
        }
    } catch (Exception $e) {
        // Stille opvang bij database locks
    }

    // Wacht 250 milliseconden voor de volgende check (vlijmscherp en super licht voor de Pi)
    usleep(250000);
}
?>
