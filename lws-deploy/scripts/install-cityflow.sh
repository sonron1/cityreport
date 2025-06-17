#!/bin/bash
# install-cityflow.sh - À exécuter sur le serveur LWS

echo "🚀 Installation City Report sur LWS"
echo "=================================="

# Vérifier PHP
echo "🔍 Vérification PHP..."
php -v

# Vérifier Composer
echo "🔍 Vérification Composer..."
if ! command -v composer &> /dev/null; then
    echo "❌ Composer non trouvé"
    echo "💡 Installez Composer ou utilisez php composer.phar"
else
    echo "✅ Composer disponible"
fi

# Installation des dépendances
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction

# Test connexion base
echo "🗄️ Test connexion base de données..."
php -r "
try {
    \$pdo = new PDO('pgsql:host=localhost;dbname=cp2608560p22_cityflow', 'cp2608560p22_manto98', 'Akoutika1998');
    echo '✅ Connexion PostgreSQL OK\n';
} catch (Exception \$e) {
    echo '❌ Erreur DB: ' . \$e->getMessage() . '\n';
}
"

# Migrations
echo "🗂️ Migrations base de données..."
php bin/console doctrine:migrations:migrate --no-interaction

# Fixtures
echo "📊 Chargement des données..."
php bin/console doctrine:fixtures:load --no-interaction

# Cache
echo "🧹 Gestion du cache..."
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Permissions
echo "🔐 Configuration des permissions..."
chmod -R 755 var/
chmod -R 755 public/

echo "✅ Installation terminée!"
echo "🌐 Visitez: http://cityflow-benin.com"