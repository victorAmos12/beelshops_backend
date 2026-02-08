<?php

namespace App\Controller;

use App\Entity\ListeSouhaits;
use App\Repository\ListeSouhaitsRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/liste-souhaits', name: 'api_liste_souhaits_')]
class ListeSouhaitsController extends AbstractController
{
    public function __construct(
        private ListeSouhaitsRepository $listeSouhaitsRepository,
        private ProduitRepository $produitRepository,
        private UtilisateurRepository $utilisateurRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/liste-souhaits/{userId} - Récupérer la liste de souhaits d'un utilisateur
     */
    #[Route('/{userId}', name: 'show', methods: ['GET'])]
    public function show(int $userId, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $utilisateur = $this->utilisateurRepository->find($userId);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $query = $this->listeSouhaitsRepository->createQueryBuilder('l')
            ->where('l.id_utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur);

        $total = count($query->getQuery()->getResult());
        $souhaits = $query
            ->orderBy('l.addedAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($souhaits as $souhait) {
            $data[] = $this->formatSouhait($souhait);
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
     * POST /api/liste-souhaits - Ajouter un produit à la liste de souhaits
     */
    #[Route('', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['utilisateur_id']) || !isset($data['produit_id'])) {
            return $this->json(
                ['error' => 'utilisateur_id et produit_id sont requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $utilisateur = $this->utilisateurRepository->find($data['utilisateur_id']);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $produit = $this->produitRepository->find($data['produit_id']);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le produit est déjà dans la liste
        $existing = $this->listeSouhaitsRepository->findOneBy([
            'id_utilisateur' => $utilisateur,
            'id_produit' => $produit
        ]);

        if ($existing) {
            return $this->json(
                ['error' => 'Ce produit est déjà dans votre liste de souhaits'],
                Response::HTTP_CONFLICT
            );
        }

        $souhait = new ListeSouhaits();
        $souhait->setIdUtilisateur($utilisateur);
        $souhait->setIdProduit($produit);
        $souhait->setAddedAt(new \DateTimeImmutable());

        $this->entityManager->persist($souhait);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Produit ajouté à la liste de souhaits',
            'data' => $this->formatSouhait($souhait)
        ], Response::HTTP_CREATED);
    }

    /**
     * DELETE /api/liste-souhaits/{id} - Supprimer un produit de la liste de souhaits
     */
    #[Route('/{id}', name: 'remove', methods: ['DELETE'])]
    public function remove(ListeSouhaits $souhait): JsonResponse
    {
        $this->entityManager->remove($souhait);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Produit supprimé de la liste de souhaits'
        ]);
    }

    /**
     * DELETE /api/liste-souhaits/utilisateur/{userId}/produit/{produitId} - Supprimer un produit spécifique
     */
    #[Route('/utilisateur/{userId}/produit/{produitId}', name: 'remove_by_ids', methods: ['DELETE'])]
    public function removeByIds(int $userId, int $produitId): JsonResponse
    {
        $utilisateur = $this->utilisateurRepository->find($userId);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $produit = $this->produitRepository->find($produitId);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $souhait = $this->listeSouhaitsRepository->findOneBy([
            'id_utilisateur' => $utilisateur,
            'id_produit' => $produit
        ]);

        if (!$souhait) {
            return $this->json(['error' => 'Souhait non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($souhait);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Produit supprimé de la liste de souhaits'
        ]);
    }

    /**
     * GET /api/liste-souhaits/check/{userId}/{produitId} - Vérifier si un produit est dans la liste
     */
    #[Route('/check/{userId}/{produitId}', name: 'check', methods: ['GET'])]
    public function check(int $userId, int $produitId): JsonResponse
    {
        $utilisateur = $this->utilisateurRepository->find($userId);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $produit = $this->produitRepository->find($produitId);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $souhait = $this->listeSouhaitsRepository->findOneBy([
            'id_utilisateur' => $utilisateur,
            'id_produit' => $produit
        ]);

        return $this->json([
            'success' => true,
            'inWishlist' => $souhait !== null,
            'wishlistId' => $souhait ? $souhait->getId() : null
        ]);
    }

    /**
     * Formater un souhait
     */
    private function formatSouhait(ListeSouhaits $souhait): array
    {
        return [
            'id' => $souhait->getId(),
            'produit' => [
                'id' => $souhait->getIdProduit()->getId(),
                'nom' => $souhait->getIdProduit()->getNom(),
                'prix' => $souhait->getIdProduit()->getPrix(),
                'image' => $souhait->getIdProduit()->getImage(),
                'slug' => $souhait->getIdProduit()->getSlug()
            ],
            'addedAt' => $souhait->getAddedAt()
        ];
    }
}
