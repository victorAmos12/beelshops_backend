<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    /**
     * GET /api - Endpoint de bienvenue API
     */
    #[Route('', name: 'welcome', methods: ['GET'])]
    public function welcome(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'Bienvenue sur l\'API BeelShops',
            'version' => '1.0.0',
            'endpoints' => [
                'produits' => '/api/produits',
                'categories' => '/api/categories',
                'utilisateurs' => '/api/utilisateurs',
                'documentation' => '/API_DOCUMENTATION.md'
            ]
        ]);
    }
}
