<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\PanierArticles;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\PanierRepository;
use App\Repository\PanierArticlesRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/panier', name: 'api_panier_')]
class PanierController extends AbstractController
{
    public function __construct(
        private PanierRepository $panierRepository,
        private PanierArticlesRepository $panierArticlesRepository,
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/panier/{userId} - Récupère le panier d'un utilisateur
     */
    #[Route('/{userId}', name: 'show', methods: ['GET'])]
    public function show(int $userId): JsonResponse
    {
        $panier = $this->panierRepository->findOneBy(['utilisateur' => $userId]);

        if (!$panier) {
            return $this->json(['error' => 'Panier non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $articles = $panier->getPanierArticles();
        $total = 0;

        $articlesData = [];
        foreach ($articles as $article) {
            $prix = (float)$article->getProduitId()->getPrix();
            $sousTotal = $prix * $article->getQuantite();
            $total += $sousTotal;

            $articlesData[] = [
                'id' => $article->getId(),
                'produit' => [
                    'id' => $article->getProduitId()->getId(),
                    'nom' => $article->getProduitId()->getNom(),
                    'prix' => $article->getProduitId()->getPrix(),
                    'image' => $article->getProduitId()->getImage()
                ],
                'quantite' => $article->getQuantite(),
                'prixUnitaire' => $prix,
                'sousTotal' => $sousTotal,
                'addedAt' => $article->getAddedAt()
            ];
        }

        return $this->json([
            'success' => true,
            'data' => [
                'id' => $panier->getId(),
                'articles' => $articlesData,
                'total' => $total,
                'nombreArticles' => count($articles)
            ]
        ]);
    }

    /**
     * POST /api/panier/{userId}/articles - Ajouter un article au panier
     */
    #[Route('/{userId}/articles', name: 'add_article', methods: ['POST'])]
    public function addArticle(int $userId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['produit_id']) || !isset($data['quantite'])) {
            return $this->json(
                ['error' => 'produit_id et quantite sont requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $panier = $this->panierRepository->findOneBy(['utilisateur' => $userId]);
        if (!$panier) {
            return $this->json(['error' => 'Panier non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $produit = $this->produitRepository->find($data['produit_id']);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le produit est déjà dans le panier
        $existingArticle = $this->panierArticlesRepository->findOneBy([
            'panier_id' => $panier,
            'produit_id' => $produit
        ]);

        if ($existingArticle) {
            // Augmenter la quantité
            $existingArticle->setQuantite($existingArticle->getQuantite() + $data['quantite']);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Quantité mise à jour',
                'data' => [
                    'id' => $existingArticle->getId(),
                    'quantite' => $existingArticle->getQuantite()
                ]
            ]);
        }

        // Créer un nouvel article
        $article = new PanierArticles();
        $article->setPanierId($panier);
        $article->setProduitId($produit);
        $article->setQuantite($data['quantite']);
        $article->setAddedAt(new \DateTimeImmutable());

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Article ajouté au panier',
            'data' => [
                'id' => $article->getId(),
                'produit_id' => $produit->getId(),
                'quantite' => $article->getQuantite()
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/panier/articles/{articleId} - Mettre à jour la quantité d'un article
     */
    #[Route('/articles/{articleId}', name: 'update_article', methods: ['PUT'])]
    public function updateArticle(int $articleId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['quantite'])) {
            return $this->json(['error' => 'quantite est requis'], Response::HTTP_BAD_REQUEST);
        }

        $article = $this->panierArticlesRepository->find($articleId);
        if (!$article) {
            return $this->json(['error' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        if ($data['quantite'] <= 0) {
            return $this->json(['error' => 'La quantité doit être positive'], Response::HTTP_BAD_REQUEST);
        }

        $article->setQuantite($data['quantite']);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Article mis à jour',
            'data' => [
                'id' => $article->getId(),
                'quantite' => $article->getQuantite()
            ]
        ]);
    }

    /**
     * DELETE /api/panier/articles/{articleId} - Supprimer un article du panier
     */
    #[Route('/articles/{articleId}', name: 'remove_article', methods: ['DELETE'])]
    public function removeArticle(int $articleId): JsonResponse
    {
        $article = $this->panierArticlesRepository->find($articleId);
        if (!$article) {
            return $this->json(['error' => 'Article non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Article supprimé du panier'
        ]);
    }

    /**
     * DELETE /api/panier/{userId} - Vider le panier
     */
    #[Route('/{userId}', name: 'clear', methods: ['DELETE'])]
    public function clear(int $userId): JsonResponse
    {
        $panier = $this->panierRepository->findOneBy(['utilisateur' => $userId]);
        if (!$panier) {
            return $this->json(['error' => 'Panier non trouvé'], Response::HTTP_NOT_FOUND);
        }

        foreach ($panier->getPanierArticles() as $article) {
            $this->entityManager->remove($article);
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Panier vidé'
        ]);
    }
}
