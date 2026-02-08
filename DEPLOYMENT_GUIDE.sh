#!/bin/bash

# ============================================
# BeelShops API - Commandes de Déploiement
# ============================================

echo "🚀 BeelShops API - Guide de Déploiement"
echo "========================================"
echo ""

# ============================================
# 1. Installation et Configuration
# ============================================

echo "📦 Phase 1: Installation et Configuration"
echo "=========================================="
echo ""

echo "1.1 Installer les dépendances Composer"
echo "$ composer install"
echo ""

echo "1.2 Configurer la base de données dans .env"
echo "$ nano .env"
echo "DATABASE_URL=\"mysql://root:password@127.0.0.1:3306/beelshops?serverVersion=8.0&charset=utf8mb4\""
echo ""

echo "1.3 Créer la base de données"
echo "$ php bin/console doctrine:database:create"
echo ""

echo "1.4 Exécuter les migrations"
echo "$ php bin/console doctrine:migrations:migrate"
echo ""

# ============================================
# 2. Vérification de la Structure
# ============================================

echo "✅ Phase 2: Vérification de la Structure"
echo "========================================"
echo ""

echo "2.1 Vérifier les entités"
echo "$ php bin/console doctrine:mapping:info"
echo ""

echo "2.2 Vérifier les routes"
echo "$ php bin/console debug:router"
echo ""

# ============================================
# 3. Démarrage du Serveur
# ============================================

echo "🌐 Phase 3: Démarrage du Serveur"
echo "================================"
echo ""

echo "3.1 Démarrer le serveur Symfony"
echo "$ symfony server:start"
echo ""

echo "3.2 Ou utiliser PHP directement"
echo "$ php -S localhost:8000 -t public"
echo ""

echo "3.3 L'API sera accessible à:"
echo "http://localhost:8000/api"
echo ""

# ============================================
# 4. Tests
# ============================================

echo "🧪 Phase 4: Tests"
echo "================="
echo ""

echo "4.1 Tester l'endpoint de bienvenue"
echo "$ curl http://localhost:8000/api"
echo ""

echo "4.2 Exécuter le script de test complet"
echo "$ bash test_api.sh"
echo ""

echo "4.3 Tester un endpoint spécifique"
echo "$ curl -X GET http://localhost:8000/api/produits"
echo ""

# ============================================
# 5. Commandes Utiles
# ============================================

echo "🛠️  Phase 5: Commandes Utiles"
echo "============================="
echo ""

echo "5.1 Créer une nouvelle entité"
echo "$ php bin/console make:entity NomEntite"
echo ""

echo "5.2 Créer un contrôleur"
echo "$ php bin/console make:controller NomController"
echo ""

echo "5.3 Créer une migration"
echo "$ php bin/console make:migration"
echo ""

echo "5.4 Exécuter les migrations"
echo "$ php bin/console doctrine:migrations:migrate"
echo ""

echo "5.5 Annuler la dernière migration"
echo "$ php bin/console doctrine:migrations:migrate prev"
echo ""

echo "5.6 Vider le cache"
echo "$ php bin/console cache:clear"
echo ""

echo "5.7 Charger des fixtures (données de test)"
echo "$ php bin/console doctrine:fixtures:load"
echo ""

# ============================================
# 6. Configuration Angular
# ============================================

echo "🅰️  Phase 6: Configuration Angular"
echo "=================================="
echo ""

echo "6.1 Créer un service Angular"
echo "$ ng generate service services/produit"
echo ""

echo "6.2 Créer un composant Angular"
echo "$ ng generate component components/produit-list"
echo ""

echo "6.3 Démarrer le serveur Angular"
echo "$ ng serve"
echo ""

echo "6.4 L'application Angular sera accessible à:"
echo "http://localhost:4200"
echo ""

# ============================================
# 7. Déploiement en Production
# ============================================

echo "🚀 Phase 7: Déploiement en Production"
echo "====================================="
echo ""

echo "7.1 Installer les dépendances (sans dev)"
echo "$ composer install --no-dev --optimize-autoloader"
echo ""

echo "7.2 Configurer l'environnement de production"
echo "$ cp .env .env.local"
echo "$ nano .env.local"
echo "APP_ENV=prod"
echo "APP_DEBUG=0"
echo ""

echo "7.3 Vider le cache"
echo "$ php bin/console cache:clear --env=prod"
echo ""

echo "7.4 Réchauffer le cache"
echo "$ php bin/console cache:warmup --env=prod"
echo ""

echo "7.5 Exécuter les migrations"
echo "$ php bin/console doctrine:migrations:migrate --env=prod"
echo ""

# ============================================
# 8. Dépannage
# ============================================

echo "🔧 Phase 8: Dépannage"
echo "===================="
echo ""

echo "8.1 Erreur: Base de données non trouvée"
echo "$ php bin/console doctrine:database:create"
echo ""

echo "8.2 Erreur: Tables manquantes"
echo "$ php bin/console doctrine:migrations:migrate"
echo ""

echo "8.3 Erreur: Classe non trouvée"
echo "$ composer dump-autoload"
echo ""

echo "8.4 Erreur: Permission refusée"
echo "$ chmod -R 777 var/"
echo ""

echo "8.5 Erreur: CORS"
echo "Vérifier la configuration dans config/packages/nelmio_cors.yaml"
echo ""

# ============================================
# 9. Fichiers Importants
# ============================================

echo "📁 Phase 9: Fichiers Importants"
echo "==============================="
echo ""

echo "9.1 Configuration"
echo "- .env.dev - Variables d'environnement (développement)"
echo "- .env.local - Variables d'environnement (local)"
echo "- config/packages/nelmio_cors.yaml - Configuration CORS"
echo "- config/packages/doctrine.yaml - Configuration Doctrine"
echo ""

echo "9.2 Code Source"
echo "- src/Controller/ - Contrôleurs API"
echo "- src/Entity/ - Entités Doctrine"
echo "- src/Repository/ - Repositories"
echo "- src/Service/ - Services"
echo "- src/Constants/ - Constantes"
echo ""

echo "9.3 Documentation"
echo "- API_ENDPOINTS_COMPLETE.md - Documentation complète"
echo "- README_API.md - Guide de démarrage"
echo "- ANGULAR_INTEGRATION.md - Guide d'intégration Angular"
echo "- CHECKLIST_COMPLETE.md - Checklist complète"
echo ""

# ============================================
# 10. Ressources Utiles
# ============================================

echo "📚 Phase 10: Ressources Utiles"
echo "=============================="
echo ""

echo "10.1 Documentation Symfony"
echo "https://symfony.com/doc/current/"
echo ""

echo "10.2 Documentation Doctrine"
echo "https://www.doctrine-project.org/projects/doctrine-orm/en/latest/"
echo ""

echo "10.3 Documentation Angular"
echo "https://angular.io/docs"
echo ""

echo "10.4 Nelmio CORS"
echo "https://github.com/nelmio/NelmioCorsBundle"
echo ""

# ============================================
# Fin
# ============================================

echo ""
echo "✨ Configuration terminée!"
echo "🚀 L'API BeelShops est prête pour le développement!"
echo ""
