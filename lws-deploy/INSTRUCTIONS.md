🚀 DÉPLOIEMENT CITY REPORT SUR LWS
==================================

📋 ÉTAPES À SUIVRE:

1. 📤 UPLOAD FTP
   - Connectez-vous à votre FTP LWS
   - Serveur: ftp.cityflow-benin.com (ou selon LWS)
   - Utilisateur: cp2608560p22
   - Mot de passe: Akoutika1998
   - Uploadez tout le projet dans /www/

2. 🌐 ACCÈS PANEL WEB
   - Allez sur votre cPanel LWS
   - Cherchez "Terminal" ou "Gestionnaire de fichiers"
   - Naviguez vers /www/

3. 🛠️ INSTALLATION
   Si vous avez accès au terminal web:
   
   cd /www
   chmod +x scripts/install-cityflow.sh
   ./scripts/install-cityflow.sh

4. 🗄️ ALTERNATIVE: Installation manuelle
   Si pas d'accès terminal, exécutez ces commandes:
   
   composer install --no-dev --optimize-autoloader
   php bin/console doctrine:migrations:migrate --no-interaction
   php bin/console doctrine:fixtures:load --no-interaction
   php bin/console cache:clear --env=prod

5. ✅ VÉRIFICATION
   - Visitez: http://cityflow-benin.com
   - Testez la connexion admin
   - Vérifiez les fonctionnalités

📞 SUPPORT:
Si problème, contactez le support LWS avec ces infos:
- Utilisateur: cp2608560p22
- Base: cp2608560p22_cityflow
- Erreur rencontrée
