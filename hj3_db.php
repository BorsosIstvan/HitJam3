<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Open de HitJam3 database op de Raspberry Pi
    $db_path = '/var/www/html/HitData/hitjam3.db';
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database verbindingsfout: " . $e->getMessage());
}
?>