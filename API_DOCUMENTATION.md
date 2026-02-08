# Documentation des Endpoints API BeelShops

## Base URL
```
http://localhost:8000/api
```

---

## 📦 PRODUITS

### 1. Récupérer tous les produits
```
GET /api/produits
```

**Paramètres de requête:**
- `page` (int, optionnel): Numéro de page (défaut: 1)
- `limit` (int, optionnel): Nombre de résultats par page (défaut: 10)
- `category` (int, optionnel): Filtrer par catégorie
- `search` (string, optionnel): Rechercher par nom ou description

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Bracelet Or 18K",
      "slug": "bracelet-or-18k",
      "description": "Bracelet en or 18 carats",
      "prix": "299.99",
      "stock": 50,
      "weight": "5g",
      "image": "bracelet-or.jpg",
      "isActive": true,
      "createdAt": "2024-01-15T10:30:00+00:00",
      "updatedAt": "2024-01-15T10:30:00+00:00"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 25,
    "pages": 3
  }
}
```

---

### 2. Récupérer un produit par ID
```
GET /api/produits/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nom": "Bracelet Or 18K",
    "slug": "bracelet-or-18k",
    "description": "Bracelet en or 18 carats",
    "prix": "299.99",
    "stock": 50,
    "weight": "5g",
    "image": "bracelet-or.jpg",
    "isActive": true,
    "createdAt": "2024-01-15T10:30:00+00:00",
    "updatedAt": "2024-01-15T10:30:00+00:00"
  }
}
```

---

### 3. Créer un produit
```
POST /api/produits
Content-Type: application/json
```

**Body:**
```json
{
  "nom": "Chaîne Argent",
  "slug": "chaine-argent",
  "description": "Chaîne en argent 925",
  "prix": "149.99",
  "stock": 100,
  "weight": "3g",
  "image": "chaine-argent.jpg",
  "isActive": true,
  "category_id": 2
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Produit créé avec succès",
  "data": {
    "id": 2,
    "nom": "Chaîne Argent",
    ...
  }
}
```

---

### 4. Mettre à jour un produit
```
PUT /api/produits/{id}
Content-Type: application/json
```

**Body (champs optionnels):**
```json
{
  "nom": "Chaîne Argent 925",
  "prix": "159.99",
  "stock": 95
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Produit mis à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer un produit
```
DELETE /api/produits/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Produit supprimé avec succès"
}
```

---

### 6. Récupérer les produits par catégorie
```
GET /api/produits/categorie/{categoryId}
```

**Paramètres de requête:**
- `page` (int, optionnel): Numéro de page (défaut: 1)
- `limit` (int, optionnel): Nombre de résultats par page (défaut: 10)

**Réponse (200):**
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": { ... }
}
```

---

## 🏷️ CATÉGORIES

### 1. Récupérer toutes les catégories
```
GET /api/categories
```

**Paramètres de requête:**
- `page` (int, optionnel): Numéro de page (défaut: 1)
- `limit` (int, optionnel): Nombre de résultats par page (défaut: 10)

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nom": "Bracelets",
      "slug": "bracelets",
      "description": "Tous nos bracelets",
      "image": "bracelets.jpg",
      "createdAt": "2024-01-15T10:30:00+00:00"
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Récupérer une catégorie par ID
```
GET /api/categories/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nom": "Bracelets",
    "slug": "bracelets",
    "description": "Tous nos bracelets",
    "image": "bracelets.jpg",
    "createdAt": "2024-01-15T10:30:00+00:00"
  }
}
```

---

### 3. Créer une catégorie
```
POST /api/categories
Content-Type: application/json
```

**Body:**
```json
{
  "nom": "Colliers",
  "slug": "colliers",
  "description": "Tous nos colliers",
  "image": "colliers.jpg"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Catégorie créée avec succès",
  "data": { ... }
}
```

---

### 4. Mettre à jour une catégorie
```
PUT /api/categories/{id}
Content-Type: application/json
```

**Body (champs optionnels):**
```json
{
  "nom": "Colliers Premium",
  "description": "Nos colliers haut de gamme"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Catégorie mise à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer une catégorie
```
DELETE /api/categories/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Catégorie supprimée avec succès"
}
```

**Erreur (409):** Si la catégorie contient des produits
```json
{
  "error": "Impossible de supprimer une catégorie qui contient des produits"
}
```

---

### 6. Récupérer les produits d'une catégorie
```
GET /api/categories/{id}/produits
```

**Paramètres de requête:**
- `page` (int, optionnel): Numéro de page (défaut: 1)
- `limit` (int, optionnel): Nombre de résultats par page (défaut: 10)

**Réponse (200):**
```json
{
  "success": true,
  "data": [ ... ],
  "pagination": { ... }
}
```

---

## 👤 UTILISATEURS

### 1. Récupérer tous les utilisateurs
```
GET /api/utilisateurs
```

**Paramètres de requête:**
- `page` (int, optionnel): Numéro de page (défaut: 1)
- `limit` (int, optionnel): Nombre de résultats par page (défaut: 10)

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "email": "user@example.com",
      "nom": "Dupont",
      "prenom": "Jean",
      "phone": "0612345678",
      "isActive": true,
      "createdAt": "2024-01-15T10:30:00+00:00",
      "updatedAt": "2024-01-15T10:30:00+00:00"
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Récupérer un utilisateur par ID
```
GET /api/utilisateurs/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "email": "user@example.com",
    "nom": "Dupont",
    "prenom": "Jean",
    "phone": "0612345678",
    "isActive": true,
    "createdAt": "2024-01-15T10:30:00+00:00",
    "updatedAt": "2024-01-15T10:30:00+00:00"
  }
}
```

---

### 3. Créer un utilisateur (Inscription)
```
POST /api/utilisateurs
Content-Type: application/json
```

**Body:**
```json
{
  "email": "newuser@example.com",
  "password": "SecurePassword123!",
  "nom": "Martin",
  "prenom": "Sophie",
  "phone": "0687654321"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Utilisateur créé avec succès",
  "data": {
    "id": 2,
    "email": "newuser@example.com",
    "nom": "Martin",
    "prenom": "Sophie"
  }
}
```

**Erreur (409):** Email déjà utilisé
```json
{
  "error": "Cet email est déjà utilisé"
}
```

---

### 4. Mettre à jour un utilisateur
```
PUT /api/utilisateurs/{id}
Content-Type: application/json
```

**Body (champs optionnels):**
```json
{
  "nom": "Martin-Dupont",
  "phone": "0698765432",
  "password": "NewPassword456!"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Utilisateur mis à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer un utilisateur
```
DELETE /api/utilisateurs/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Utilisateur supprimé avec succès"
}
```

---

### 6. Connexion utilisateur
```
POST /api/utilisateurs/login
Content-Type: application/json
```

**Body:**
```json
{
  "email": "user@example.com",
  "password": "SecurePassword123!"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "id": 1,
    "email": "user@example.com",
    "nom": "Dupont",
    "prenom": "Jean"
  }
}
```

**Erreur (401):** Email ou mot de passe incorrect
```json
{
  "error": "Email ou mot de passe incorrect"
}
```

---

### 7. Récupérer les commandes d'un utilisateur
```
GET /api/utilisateurs/{id}/commandes
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "numOrdre": "CMD-2024-001",
      "prixTotal": "599.99",
      "status": "delivered",
      "adresseLivraison": "123 Rue de Paris",
      "createdAt": "2024-01-15T10:30:00+00:00"
    }
  ]
}
```

---

## 🔍 Codes de Réponse HTTP

| Code | Signification |
|------|---------------|
| 200 | OK - Requête réussie |
| 201 | Created - Ressource créée |
| 400 | Bad Request - Données invalides |
| 401 | Unauthorized - Non authentifié |
| 403 | Forbidden - Accès refusé |
| 404 | Not Found - Ressource non trouvée |
| 409 | Conflict - Conflit (ex: email déjà utilisé) |
| 500 | Internal Server Error - Erreur serveur |

---

## 📝 Notes

- Tous les endpoints retournent du JSON
- Les timestamps sont au format ISO 8601
- Les prix sont en format DECIMAL (ex: "299.99")
- Les mots de passe sont hashés avec bcrypt
- Les slugs sont générés automatiquement s'ils ne sont pas fournis
