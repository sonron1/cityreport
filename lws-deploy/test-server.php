<?php
// test-server.php - Test serveur LWS
phpinfo();

echo "<hr><h2>Test Base de données</h2>";
try {
    $pdo = new PDO('pgsql:host=localhost;dbname=cp2608560p22_cityflow', 'cp2608560p22_manto98', 'Akoutika1998');
    echo "✅ Connexion PostgreSQL réussie!<br>";
    
    $version = $pdo->query('SELECT version()')->fetchColumn();
    echo "Version: " . htmlspecialchars($version) . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur: " . htmlspecialchars($e->getMessage());
}