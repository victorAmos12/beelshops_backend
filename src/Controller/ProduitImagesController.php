<?php

namespace App\Controller;

use App\Entity\ProduitImages;
use App\Repository\ProduitImagesRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/produits-images', name: 'api_produits_images_')]
class ProduitImagesController extends AbstractController
{
    public function __construct(
        private ProduitImagesRepository $produitImagesRepository,
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * GET /api/produits-images - Récupérer toutes les images
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->produitImagesRepository->createQueryBuilder('p');

        $total = count($query->getQuery()->getResult());
        $images = $query
            ->orderBy('p.position', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($images as $image) {
            $data[] = $this->formatImage($image);
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
     * GET /api/produits-images/{id} - Récupérer une image
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ProduitImages $image): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $this->formatImage($image)
        ]);
    }

    /**
     * POST /api/produits-images - Créer une image de produit
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['produit_id']) || !isset($data['image'])) {
            return $this->json(
                ['error' => 'produit_id et image sont requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $produit = $this->produitRepository->find($data['produit_id']);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $image = new ProduitImages();
        $image->setProduit($produit);
        $image->setImage($data['image']);
        $image->setAltText($data['alt_text'] ?? null);
        $image->setPosition($data['position'] ?? 0);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Image créée avec succès',
            'data' => $this->formatImage($image)
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/produits-images/{id} - Mettre à jour une image
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(ProduitImages $image, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['image'])) {
            $image->setImage($data['image']);
        }
        if (isset($data['alt_text'])) {
            $image->setAltText($data['alt_text']);
        }
        if (isset($data['position'])) {
            $image->setPosition($data['position']);
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Image mise à jour avec succès',
            'data' => $this->formatImage($image)
        ]);
    }

    /**
     * DELETE /api/produits-images/{id} - Supprimer une image
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(ProduitImages $image): JsonResponse
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Image supprimée avec succès'
        ]);
    }

    /**
     * GET /api/produits-images/produit/{produitId} - Récupérer les images d'un produit
     */
    #[Route('/produit/{produitId}', name: 'by_produit', methods: ['GET'])]
    public function byProduit(int $produitId): JsonResponse
    {
        $produit = $this->produitRepository->find($produitId);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $images = $this->produitImagesRepository->findBy(
            ['produit' => $produit],
            ['position' => 'ASC']
        );

        $data = [];
        foreach ($images as $image) {
            $data[] = $this->formatImage($image);
        }

        return $this->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * POST /api/produits-images/produit/{produitId}/reorder - Réorganiser les images
     */
    #[Route('/produit/{produitId}/reorder', name: 'reorder', methods: ['POST'])]
    public function reorder(int $produitId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['images']) || !is_array($data['images'])) {
            return $this->json(['error' => 'images array est requis'], Response::HTTP_BAD_REQUEST);
        }

        $produit = $this->produitRepository->find($produitId);
        if (!$produit) {
            return $this->json(['error' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        foreach ($data['images'] as $index => $imageId) {
            $image = $this->produitImagesRepository->find($imageId);
            if ($image && $image->getProduit()->getId() === $produitId) {
                $image->setPosition($index);
            }
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Images réorganisées avec succès'
        ]);
    }

    /**
     * Formater une image
     */
    private function formatImage(ProduitImages $image): array
    {
        return [
            'id' => $image->getId(),
            'produit' => [
                'id' => $image->getProduit()->getId(),
                'nom' => $image->getProduit()->getNom()
            ],
            'image' => $image->getImage(),
            'altText' => $image->getAltText(),
            'position' => $image->getPosition()
        ];
    }
}
