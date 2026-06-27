<?php
require_once('hj3_db.php');

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Zoek de gebruiker op in de database
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Controleer of de gebruiker bestaat en het wachtwoord klopt
            if ($user && password_verify($password, $user['password'])) {
                // Sla gegevens op in de sessie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Automatisch toevoegen aan de actieve spelerslijst in de game
                $db->exec("INSERT OR IGNORE INTO game_players (username, tokens, is_host) VALUES ('".$user['username']."', 3, 0)");

                // Stuur door naar het speelveld!
                header("Location: speelveld.php");
                exit;
            } else {
                $error_msg = "❌ ONJUISTE GEBRUIKERSNAAM OF WACHTWOORD!";
            }
        } catch (Exception $e) {
            $error_msg = "Fout: " . $e->getMessage();
        }
    } else {
        $error_msg = "⚠️ VUL ALLE VELDEN IN!";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 3 - Inloggen</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #0b0c10; color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #160c13 0%, #0b0c10 100%); padding: 30px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: center; text-align: center; box-shadow: 0 0 30px rgba(0,0,0,0.6); }
        .logo { font-size: 36px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin-bottom: 30px; }
        h2 { font-size: 18px; text-transform: uppercase; color: #ff2d55; margin-bottom: 20px; letter-spacing: 1px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        label { font-size: 12px; text-transform: uppercase; color: #888; font-weight: bold; display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; font-size: 16px; box-sizing: border-box; }
        input:focus { border-color: #ff9500; outline: none; background: rgba(255,255,255,0.08); }
        .btn { width: 100%; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; text-transform: uppercase; margin-top: 15px; background: linear-gradient(90deg, #ff9500, #ff2d55); color: white; box-shadow: 0 6px 20px rgba(255, 149, 0, 0.3); }
        .btn:active { transform: scale(0.97); }
        .link-text { margin-top: 20px; font-size: 14px; color: #b3b3b3; }
        .link-text a { color: #ff2d55; text-decoration: none; font-weight: bold; }
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 15px; font-weight: bold; font-size: 13px; background: rgba(255,45,85,0.15); border: 1px solid #ff2d55; color: #ff2d55; }
    </style>
</head>
<body>
    <div class="app-container">
        <h1 class="logo">HitJam 3</h1>
        <h2>Arcade Login</h2>

        <?php if (!empty($error_msg)): ?>
            <div class="alert"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Gebruikersnaam</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Wachtwoord</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Inloggen ⚡</button>
        </form>

        <p class="link-text">Nieuwe speler? <a href="register.php">Account aanmaken</a></p>
    </div>
</body>
</html>
