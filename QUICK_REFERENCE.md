# 🚀 Commandes Essentielles - BeelShops API

## Installation et Configuration

```bash
# 1. Installer les dépendances
composer install

# 2. Configurer la base de données dans .env
# DATABASE_URL="mysql://root:password@127.0.0.1:3306/beelshops?serverVersion=8.0&charset=utf8mb4"

# 3. Créer la base de données
php bin/console doctrine:database:create

# 4. Exécuter les migrations
php bin/console doctrine:migrations:migrate

# 5. Vider le cache
php bin/console cache:clear
```

## Démarrage du Serveur

```bash
# Option 1: Avec Symfony CLI
symfony server:start

# Option 2: Avec PHP
php -S localhost:8000 -t public

# L'API sera accessible à: http://localhost:8000/api
```

## Tests

```bash
# Test de l'endpoint de bienvenue
curl http://localhost:8000/api

# Test avec le script complet
bash test_api.sh

# Test d'un endpoint spécifique
curl -X GET http://localhost:8000/api/produits
curl -X GET http://localhost:8000/api/categories
curl -X GET http://localhost:8000/api/utilisateurs
```

## Commandes Utiles

```bash
# Afficher toutes les routes
php bin/console debug:router

# Afficher les informations des entités
php bin/console doctrine:mapping:info

# Créer une nouvelle entité
php bin/console make:entity NomEntite

# Créer un contrôleur
php bin/console make:controller NomController

# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Annuler la dernière migration
php bin/console doctrine:migrations:migrate prev

# Vider le cache
php bin/console cache:clear

# Réchauffer le cache
php bin/console cache:warmup
```

## Déploiement en Production

```bash
# 1. Installer les dépendances (sans dev)
composer install --no-dev --optimize-autoloader

# 2. Configurer l'environnement
# Éditer .env.local:
# APP_ENV=prod
# APP_DEBUG=0

# 3. Vider et réchauffer le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 4. Exécuter les migrations
php bin/console doctrine:migrations:migrate --env=prod
```

## Dépannage

```bash
# Erreur: Base de données non trouvée
php bin/console doctrine:database:create

# Erreur: Tables manquantes
php bin/console doctrine:migrations:migrate

# Erreur: Classe non trouvée
composer dump-autoload

# Erreur: Permission refusée
chmod -R 777 var/

# Afficher les logs
tail -f var/log/dev.log

# Vérifier la configuration CORS
cat config/packages/nelmio_cors.yaml
```

## Exemples cURL

```bash
# Récupérer tous les produits
curl -X GET http://localhost:8000/api/produits

# Créer un produit
curl -X POST http://localhost:8000/api/produits \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Bracelet Or 18K",
    "description": "Bracelet en or 18 carats",
    "prix": "299.99",
    "stock": 50,
    "category_id": 1
  }'

# Récupérer un produit
curl -X GET http://localhost:8000/api/produits/1

# Mettre à jour un produit
curl -X PUT http://localhost:8000/api/produits/1 \
  -H "Content-Type: application/json" \
  -d '{
    "prix": "349.99",
    "stock": 45
  }'

# Supprimer un produit
curl -X DELETE http://localhost:8000/api/produits/1

# Créer un utilisateur
curl -X POST http://localhost:8000/api/utilisateurs \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "SecurePassword123!",
    "nom": "Dupont",
    "prenom": "Jean"
  }'

# Connexion utilisateur
curl -X POST http://localhost:8000/api/utilisateurs/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "SecurePassword123!"
  }'

# Ajouter un article au panier
curl -X POST http://localhost:8000/api/panier/1/articles \
  -H "Content-Type: application/json" \
  -d '{
    "produit_id": 1,
    "quantite": 2
  }'

# Créer une commande
curl -X POST http://localhost:8000/api/commandes \
  -H "Content-Type: application/json" \
  -d '{
    "utilisateur_id": 1,
    "adresse_livraison": "123 Rue de Paris",
    "articles": [
      {
        "produit_id": 1,
        "quantite": 2
      }
    ]
  }'

# Créer un avis
curl -X POST http://localhost:8000/api/avis \
  -H "Content-Type: application/json" \
  -d '{
    "produit_id": 1,
    "utilisateur_id": 1,
    "rating": 5,
    "commentaire": "Excellent produit!"
  }'

# Ajouter à la liste de souhaits
curl -X POST http://localhost:8000/api/liste-souhaits \
  -H "Content-Type: application/json" \
  -d '{
    "utilisateur_id": 1,
    "produit_id": 1
  }'
```

## Configuration Angular

```bash
# Créer un service
ng generate service services/produit

# Créer un composant
ng generate component components/produit-list

# Démarrer le serveur Angular
ng serve

# Compiler pour la production
ng build --prod
```

## Fichiers Importants

```
.env                              - Variables d'environnement
.env.dev                          - Variables de développement
.env.local                        - Variables locales (à créer)
config/packages/nelmio_cors.yaml  - Configuration CORS
config/packages/doctrine.yaml     - Configuration Doctrine
src/Controller/                   - Contrôleurs API
src/Entity/                       - Entités Doctrine
src/Repository/                   - Repositories
src/Service/                      - Services
src/Constants/                    - Constantes
migrations/                       - Migrations Doctrine
```

## Documentation

```
API_ENDPOINTS_COMPLETE.md    - Documentation complète des endpoints
README_API.md                - Guide de démarrage
ANGULAR_INTEGRATION.md       - Guide d'intégration Angular
IMPLEMENTATION_SUMMARY.md    - Résumé des corrections BDD
CHECKLIST_COMPLETE.md        - Checklist complète
PROJECT_SUMMARY.md           - Résumé du projet
DEPLOYMENT_GUIDE.sh          - Guide de déploiement
```

## Ressources Utiles

- Symfony: https://symfony.com/doc/current/
- Doctrine: https://www.doctrine-project.org/
- Angular: https://angular.io/docs
- Nelmio CORS: https://github.com/nelmio/NelmioCorsBundle

## Checklist de Déploiement

- [ ] Installer les dépendances
- [ ] Configurer la base de données
- [ ] Exécuter les migrations
- [ ] Vérifier les routes
- [ ] Tester les endpoints
- [ ] Configurer CORS
- [ ] Vérifier les logs
- [ ] Déployer en production
- [ ] Configurer le monitoring
- [ ] Configurer les backups

## Support

Pour toute question, consultez:
1. La documentation dans `API_ENDPOINTS_COMPLETE.md`
2. Les exemples dans `ANGULAR_INTEGRATION.md`
3. Le script de test: `bash test_api.sh`
4. Les logs: `tail -f var/log/dev.log`
