<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories', name: 'api_categories_')]
class CategorieController extends AbstractController
{
    public function __construct(
        private CategorieRepository $categorieRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/categories - Récupère toutes les catégories
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->categorieRepository->createQueryBuilder('c');

        $total = count($query->getQuery()->getResult());
        $categories = $query
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->json([
            'success' => true,
            'data' => $categories,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/categories/{id} - Récupère une catégorie par ID
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Categorie $categorie): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $categorie
        ]);
    }

    /**
     * POST /api/categories - Crée une nouvelle catégorie
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['nom'])) {
            return $this->json(['error' => 'Le nom de la catégorie est requis'], Response::HTTP_BAD_REQUEST);
        }

        $categorie = new Categorie();
        $categorie->setNom($data['nom']);
        $categorie->setSlug($data['slug'] ?? $this->slugify($data['nom']));
        $categorie->setDescription($data['description'] ?? null);
        $categorie->setImage($data['image'] ?? null);
        $categorie->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($categorie);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Catégorie créée avec succès',
            'data' => $categorie
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/categories/{id} - Met à jour une catégorie
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Categorie $categorie, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['nom'])) {
            $categorie->setNom($data['nom']);
        }
        if (isset($data['slug'])) {
            $categorie->setSlug($data['slug']);
        }
        if (isset($data['description'])) {
            $categorie->setDescription($data['description']);
        }
        if (isset($data['image'])) {
            $categorie->setImage($data['image']);
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $categorie
        ]);
    }

    /**
     * DELETE /api/categories/{id} - Supprime une catégorie
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Categorie $categorie): JsonResponse
    {
        // Vérifier si la catégorie a des produits
        if (count($categorie->getProduits()) > 0) {
            return $this->json(
                ['error' => 'Impossible de supprimer une catégorie qui contient des produits'],
                Response::HTTP_CONFLICT
            );
        }

        $this->entityManager->remove($categorie);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }

    /**
     * GET /api/categories/{id}/produits - Récupère les produits d'une catégorie
     */
    #[Route('/{id}/produits', name: 'produits', methods: ['GET'])]
    public function produits(Categorie $categorie, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $produits = $categorie->getProduits();
        $total = count($produits);

        // Pagination manuelle
        $produits = $produits->slice(($page - 1) * $limit, $limit);

        return $this->json([
            'success' => true,
            'data' => $produits,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Convertit une chaîne en slug
     */
    private function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = preg_replace('~-+~', '-', $text);
        $text = trim($text, '-');
        return strtolower($text);
    }
}
