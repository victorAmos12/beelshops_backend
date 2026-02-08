#!/bin/bash

# Script de test des endpoints API BeelShops
# Utilisation: bash test_api.sh

BASE_URL="http://localhost:8000/api"

echo "=========================================="
echo "Tests API BeelShops"
echo "=========================================="
echo ""

# Couleurs pour l'affichage
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: Endpoint de bienvenue
echo -e "${YELLOW}Test 1: Endpoint de bienvenue${NC}"
curl -X GET "$BASE_URL" -H "Content-Type: application/json"
echo -e "\n\n"

# Test 2: Créer une catégorie
echo -e "${YELLOW}Test 2: Créer une catégorie${NC}"
CATEGORY_RESPONSE=$(curl -s -X POST "$BASE_URL/categories" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Bracelets",
    "slug": "bracelets",
    "description": "Tous nos bracelets",
    "image": "bracelets.jpg"
  }')
echo "$CATEGORY_RESPONSE" | jq .
CATEGORY_ID=$(echo "$CATEGORY_RESPONSE" | jq -r '.data.id // empty')
echo -e "\n"

# Test 3: Récupérer toutes les catégories
echo -e "${YELLOW}Test 3: Récupérer toutes les catégories${NC}"
curl -s -X GET "$BASE_URL/categories" -H "Content-Type: application/json" | jq .
echo -e "\n\n"

# Test 4: Créer un produit
echo -e "${YELLOW}Test 4: Créer un produit${NC}"
if [ ! -z "$CATEGORY_ID" ]; then
  PRODUCT_RESPONSE=$(curl -s -X POST "$BASE_URL/produits" \
    -H "Content-Type: application/json" \
    -d "{
      \"nom\": \"Bracelet Or 18K\",
      \"slug\": \"bracelet-or-18k\",
      \"description\": \"Bracelet en or 18 carats\",
      \"prix\": \"299.99\",
      \"stock\": 50,
      \"weight\": \"5g\",
      \"image\": \"bracelet-or.jpg\",
      \"isActive\": true,
      \"category_id\": $CATEGORY_ID
    }")
  echo "$PRODUCT_RESPONSE" | jq .
  PRODUCT_ID=$(echo "$PRODUCT_RESPONSE" | jq -r '.data.id // empty')
else
  echo -e "${RED}Erreur: Impossible de créer une catégorie${NC}"
fi
echo -e "\n\n"

# Test 5: Récupérer tous les produits
echo -e "${YELLOW}Test 5: Récupérer tous les produits${NC}"
curl -s -X GET "$BASE_URL/produits" -H "Content-Type: application/json" | jq .
echo -e "\n\n"

# Test 6: Récupérer un produit par ID
echo -e "${YELLOW}Test 6: Récupérer un produit par ID${NC}"
if [ ! -z "$PRODUCT_ID" ]; then
  curl -s -X GET "$BASE_URL/produits/$PRODUCT_ID" -H "Content-Type: application/json" | jq .
else
  echo -e "${RED}Erreur: Impossible de récupérer le produit${NC}"
fi
echo -e "\n\n"

# Test 7: Créer un utilisateur
echo -e "${YELLOW}Test 7: Créer un utilisateur${NC}"
USER_RESPONSE=$(curl -s -X POST "$BASE_URL/utilisateurs" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "TestPassword123!",
    "nom": "Dupont",
    "prenom": "Jean",
    "phone": "0612345678"
  }')
echo "$USER_RESPONSE" | jq .
USER_ID=$(echo "$USER_RESPONSE" | jq -r '.data.id // empty')
echo -e "\n\n"

# Test 8: Récupérer tous les utilisateurs
echo -e "${YELLOW}Test 8: Récupérer tous les utilisateurs${NC}"
curl -s -X GET "$BASE_URL/utilisateurs" -H "Content-Type: application/json" | jq .
echo -e "\n\n"

# Test 9: Connexion utilisateur
echo -e "${YELLOW}Test 9: Connexion utilisateur${NC}"
curl -s -X POST "$BASE_URL/utilisateurs/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "TestPassword123!"
  }' | jq .
echo -e "\n\n"

# Test 10: Mettre à jour un produit
echo -e "${YELLOW}Test 10: Mettre à jour un produit${NC}"
if [ ! -z "$PRODUCT_ID" ]; then
  curl -s -X PUT "$BASE_URL/produits/$PRODUCT_ID" \
    -H "Content-Type: application/json" \
    -d '{
      "prix": "349.99",
      "stock": 45
    }' | jq .
else
  echo -e "${RED}Erreur: Impossible de mettre à jour le produit${NC}"
fi
echo -e "\n\n"

echo -e "${GREEN}=========================================="
echo "Tests terminés"
echo "==========================================${NC}"
