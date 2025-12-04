<?php
/**
 * Test rejestracji - sprawdzenie czy wszystkie klasy działają poprawnie
 */

require_once __DIR__ . '/../config/paths.php';
require_once SRC_PATH . '/Helpers/url.php';

// Załaduj klasy
load_class('Database');
load_class('Security');
load_class('EmailVerification');
require_once SRC_PATH . '/Models/User.php';
require_once SRC_PATH . '/Controllers/Auth/RegisterController.php';

session_start();

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Rejestracji</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .test { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        pre { background: white; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 Test Systemu Rejestracji</h1>
    
    <div class="test">
        <h2>1. Test połączenia z bazą</h2>
        <?php
        $dbTest = Database::testConnection();
        if ($dbTest['success']) {
            echo '<p class="success">✓ Połączenie działa</p>';
            echo '<pre>' . print_r($dbTest, true) . '</pre>';
        } else {
            echo '<p class="error">✗ Błąd połączenia</p>';
            echo '<pre>' . print_r($dbTest, true) . '</pre>';
        }
        ?>
    </div>
    
    <div class="test">
        <h2>2. Test generowania tokenu CSRF</h2>
        <?php
        $token = Security::generateCsrfToken();
        echo '<p class="success">✓ Token wygenerowany: ' . substr($token, 0, 20) . '...</p>';
        echo '<p>Weryfikacja tokenu: ' . (Security::verifyCsrfToken($token) ? '✓ OK' : '✗ Błąd') . '</p>';
        ?>
    </div>
    
    <div class="test">
        <h2>3. Test hashowania hasła</h2>
        <?php
        $password = 'Test123!@#';
        $hash = Security::hashPassword($password);
        $verify = Security::verifyPassword($password, $hash);
        echo '<p class="success">✓ Hasło zahashowane</p>';
        echo '<p>Hash: ' . substr($hash, 0, 30) . '...</p>';
        echo '<p>Weryfikacja: ' . ($verify ? '✓ OK' : '✗ Błąd') . '</p>';
        ?>
    </div>
    
    <div class="test">
        <h2>4. Test sprawdzenia czy email istnieje</h2>
        <?php
        $testEmail = 'test@example.com';
        $exists = User::emailExists($testEmail);
        echo '<p>Email "' . $testEmail . '": ' . ($exists ? 'Istnieje' : 'Nie istnieje') . '</p>';
        ?>
    </div>
    
    <div class="test">
        <h2>5. Test Rate Limiting</h2>
        <?php
        $canProceed = Security::checkRateLimit('test', 3, 60);
        echo '<p>' . ($canProceed ? '✓ Można kontynuować' : '✗ Limit przekroczony') . '</p>';
        echo '<p>IP: ' . Security::getClientIp() . '</p>';
        ?>
    </div>
    
    <div class="test">
        <h2>6. Formularz testowy rejestracji</h2>
        <form method="post" action="register.php" style="background: white; padding: 20px; border-radius: 5px;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
            
            <div style="margin: 10px 0;">
                <label>Nazwa użytkownika:</label><br>
                <input type="text" name="username" value="testuser<?= rand(100, 999) ?>" required style="width: 100%; padding: 8px;">
            </div>
            
            <div style="margin: 10px 0;">
                <label>Email:</label><br>
                <input type="email" name="email" value="test<?= rand(100, 999) ?>@example.com" required style="width: 100%; padding: 8px;">
            </div>
            
            <div style="margin: 10px 0;">
                <label>Hasło:</label><br>
                <input type="password" name="password" value="Test123!@#" required style="width: 100%; padding: 8px;">
            </div>
            
            <input type="hidden" name="role" value="user">
            
            <button type="submit" style="background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                Testuj Rejestrację
            </button>
        </form>
    </div>
    
    <p style="margin-top: 30px;">
        <a href="register.php">← Powrót do normalnego formularza rejestracji</a>
    </p>
</body>
</html>
