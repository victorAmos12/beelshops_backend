<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\ArticleCommande;
use App\Entity\Utilisateur;
use App\Repository\CommandesRepository;
use App\Repository\ArticleCommandeRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ProduitRepository;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/commandes', name: 'api_commandes_')]
class CommandesController extends AbstractController
{
    public function __construct(
        private CommandesRepository $commandesRepository,
        private ArticleCommandeRepository $articleCommandeRepository,
        private UtilisateurRepository $utilisateurRepository,
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $entityManager,
        private SlugService $slugService
    ) {}

    /**
     * GET /api/commandes - Récupérer toutes les commandes
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $status = $request->query->get('status');

        $query = $this->commandesRepository->createQueryBuilder('c');

        if ($status) {
            $query->where('c.status = :status')
                ->setParameter('status', $status);
        }

        $total = count($query->getQuery()->getResult());
        $commandes = $query
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($commandes as $commande) {
            $data[] = $this->formatCommande($commande);
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
     * GET /api/commandes/{id} - Récupérer une commande par ID
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Commandes $commande): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $this->formatCommande($commande)
        ]);
    }

    /**
     * POST /api/commandes - Créer une nouvelle commande
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['utilisateur_id']) || !isset($data['articles'])) {
            return $this->json(
                ['error' => 'utilisateur_id et articles sont requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $utilisateur = $this->utilisateurRepository->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $commande = new Commandes();
        $commande->setUtilisateur($utilisateur);
        $commande->setNumOrdre($this->slugService->generateOrderNumber());
        $commande->setStatus($data['status'] ?? 'pending');
        $commande->setAdresseLivraison($data['adresse_livraison'] ?? '');
        $commande->setAdresseAppartement($data['adresse_appartement'] ?? null);
        $commande->setCreatedAt(new \DateTimeImmutable());
        $commande->setUpdatedAt(new \DateTimeImmutable());

        $prixTotal = 0;

        // Ajouter les articles
        foreach ($data['articles'] as $articleData) {
            if (!isset($articleData['produit_id']) || !isset($articleData['quantite'])) {
                return $this->json(
                    ['error' => 'Chaque article doit avoir produit_id et quantite'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $produit = $this->produitRepository->find($articleData['produit_id']);
            if (!$produit) {
                return $this->json(
                    ['error' => "Produit {$articleData['produit_id']} non trouvé"],
                    Response::HTTP_NOT_FOUND
                );
            }

            $quantite = $articleData['quantite'];
            $prixUnitaire = $produit->getPrix();
            $prixArticle = (float)$prixUnitaire * $quantite;
            $prixTotal += $prixArticle;

            $article = new ArticleCommande();
            $article->setCommande($commande);
            $article->setProduit($produit);
            $article->setQuantite($quantite);
            $article->setPrixUnitaire($prixUnitaire);
            $article->setPrixTotal($this->slugService->formatPrice($prixArticle));

            $commande->addArticleCommande($article);
        }

        $commande->setPrixTotal($this->slugService->formatPrice($prixTotal));

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Commande créée avec succès',
            'data' => $this->formatCommande($commande)
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/commandes/{id} - Mettre à jour une commande
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Commandes $commande, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['status'])) {
            $commande->setStatus($data['status']);
        }
        if (isset($data['adresse_livraison'])) {
            $commande->setAdresseLivraison($data['adresse_livraison']);
        }
        if (isset($data['adresse_appartement'])) {
            $commande->setAdresseAppartement($data['adresse_appartement']);
        }

        $commande->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Commande mise à jour avec succès',
            'data' => $this->formatCommande($commande)
        ]);
    }

    /**
     * DELETE /api/commandes/{id} - Supprimer une commande
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Commandes $commande): JsonResponse
    {
        $this->entityManager->remove($commande);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès'
        ]);
    }

    /**
     * GET /api/commandes/utilisateur/{userId} - Récupérer les commandes d'un utilisateur
     */
    #[Route('/utilisateur/{userId}', name: 'by_user', methods: ['GET'])]
    public function byUser(int $userId, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->commandesRepository->createQueryBuilder('c')
            ->where('c.utilisateur = :userId')
            ->setParameter('userId', $userId);

        $total = count($query->getQuery()->getResult());
        $commandes = $query
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($commandes as $commande) {
            $data[] = $this->formatCommande($commande);
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
     * Formater une commande avec ses articles
     */
    private function formatCommande(Commandes $commande): array
    {
        $articles = [];
        foreach ($commande->getArticleCommandes() as $article) {
            $articles[] = [
                'id' => $article->getId(),
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

        return [
            'id' => $commande->getId(),
            'numOrdre' => $commande->getNumOrdre(),
            'utilisateur' => [
                'id' => $commande->getUtilisateur()->getId(),
                'nom' => $commande->getUtilisateur()->getNom(),
                'prenom' => $commande->getUtilisateur()->getPrenom(),
                'email' => $commande->getUtilisateur()->getEmail()
            ],
            'articles' => $articles,
            'prixTotal' => $commande->getPrixTotal(),
            'status' => $commande->getStatus(),
            'adresseLivraison' => $commande->getAdresseLivraison(),
            'adresseAppartement' => $commande->getAdresseAppartement(),
            'createdAt' => $commande->getCreatedAt(),
            'updatedAt' => $commande->getUpdatedAt()
        ];
    }
}
