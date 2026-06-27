<?php
require_once('hj3_db.php');
header('Content-Type: application/json; charset=utf-8');

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
// Sluit de sessie-lock direct om blokkades bij polling te voorkomen!
session_write_close(); 

if (empty($username)) {
    echo json_encode(['status' => 'error', 'message' => 'Niet ingelogd']);
    exit;
}

try {
    // 1. UPDATE DE HARTSLAG: Zet de huidige tijd bij de actieve speler
    // (We gebruiken INSERT OR REPLACE zodat de speler er sowieso in staat)
    $stmt = $db->prepare("INSERT OR REPLACE INTO game_players (username, tokens, is_active, last_seen) 
                          VALUES (?, COALESCE((SELECT tokens FROM game_players WHERE username = ?), 3), 1, datetime('now'))");
    $stmt->execute([$username, $username]);

    // 2. HAAL ONLINE SPELERS OP: Selecteer iedereen die de afgelopen 10 seconden actief was
    // SQLite gebruikt 'now', '-10 seconds' om terug in de tijd te rekenen
    $online_stmt = $db->query("SELECT username FROM game_players WHERE last_seen > datetime('now', '-10 seconds') ORDER BY username ASC");
    $online_spelers = $online_stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'status' => 'success',
        'me' => $username,
        'online' => $online_spelers
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
