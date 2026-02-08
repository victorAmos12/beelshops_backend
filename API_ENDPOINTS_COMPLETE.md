# 📚 Documentation Complète des Endpoints API BeelShops

## Configuration CORS

La configuration CORS est activée pour Angular. Les origines autorisées sont configurées dans `.env.dev`:

```env
CORS_ALLOW_ORIGIN=^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$
```

Cela permet les requêtes depuis:
- `http://localhost:*`
- `http://127.0.0.1:*`
- `https://localhost:*`
- `https://127.0.0.1:*`

---

## 🛒 PANIER

### 1. Récupérer le panier d'un utilisateur
```
GET /api/panier/{userId}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "articles": [
      {
        "id": 1,
        "produit": {
          "id": 1,
          "nom": "Bracelet Or 18K",
          "prix": "299.99",
          "image": "bracelet-or.jpg"
        },
        "quantite": 2,
        "prixUnitaire": 299.99,
        "sousTotal": 599.98,
        "addedAt": "2024-01-15T10:30:00+00:00"
      }
    ],
    "total": 599.98,
    "nombreArticles": 1
  }
}
```

---

### 2. Ajouter un article au panier
```
POST /api/panier/{userId}/articles
Content-Type: application/json
```

**Body:**
```json
{
  "produit_id": 1,
  "quantite": 2
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Article ajouté au panier",
  "data": {
    "id": 1,
    "produit_id": 1,
    "quantite": 2
  }
}
```

---

### 3. Mettre à jour la quantité d'un article
```
PUT /api/panier/articles/{articleId}
Content-Type: application/json
```

**Body:**
```json
{
  "quantite": 3
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Article mis à jour",
  "data": {
    "id": 1,
    "quantite": 3
  }
}
```

---

### 4. Supprimer un article du panier
```
DELETE /api/panier/articles/{articleId}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Article supprimé du panier"
}
```

---

### 5. Vider le panier
```
DELETE /api/panier/{userId}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Panier vidé"
}
```

---

## 📦 COMMANDES

### 1. Récupérer toutes les commandes
```
GET /api/commandes?page=1&limit=10&status=pending
```

**Paramètres:**
- `page` (int): Numéro de page
- `limit` (int): Nombre de résultats
- `status` (string): Filtrer par statut (pending, confirmed, shipped, delivered, cancelled)

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "numOrdre": "CMD-20240115-ABC123",
      "utilisateur": {
        "id": 1,
        "nom": "Dupont",
        "prenom": "Jean",
        "email": "jean@example.com"
      },
      "articles": [
        {
          "id": 1,
          "produit": {
            "id": 1,
            "nom": "Bracelet Or 18K",
            "image": "bracelet-or.jpg"
          },
          "quantite": 2,
          "prixUnitaire": "299.99",
          "prixTotal": "599.98"
        }
      ],
      "prixTotal": "599.98",
      "status": "pending",
      "adresseLivraison": "123 Rue de Paris",
      "adresseAppartement": "Apt 5",
      "createdAt": "2024-01-15T10:30:00+00:00",
      "updatedAt": "2024-01-15T10:30:00+00:00"
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Récupérer une commande par ID
```
GET /api/commandes/{id}
```

**Réponse (200):** Même format que ci-dessus

---

### 3. Créer une commande
```
POST /api/commandes
Content-Type: application/json
```

**Body:**
```json
{
  "utilisateur_id": 1,
  "status": "pending",
  "adresse_livraison": "123 Rue de Paris",
  "adresse_appartement": "Apt 5",
  "articles": [
    {
      "produit_id": 1,
      "quantite": 2
    },
    {
      "produit_id": 2,
      "quantite": 1
    }
  ]
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "data": { ... }
}
```

---

### 4. Mettre à jour une commande
```
PUT /api/commandes/{id}
Content-Type: application/json
```

**Body (champs optionnels):**
```json
{
  "status": "shipped",
  "adresse_livraison": "456 Avenue de Lyon"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Commande mise à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer une commande
```
DELETE /api/commandes/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Commande supprimée avec succès"
}
```

---

### 6. Récupérer les commandes d'un utilisateur
```
GET /api/commandes/utilisateur/{userId}?page=1&limit=10
```

**Réponse (200):** Même format que la liste des commandes

---

## 📋 ARTICLES DE COMMANDE

### 1. Récupérer tous les articles de commande
```
GET /api/articles-commandes?page=1&limit=10
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "commande": {
        "id": 1,
        "numOrdre": "CMD-20240115-ABC123"
      },
      "produit": {
        "id": 1,
        "nom": "Bracelet Or 18K",
        "image": "bracelet-or.jpg"
      },
      "quantite": 2,
      "prixUnitaire": "299.99",
      "prixTotal": "599.98"
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Récupérer un article de commande
```
GET /api/articles-commandes/{id}
```

**Réponse (200):** Même format que ci-dessus

---

### 3. Créer un article de commande
```
POST /api/articles-commandes
Content-Type: application/json
```

**Body:**
```json
{
  "commande_id": 1,
  "produit_id": 1,
  "quantite": 2
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Article de commande créé avec succès",
  "data": { ... }
}
```

---

### 4. Mettre à jour un article de commande
```
PUT /api/articles-commandes/{id}
Content-Type: application/json
```

**Body:**
```json
{
  "quantite": 3
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Article de commande mis à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer un article de commande
```
DELETE /api/articles-commandes/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Article de commande supprimé avec succès"
}
```

---

### 6. Récupérer les articles d'une commande
```
GET /api/articles-commandes/commande/{commandeId}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [ ... ]
}
```

---

## ⭐ AVIS CLIENTS

### 1. Récupérer tous les avis
```
GET /api/avis?page=1&limit=10
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "produit": {
        "id": 1,
        "nom": "Bracelet Or 18K"
      },
      "utilisateur": {
        "id": 1,
        "nom": "Dupont",
        "prenom": "Jean"
      },
      "rating": 5,
      "commentaire": "Excellent produit!",
      "createdAt": "2024-01-15T10:30:00+00:00",
      "updatedAt": "2024-01-15T10:30:00+00:00"
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Récupérer un avis
```
GET /api/avis/{id}
```

