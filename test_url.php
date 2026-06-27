<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
try{
    // Open verbinding met HitJam3 SQLite database
    $db_path = '/var/www/html/HitData/hitjam3.db';
    $db = new PDO("sqlite:" .$db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Kies willekeurig één liedje uit de database
    $stmt = $db->query("SELECT id, artist, title, year, preview_url FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);

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

    if ($response) {
        $json = json_decode($response, true);
        if (isset($json['results'][0]['previewUrl'])) {
            $preview_url = $json['results'][0]['previewUrl'];
        }
    }

      echo json_encode([
        'status' => 'success',
        'id' => $song['id'],
        'artist' => $song['artist'],
        'title' => $song['title'],
        'preview_url' => $preview_url,
        'year' => $song['year'] // Handig voor de host-view om te controleren!
    ]);
}catch (Exception $e){
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>