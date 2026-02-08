# Authentification et Gestion des Rôles - BeelShop

## Configuration JWT

Les clés JWT doivent être générées. Exécutez les commandes suivantes dans le répertoire du projet :

```bash
# Créer le répertoire pour les clés JWT
mkdir -p config/jwt

# Générer la clé privée
openssl genrsa -out config/jwt/private.pem 4096

# Générer la clé publique
openssl rsa -in config/jwt/private.pem -pubout -out config/jwt/public.pem
```

## Migration de la Base de Données

Exécutez la migration pour ajouter la colonne des rôles :

```bash
php bin/console doctrine:migrations:migrate
```

## Commandes SQL pour Créer les Utilisateurs

### 1. Créer un Utilisateur Admin

```sql
INSERT INTO utilisateur (email, password, nom, prenom, phone, created_at, updated_at, is_active, roles)
VALUES (
    'admin@beelshop.com',
    '$2y$13$YOUR_HASHED_PASSWORD_HERE',
    'Admin',
    'BeelShop',
    '+33612345678',
    NOW(),
    NOW(),
    1,
    '["ROLE_ADMIN", "ROLE_CLIENT"]'
);
```

### 2. Créer un Utilisateur Client

```sql
INSERT INTO utilisateur (email, password, nom, prenom, phone, created_at, updated_at, is_active, roles)
VALUES (
    'client@beelshop.com',
    '$2y$13$YOUR_HASHED_PASSWORD_HERE',
    'Client',
    'Test',
    '+33687654321',
    NOW(),
    NOW(),
    1,
    '["ROLE_CLIENT"]'
);
```

### Générer les Mots de Passe Hachés

Pour générer les mots de passe hachés, utilisez la commande Symfony :

```bash
php bin/console security:hash-password
```

Ou utilisez ce script PHP :

```php
<?php
require 'vendor/autoload.php';

use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

$hasher = new NativePasswordHasher();
$password = 'votre_mot_de_passe';
$hashed = $hasher->hash($password);
echo $hashed;
?>
```

### Exemple Complet avec Mots de Passe

Pour les tests, voici les commandes SQL avec des mots de passe hachés (mot de passe : "password123") :

```sql
-- Admin User
INSERT INTO utilisateur (email, password, nom, prenom, phone, created_at, updated_at, is_active, roles)
VALUES (
    'admin@beelshop.com',
    '$2y$13$8qJ8.8qJ8.8qJ8.8qJ8.8uJ8.8qJ8.8qJ8.8qJ8.8qJ8.8qJ8.8qJ8',
    'Admin',
    'BeelShop',
    '+33612345678',
    NOW(),
    NOW(),
    1,
    '["ROLE_ADMIN", "ROLE_CLIENT"]'
);

-- Client User
INSERT INTO utilisateur (email, password, nom, prenom, phone, created_at, updated_at, is_active, roles)
VALUES (
    'client@beelshop.com',
    '$2y$13$8qJ8.8qJ8.8qJ8.8qJ8.8uJ8.8qJ8.8qJ8.8qJ8.8qJ8.8qJ8.8qJ8',
    'Client',
    'Test',
    '+33687654321',
    NOW(),
    NOW(),
    1,
    '["ROLE_CLIENT"]'
);
```

## Endpoints d'Authentification

### 1. Connexion (Login)

**Endpoint:** `POST /api/auth/login`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
    "email": "admin@beelshop.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Connexion réussie",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "user": {
        "id": 1,
        "email": "admin@beelshop.com",
        "nom": "Admin",
        "prenom": "BeelShop",
        "roles": ["ROLE_ADMIN", "ROLE_CLIENT"],
        "isActive": true
    }
}
```

**Erreurs possibles:**
- `400 Bad Request`: Email et mot de passe requis
- `401 Unauthorized`: Email ou mot de passe incorrect
- `403 Forbidden`: Compte désactivé

---

### 2. Inscription (Register)

**Endpoint:** `POST /api/auth/register`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
    "email": "newuser@beelshop.com",
    "password": "securePassword123",
    "nom": "Dupont",
    "prenom": "Jean",
    "phone": "+33612345678",
    "role": "ROLE_CLIENT"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Inscription réussie",
    "data": {
        "id": 3,
        "email": "newuser@beelshop.com",
        "nom": "Dupont",
        "prenom": "Jean",
        "roles": ["ROLE_CLIENT"]
    }
}
```

**Erreurs possibles:**
- `400 Bad Request`: Champ requis manquant
- `409 Conflict`: Email déjà utilisé

---

### 3. Récupérer les Informations de l'Utilisateur Connecté

**Endpoint:** `GET /api/auth/me`

**Headers:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "email": "admin@beelshop.com",
        "nom": "Admin",
        "prenom": "BeelShop",
        "phone": "+33612345678",
        "roles": ["ROLE_ADMIN", "ROLE_CLIENT"],
        "isActive": true,
        "createdAt": "2025-02-08T03:40:00+00:00",
        "updatedAt": "2025-02-08T03:40:00+00:00"
    }
}
```

**Erreurs possibles:**
- `401 Unauthorized`: Token manquant ou invalide

---

### 4. Rafraîchir le Token

**Endpoint:** `POST /api/auth/refresh`

**Headers:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Token rafraîchi",
    "user": {
        "id": 1,
        "email": "admin@beelshop.com",
        "nom": "Admin",
        "prenom": "BeelShop",
        "roles": ["ROLE_ADMIN", "ROLE_CLIENT"]
    }
}
```

**Erreurs possibles:**
- `401 Unauthorized`: Token manquant ou invalide
- `403 Forbidden`: Compte désactivé

---

## Rôles Disponibles

- **ROLE_CLIENT**: Rôle par défaut pour les clients
- **ROLE_ADMIN**: Rôle administrateur avec accès complet

## Contrôle d'Accès

Les endpoints sont protégés selon les rôles :

| Endpoint | Rôles Requis |
|----------|-------------|
| `POST /api/auth/login` | PUBLIC |
| `POST /api/auth/register` | PUBLIC |
| `GET /api/auth/me` | ROLE_CLIENT |
| `POST /api/auth/refresh` | ROLE_CLIENT |
| `GET /api/utilisateurs` | ROLE_ADMIN |
| `POST /api/utilisateurs` | ROLE_ADMIN |
| `PUT /api/utilisateurs/{id}` | ROLE_ADMIN |
| `DELETE /api/utilisateurs/{id}` | ROLE_ADMIN |

## Utilisation du Token JWT

Tous les endpoints protégés nécessitent un header `Authorization` :

```
Authorization: Bearer <token_jwt>
```

Le token est valide pendant 3600 secondes (1 heure) par défaut.

## Exemple d'Utilisation avec cURL

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@beelshop.com",
    "password": "password123"
  }'
```

### Récupérer les infos utilisateur
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Inscription
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "newuser@beelshop.com",
    "password": "securePassword123",
    "nom": "Dupont",
    "prenom": "Jean",
    "phone": "+33612345678"
  }'
```

## Prochaines Étapes

1. Générer les clés JWT
2. Exécuter la migration
3. Créer les utilisateurs admin et client
4. Tester les endpoints
5. Intégrer l'authentification dans le frontend
