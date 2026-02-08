<?php

namespace App\Controller;

use App\Entity\AvisClient;
use App\Repository\AvisClientRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/avis', name: 'api_avis_')]
class AvisClientController extends AbstractController
{
    public function __construct(
        private AvisClientRepository $avisClientRepository,
        private ProduitRepository $produitRepository,
        private UtilisateurRepository $utilisateurRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/avis - Récupérer tous les avis
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->avisClientRepository->createQueryBuilder('a');

        $total = count($query->getQuery()->getResult());
        $avis = $query
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($avis as $av) {
            $data[] = $this->formatAvis($av);
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
     * GET /api/avis/{id} - Récupérer un avis
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(AvisClient $avis): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $this->formatAvis($avis)
        ]);
    }

    /**
     * POST /api/avis - Créer un avis
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['produit_id']) || !isset($data['utilisateur_id']) || !isset($data['rating'])) {
            return $this->json(
                ['error' => 'produit_id, utilisateur_id et rating sont requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Valider le rating (1-5)
        if ($data['rating'] < 1 || $data['rating'] > 5) {
            return $this->json(
                ['error' => 'Le rating doit être entre 1 et 5'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $produit = $this->produitRepository->find($data['produit_id']);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $utilisateur = $this->utilisateurRepository->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si l'utilisateur a déjà laissé un avis pour ce produit
        $existingAvis = $this->avisClientRepository->findOneBy([
            'id_produit' => $produit,
            'id_utilisateur' => $utilisateur
        ]);

        if ($existingAvis) {
            return $this->json(
                ['error' => 'Vous avez déjà laissé un avis pour ce produit'],
                Response::HTTP_CONFLICT
            );
        }

        $avis = new AvisClient();
        $avis->setIdProduit($produit);
        $avis->setIdUtilisateur($utilisateur);
        $avis->setRating($data['rating']);
        $avis->setCommentaire($data['commentaire'] ?? null);
        $avis->setCreatedAt(new \DateTimeImmutable());
        $avis->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($avis);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Avis créé avec succès',
            'data' => $this->formatAvis($avis)
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/avis/{id} - Mettre à jour un avis
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(AvisClient $avis, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['rating'])) {
            if ($data['rating'] < 1 || $data['rating'] > 5) {
                return $this->json(
                    ['error' => 'Le rating doit être entre 1 et 5'],
                    Response::HTTP_BAD_REQUEST
                );
            }
            $avis->setRating($data['rating']);
        }

        if (isset($data['commentaire'])) {
            $avis->setCommentaire($data['commentaire']);
        }

        $avis->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Avis mis à jour avec succès',
            'data' => $this->formatAvis($avis)
        ]);
    }

    /**
     * DELETE /api/avis/{id} - Supprimer un avis
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(AvisClient $avis): JsonResponse
    {
        $this->entityManager->remove($avis);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Avis supprimé avec succès'
        ]);
    }

    /**
     * GET /api/avis/produit/{produitId} - Récupérer les avis d'un produit
     */
    #[Route('/produit/{produitId}', name: 'by_produit', methods: ['GET'])]
    public function byProduit(int $produitId, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $produit = $this->produitRepository->find($produitId);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $query = $this->avisClientRepository->createQueryBuilder('a')
            ->where('a.id_produit = :produit')
            ->setParameter('produit', $produit);

        $total = count($query->getQuery()->getResult());
        $avis = $query
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        $totalRating = 0;

        foreach ($avis as $av) {
            $data[] = $this->formatAvis($av);
            $totalRating += $av->getRating();
        }

        $moyenneRating = $total > 0 ? round($totalRating / $total, 2) : 0;

        return $this->json([
            'success' => true,
            'data' => $data,
            'stats' => [
                'total' => $total,
                'moyenneRating' => $moyenneRating
            ],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/avis/utilisateur/{utilisateurId} - Récupérer les avis d'un utilisateur
     */
    #[Route('/utilisateur/{utilisateurId}', name: 'by_utilisateur', methods: ['GET'])]
    public function byUtilisateur(int $utilisateurId, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $utilisateur = $this->utilisateurRepository->find($utilisateurId);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $query = $this->avisClientRepository->createQueryBuilder('a')
            ->where('a.id_utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur);

        $total = count($query->getQuery()->getResult());
        $avis = $query
            ->orderBy('a.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($avis as $av) {
            $data[] = $this->formatAvis($av);
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
     * Formater un avis
     */
    private function formatAvis(AvisClient $avis): array
    {
        return [
            'id' => $avis->getId(),
            'produit' => [
                'id' => $avis->getIdProduit()->getId(),
                'nom' => $avis->getIdProduit()->getNom()
            ],
            'utilisateur' => [
                'id' => $avis->getIdUtilisateur()->getId(),
                'nom' => $avis->getIdUtilisateur()->getNom(),
                'prenom' => $avis->getIdUtilisateur()->getPrenom()
            ],
            'rating' => $avis->getRating(),
            'commentaire' => $avis->getCommentaire(),
            'createdAt' => $avis->getCreatedAt(),
            'updatedAt' => $avis->getUpdatedAt()
        ];
    }
}
