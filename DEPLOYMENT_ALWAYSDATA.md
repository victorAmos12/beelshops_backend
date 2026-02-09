# Guide de Déploiement sur AlwaysData

## Configuration CORS pour AlwaysData

Le problème CORS que vous rencontrez vient du serveur qui ne retourne pas les headers CORS. Voici comment le résoudre :

### 1. Vérifier la configuration Apache

Sur AlwaysData, assurez-vous que les modules Apache suivants sont activés :
- `mod_rewrite`
- `mod_headers`

### 2. Fichiers .htaccess

Les fichiers `.htaccess` ont été créés à la racine et dans le répertoire `public/` pour gérer les headers CORS.

### 3. Configuration Symfony

La configuration CORS a été mise à jour pour accepter toutes les origines en développement.

### 4. Vérifier le déploiement

Après le déploiement, testez avec :

```bash
curl -X OPTIONS https://beelshops.alwaysdata.net/api/auth/login \
  -H "Origin: https://beelshops.vercel.app" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type" \
  -v
```

Vous devriez voir les headers CORS dans la réponse :
```
Access-Control-Allow-Origin: https://beelshops.vercel.app
Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD
Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin, X-API-KEY
```

### 5. Endpoints Publics

Les endpoints suivants sont maintenant accessibles sans authentification :

- `GET /api/produits` - Récupérer tous les produits
- `GET /api/categories` - Récupérer toutes les catégories
- `GET /api/produit-images` - Récupérer les images des produits
- `GET /api/avis-client` - Récupérer les avis clients
- `POST /api/auth/login` - Connexion
- `POST /api/auth/register` - Inscription

### 6. Endpoints Protégés

Les endpoints suivants nécessitent un token JWT :

- `GET /api/auth/me` - Récupérer les infos de l'utilisateur connecté
- `POST /api/auth/refresh` - Rafraîchir le token
- `GET /api/utilisateurs` - Récupérer tous les utilisateurs (ADMIN)
- `POST /api/utilisateurs` - Créer un utilisateur (ADMIN)
- `PUT /api/utilisateurs/{id}` - Modifier un utilisateur (ADMIN)
- `DELETE /api/utilisateurs/{id}` - Supprimer un utilisateur (ADMIN)

### 7. Dépannage

Si vous continuez à avoir des erreurs CORS :

1. Vérifiez que les fichiers `.htaccess` sont bien uploadés
2. Vérifiez que `mod_headers` est activé sur le serveur
3. Testez l'endpoint `/api/debug/cors` pour voir la configuration actuelle
4. Vérifiez les logs du serveur Apache

### 8. Frontend - Configuration de l'URL API

Dans votre frontend Angular/React, configurez l'URL de l'API :

```typescript
// environment.prod.ts
export const environment = {
  production: true,
  apiUrl: 'https://beelshops.alwaysdata.net'
};
```

### 9. Requête de Connexion Exemple

```bash
curl -X POST https://beelshops.alwaysdata.net/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@beelshop.com",
    "password": "password123"
  }'
```

Réponse attendue :
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

## Checklist de Déploiement

- [ ] Clés JWT générées et uploadées dans `config/jwt/`
- [ ] Fichiers `.htaccess` uploadés
- [ ] Base de données migrée
- [ ] Utilisateurs admin et client créés
- [ ] Variables d'environnement configurées
- [ ] CORS testé avec curl
- [ ] Connexion testée depuis le frontend
- [ ] Produits accessibles sans authentification
