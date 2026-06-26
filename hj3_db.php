<?php
// Foutrapportage aan tijdens het bouwen zodat we alles zien
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 🔥 NIEUW EN ONAFHANKELIJK DATABASESCHIP
    $db_path = '/var/www/html/HitData/hitjam3.db';
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } 
catch (Exception $e) {
    die("<div style='color:red; font-family:sans-serif; padding:20px;'>❌ Database startfout: " . $e->getMessage() . "</div>");
}
?>