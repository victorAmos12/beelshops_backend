# BeelShops - API REST

API REST professionnelle pour la plateforme de vente de bijoux **BeelShops**.

## 🚀 Démarrage rapide

### Prérequis
- PHP 8.1+
- Symfony 7.0+
- MySQL 8.0+
- Composer

### Installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd beelshop
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
```bash
# Éditer le fichier .env
DATABASE_URL="mysql://root:password@127.0.0.1:3306/beelshops?serverVersion=8.0&charset=utf8mb4"
```

4. **Créer la base de données**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Démarrer le serveur**
```bash
symfony server:start
# ou
php -S localhost:8000 -t public
```

L'API sera accessible à `http://localhost:8000/api`

---

## 📚 Documentation

La documentation complète des endpoints est disponible dans [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### Endpoints principaux

#### Produits
- `GET /api/produits` - Récupérer tous les produits
- `GET /api/produits/{id}` - Récupérer un produit
- `POST /api/produits` - Créer un produit
- `PUT /api/produits/{id}` - Mettre à jour un produit
- `DELETE /api/produits/{id}` - Supprimer un produit

#### Catégories
- `GET /api/categories` - Récupérer toutes les catégories
- `GET /api/categories/{id}` - Récupérer une catégorie
- `POST /api/categories` - Créer une catégorie
- `PUT /api/categories/{id}` - Mettre à jour une catégorie
- `DELETE /api/categories/{id}` - Supprimer une catégorie

#### Utilisateurs
- `GET /api/utilisateurs` - Récupérer tous les utilisateurs
- `GET /api/utilisateurs/{id}` - Récupérer un utilisateur
- `POST /api/utilisateurs` - Créer un utilisateur (inscription)
- `PUT /api/utilisateurs/{id}` - Mettre à jour un utilisateur
- `DELETE /api/utilisateurs/{id}` - Supprimer un utilisateur
- `POST /api/utilisateurs/login` - Connexion utilisateur

---

## 🧪 Tests

### Avec cURL

```bash
# Récupérer tous les produits
curl -X GET http://localhost:8000/api/produits

# Créer une catégorie
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Bracelets",
    "slug": "bracelets",
    "description": "Tous nos bracelets"
  }'

# Créer un utilisateur
curl -X POST http://localhost:8000/api/utilisateurs \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "SecurePassword123!",
    "nom": "Dupont",
    "prenom": "Jean"
  }'
```

### Avec le script de test

```bash
bash test_api.sh
```

### Avec Postman

1. Importer la collection Postman (à créer)
2. Configurer l'environnement avec `BASE_URL=http://localhost:8000/api`
3. Exécuter les tests

---

## 🏗️ Structure du projet

```
beelshop/
├── src/
│   ├── Controller/
│   │   ├── ApiController.php
│   │   ├── ProduitController.php
│   │   ├── CategorieController.php
│   │   └── UtilisateurController.php
│   ├── Entity/
│   │   ├── Produit.php
│   │   ├── Categorie.php
│   │   ├── Utilisateur.php
│   │   ├── Commandes.php
│   │   ├── ArticleCommande.php
│   │   ├── Panier.php
│   │   ├── PanierArticles.php
│   │   ├── AvisClient.php
│   │   ├── ListeSouhaits.php
│   │   └── ProduitImages.php
│   └── Repository/
│       ├── ProduitRepository.php
│       ├── CategorieRepository.php
│       └── UtilisateurRepository.php
├── migrations/
├── config/
├── public/
├── tests/
├── API_DOCUMENTATION.md
├── README.md
└── composer.json
```

---

## 🔐 Sécurité

### Bonnes pratiques implémentées

- ✅ Hachage des mots de passe avec bcrypt
- ✅ Validation des données d'entrée
- ✅ Gestion des erreurs appropriée
- ✅ Codes HTTP corrects
- ✅ Réponses JSON structurées

### À implémenter

- [ ] Authentification JWT
- [ ] Autorisation basée sur les rôles (RBAC)
- [ ] Rate limiting
- [ ] CORS
- [ ] Validation avec Symfony Validator
- [ ] Tests unitaires et d'intégration

---

## 📊 Modèle de données

### Relations principales

```
Utilisateur (1) ──→ (N) Commandes
Utilisateur (1) ──→ (N) AvisClient
Utilisateur (1) ──→ (N) ListeSouhaits
Utilisateur (1) ──→ (1) Panier

Categorie (1) ──→ (N) Produit
Produit (1) ──→ (N) ProduitImages
Produit (1) ──→ (N) AvisClient
Produit (1) ──→ (N) ListeSouhaits
Produit (1) ──→ (N) PanierArticles

Commandes (1) ──→ (N) ArticleCommande
ArticleCommande (N) ──→ (1) Produit

Panier (1) ──→ (N) PanierArticles
PanierArticles (N) ──→ (1) Produit
```

---

## 🐛 Dépannage

### Erreur: "Base de données non trouvée"
```bash
php bin/console doctrine:database:create
```

### Erreur: "Tables manquantes"
```bash
php bin/console doctrine:migrations:migrate
```

### Erreur: "Classe non trouvée"
```bash
composer dump-autoload
```

---

## 📝 Conventions de code

- **Nommage**: camelCase pour les variables, PascalCase pour les classes
- **Indentation**: 4 espaces
- **Commentaires**: PHPDoc pour les méthodes publiques
- **Formatage**: PSR-12

---

## 🚀 Prochaines étapes

1. Implémenter l'authentification JWT
2. Ajouter les endpoints pour les commandes
3. Ajouter les endpoints pour le panier
4. Ajouter les endpoints pour les avis clients
5. Ajouter les tests unitaires
6. Déployer en production

---

## 📄 Licence

Ce projet est sous licence MIT.

---

## 👥 Auteur

BeelShops - Plateforme de vente de bijoux

---

## 📞 Support

Pour toute question ou problème, veuillez créer une issue sur le repository.
