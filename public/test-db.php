<?php
/**
 * Skrypt testowy połączenia z bazą danych
 * Uruchom: http://localhost/test-db.php
 */

require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../src/Helpers/url.php';
require_once __DIR__ . '/../src/classes/Database.php';

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test połączenia z bazą danych</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 3rem;
        }
        
        h1 {
            color: #1a202c;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        
        .test-section {
            background: #f7fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .test-section h2 {
            color: #2d3748;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        
        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .status.success {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .status.error {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .info {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #4a5568;
        }
        
        .info-value {
            color: #2d3748;
        }
        
        .table-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .table-item {
            background: white;
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #2d3748;
        }
        
        .missing-table {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .error-message {
            background: #fed7d7;
            color: #742a2a;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1.5rem;
            transition: background 0.2s;
        }
        
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Test połączenia z bazą danych</h1>
        
        <!-- Test połączenia -->
        <div class="test-section">
            <h2>1. Połączenie z MySQL</h2>
            <?php
            $connectionTest = Database::testConnection();
            
            if ($connectionTest['success']): ?>
                <div class="status success">✓ Połączenie aktywne</div>
                <div class="info">
                    <div class="info-row">
                        <span class="info-label">Baza danych:</span>
                        <span class="info-value"><?= htmlspecialchars($connectionTest['database']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Liczba tabel:</span>
                        <span class="info-value"><?= $connectionTest['tables_count'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value"><?= htmlspecialchars($connectionTest['message']) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <div class="status error">✗ Błąd połączenia</div>
                <div class="error-message">
                    <?= htmlspecialchars($connectionTest['error']) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Test tabel -->
        <div class="test-section">
            <h2>2. Struktura bazy danych</h2>
            <?php
            $tablesCheck = Database::checkTables();
            
            if ($tablesCheck['success']): ?>
                <div class="status success">✓ Wszystkie wymagane tabele istnieją</div>
                <div class="info">
                    <div class="info-row">
                        <span class="info-label">Istniejące tabele:</span>
                        <span class="info-value"><?= $tablesCheck['total'] ?> / <?= $tablesCheck['required'] ?></span>
                    </div>
                </div>
                <div class="table-list">
                    <?php foreach ($tablesCheck['existing_tables'] as $table): ?>
                        <div class="table-item">📊 <?= htmlspecialchars($table) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?php if (isset($tablesCheck['error'])): ?>
                    <div class="status error">✗ Błąd sprawdzania tabel</div>
                    <div class="error-message">
                        <?= htmlspecialchars($tablesCheck['error']) ?>
                    </div>
                <?php else: ?>
                    <div class="status error">✗ Brak wymaganych tabel</div>
                    <div class="info">
                        <div class="info-row">
                            <span class="info-label">Istniejące:</span>
                            <span class="info-value"><?= $tablesCheck['total'] ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Brakujące:</span>
                            <span class="info-value"><?= count($tablesCheck['missing_tables']) ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($tablesCheck['existing_tables'])): ?>
                        <h3 style="margin-top: 1.5rem; color: #2d3748;">Istniejące tabele:</h3>
                        <div class="table-list">
                            <?php foreach ($tablesCheck['existing_tables'] as $table): ?>
                                <div class="table-item">📊 <?= htmlspecialchars($table) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($tablesCheck['missing_tables'])): ?>
                        <h3 style="margin-top: 1.5rem; color: #742a2a;">Brakujące tabele:</h3>
                        <div class="table-list">
                            <?php foreach ($tablesCheck['missing_tables'] as $table): ?>
                                <div class="table-item missing-table">⚠️ <?= htmlspecialchars($table) ?></div>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin-top: 1rem; color: #4a5568;">
                            💡 Uruchom plik <code>database/eventapp.sql</code> w phpMyAdmin lub MySQL, aby utworzyć brakujące tabele.
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <a href="/" class="btn">← Powrót do strony głównej</a>
    </div>
</body>
</html>