**Réponse (200):** Même format que ci-dessus

---

### 3. Créer un avis
```
POST /api/avis
Content-Type: application/json
```

**Body:**
```json
{
  "produit_id": 1,
  "utilisateur_id": 1,
  "rating": 5,
  "commentaire": "Excellent produit!"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Avis créé avec succès",
  "data": { ... }
}
```

---

### 4. Mettre à jour un avis
```
PUT /api/avis/{id}
Content-Type: application/json
```

**Body:**
```json
{
  "rating": 4,
  "commentaire": "Très bon produit"
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Avis mis à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer un avis
```
DELETE /api/avis/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Avis supprimé avec succès"
}
```

---

### 6. Récupérer les avis d'un produit
```
GET /api/avis/produit/{produitId}?page=1&limit=10
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [ ... ],
  "stats": {
    "total": 10,
    "moyenneRating": 4.5
  },
  "pagination": { ... }
}
```

---

### 7. Récupérer les avis d'un utilisateur
```
GET /api/avis/utilisateur/{utilisateurId}?page=1&limit=10
```

**Réponse (200):** Même format que la liste des avis

---

## ❤️ LISTE DE SOUHAITS

### 1. Récupérer la liste de souhaits d'un utilisateur
```
GET /api/liste-souhaits/{userId}?page=1&limit=10
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "produit": {
        "id": 1,
        "nom": "Bracelet Or 18K",
        "prix": "299.99",
        "image": "bracelet-or.jpg",
        "slug": "bracelet-or-18k"
      },
      "addedAt": "2024-01-15T10:30:00+00:00"
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Ajouter un produit à la liste de souhaits
```
POST /api/liste-souhaits
Content-Type: application/json
```

**Body:**
```json
{
  "utilisateur_id": 1,
  "produit_id": 1
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Produit ajouté à la liste de souhaits",
  "data": { ... }
}
```

---

### 3. Supprimer un produit de la liste de souhaits
```
DELETE /api/liste-souhaits/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Produit supprimé de la liste de souhaits"
}
```

---

### 4. Supprimer un produit spécifique
```
DELETE /api/liste-souhaits/utilisateur/{userId}/produit/{produitId}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Produit supprimé de la liste de souhaits"
}
```

---

### 5. Vérifier si un produit est dans la liste
```
GET /api/liste-souhaits/check/{userId}/{produitId}
```

**Réponse (200):**
```json
{
  "success": true,
  "inWishlist": true,
  "wishlistId": 1
}
```

---

## 🖼️ IMAGES DE PRODUITS

### 1. Récupérer toutes les images
```
GET /api/produits-images?page=1&limit=10
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "produit": {
        "id": 1,
        "nom": "Bracelet Or 18K"
      },
      "image": "bracelet-or-1.jpg",
      "altText": "Bracelet or vue de face",
      "position": 0
    }
  ],
  "pagination": { ... }
}
```

---

### 2. Récupérer une image
```
GET /api/produits-images/{id}
```

**Réponse (200):** Même format que ci-dessus

---

### 3. Créer une image de produit
```
POST /api/produits-images
Content-Type: application/json
```

**Body:**
```json
{
  "produit_id": 1,
  "image": "bracelet-or-1.jpg",
  "alt_text": "Bracelet or vue de face",
  "position": 0
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Image créée avec succès",
  "data": { ... }
}
```

---

### 4. Mettre à jour une image
```
PUT /api/produits-images/{id}
Content-Type: application/json
```

**Body:**
```json
{
  "alt_text": "Bracelet or vue de côté",
  "position": 1
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Image mise à jour avec succès",
  "data": { ... }
}
```

---

### 5. Supprimer une image
```
DELETE /api/produits-images/{id}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Image supprimée avec succès"
}
```

---

### 6. Récupérer les images d'un produit
```
GET /api/produits-images/produit/{produitId}
```

**Réponse (200):**
```json
{
  "success": true,
  "data": [ ... ]
}
```

---

### 7. Réorganiser les images d'un produit
```
POST /api/produits-images/produit/{produitId}/reorder
Content-Type: application/json
```

**Body:**
```json
{
  "images": [3, 1, 2]
}
```

**Réponse (200):**
```json
{
  "success": true,
  "message": "Images réorganisées avec succès"
}
```

---

## 📊 Statuts de commande

- `pending` - En attente
- `confirmed` - Confirmée
- `shipped` - Expédiée
- `delivered` - Livrée
- `cancelled` - Annulée

---

## 🔍 Codes de réponse HTTP

| Code | Signification |
|------|---------------|
| 200 | OK - Requête réussie |
| 201 | Created - Ressource créée |
| 400 | Bad Request - Données invalides |
| 401 | Unauthorized - Non authentifié |
| 403 | Forbidden - Accès refusé |
| 404 | Not Found - Ressource non trouvée |
| 409 | Conflict - Conflit (ex: doublon) |
| 500 | Internal Server Error - Erreur serveur |

---

## 🔐 Headers requis

```
Content-Type: application/json
Authorization: Bearer {token} (à implémenter)
```

---

## 📝 Notes

- Tous les endpoints retournent du JSON
- Les timestamps sont au format ISO 8601
- Les prix sont en format DECIMAL (ex: "299.99")
- La pagination par défaut est 10 éléments par page
- Les ratings doivent être entre 1 et 5
