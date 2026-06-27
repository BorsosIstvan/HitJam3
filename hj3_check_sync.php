<?php
require_once('hj3_db.php');
header('Content-Type: application/json; charset=utf-8');

try {
    // Haal het nummer-id op dat NU actief is in de game
    $stmt = $db->query("SELECT current_song_id FROM game_status WHERE id = 1");
    $current_song_id = $stmt->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'current_song_id' => (int)$current_song_id
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
