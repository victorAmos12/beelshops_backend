<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/produits', name: 'api_produits_')]
class ProduitController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    /**
     * GET /api/produits - Récupère tous les produits
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $category = $request->query->get('category');
        $search = $request->query->get('search');

        $query = $this->produitRepository->createQueryBuilder('p')
            ->where('p.isActive = true');

        if ($category) {
            $query->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        if ($search) {
            $query->andWhere('p.nom LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $total = count($query->getQuery()->getResult());
        $produits = $query
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

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
     * GET /api/produits/{id} - Récupère un produit par ID
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Produit $produit): JsonResponse
    {
        if (!$produit->isActive()) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data' => $produit
        ]);
    }

    /**
     * POST /api/produits - Crée un nouveau produit
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        $produit = new Produit();
        $produit->setNom($data['nom'] ?? '');
        $produit->setSlug($data['slug'] ?? $this->slugify($data['nom'] ?? ''));
        $produit->setDescription($data['description'] ?? '');
        $produit->setPrix($data['prix'] ?? '0');
        $produit->setStock($data['stock'] ?? 0);
        $produit->setWeight($data['weight'] ?? null);
        $produit->setImage($data['image'] ?? null);
        $produit->setIsActive($data['isActive'] ?? true);
        $produit->setCreatedAt(new \DateTimeImmutable());
        $produit->setUpdatedAt(new \DateTimeImmutable());

        if (isset($data['category_id'])) {
            $category = $this->entityManager->getRepository('App:Categorie')->find($data['category_id']);
            if ($category) {
                $produit->setCategory($category);
            }
        }

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Produit créé avec succès',
            'data' => $produit
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/produits/{id} - Met à jour un produit
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Produit $produit, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['nom'])) {
            $produit->setNom($data['nom']);
        }
        if (isset($data['slug'])) {
            $produit->setSlug($data['slug']);
        }
        if (isset($data['description'])) {
            $produit->setDescription($data['description']);
        }
        if (isset($data['prix'])) {
            $produit->setPrix($data['prix']);
        }
        if (isset($data['stock'])) {
            $produit->setStock($data['stock']);
        }
        if (isset($data['weight'])) {
            $produit->setWeight($data['weight']);
        }
        if (isset($data['image'])) {
            $produit->setImage($data['image']);
        }
        if (isset($data['isActive'])) {
            $produit->setIsActive($data['isActive']);
        }
        if (isset($data['category_id'])) {
            $category = $this->entityManager->getRepository('App:Categorie')->find($data['category_id']);
            if ($category) {
                $produit->setCategory($category);
            }
        }

        $produit->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Produit mis à jour avec succès',
            'data' => $produit
        ]);
    }

    /**
     * DELETE /api/produits/{id} - Supprime un produit
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Produit $produit): JsonResponse
    {
        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Produit supprimé avec succès'
        ]);
    }

    /**
     * GET /api/produits/categorie/{categoryId} - Récupère les produits par catégorie
     */
    #[Route('/categorie/{categoryId}', name: 'by_category', methods: ['GET'])]
    public function byCategory(int $categoryId, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->produitRepository->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.isActive = true')
            ->setParameter('category', $categoryId);

        $total = count($query->getQuery()->getResult());
        $produits = $query
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

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
