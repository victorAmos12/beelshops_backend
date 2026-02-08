<?php

namespace App\Controller;

use App\Entity\ArticleCommande;
use App\Repository\ArticleCommandeRepository;
use App\Repository\CommandesRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/articles-commandes', name: 'api_articles_commandes_')]
class ArticleCommandeController extends AbstractController
{
    public function __construct(
        private ArticleCommandeRepository $articleCommandeRepository,
        private CommandesRepository $commandesRepository,
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/articles-commandes - Récupérer tous les articles de commande
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->articleCommandeRepository->createQueryBuilder('a');

        $total = count($query->getQuery()->getResult());
        $articles = $query
            ->orderBy('a.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($articles as $article) {
            $data[] = $this->formatArticle($article);
        }

        return $this->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/articles-commandes/{id} - Récupérer un article de commande
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ArticleCommande $article): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $this->formatArticle($article)
        ]);
    }

    /**
     * POST /api/articles-commandes - Créer un article de commande
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['commande_id']) || !isset($data['produit_id']) || !isset($data['quantite'])) {
            return $this->json(
                ['error' => 'commande_id, produit_id et quantite sont requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $commande = $this->commandesRepository->find($data['commande_id']);
        if (!$commande) {
            return $this->json(['error' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $produit = $this->produitRepository->find($data['produit_id']);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $article = new ArticleCommande();
        $article->setCommande($commande);
        $article->setProduit($produit);
        $article->setQuantite($data['quantite']);
        $article->setPrixUnitaire($produit->getPrix());
        $article->setPrixTotal((float)$produit->getPrix() * $data['quantite']);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Article de commande créé avec succès',
            'data' => $this->formatArticle($article)
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/articles-commandes/{id} - Mettre à jour un article de commande
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(ArticleCommande $article, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['quantite'])) {
            $article->setQuantite($data['quantite']);
            $prixTotal = (float)$article->getPrixUnitaire() * $data['quantite'];
            $article->setPrixTotal($prixTotal);
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Article de commande mis à jour avec succès',
            'data' => $this->formatArticle($article)
        ]);
    }

    /**
     * DELETE /api/articles-commandes/{id} - Supprimer un article de commande
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(ArticleCommande $article): JsonResponse
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Article de commande supprimé avec succès'
        ]);
    }

    /**
     * GET /api/articles-commandes/commande/{commandeId} - Récupérer les articles d'une commande
     */
    #[Route('/commande/{commandeId}', name: 'by_commande', methods: ['GET'])]
    public function byCommande(int $commandeId): JsonResponse
    {
        $commande = $this->commandesRepository->find($commandeId);
        if (!$commande) {
            return $this->json(['error' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $articles = $commande->getArticleCommandes();
        $data = [];

        foreach ($articles as $article) {
            $data[] = $this->formatArticle($article);
        }

        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Formater un article de commande
     */
    private function formatArticle(ArticleCommande $article): array
    {
        return [
            'id' => $article->getId(),
            'commande' => [
                'id' => $article->getCommande()->getId(),
                'numOrdre' => $article->getCommande()->getNumOrdre()
            ],
            'produit' => [
                'id' => $article->getProduit()->getId(),
                'nom' => $article->getProduit()->getNom(),
                'image' => $article->getProduit()->getImage()
            ],
            'quantite' => $article->getQuantite(),
            'prixUnitaire' => $article->getPrixUnitaire(),
            'prixTotal' => $article->getPrixTotal()
        ];
    }
}
