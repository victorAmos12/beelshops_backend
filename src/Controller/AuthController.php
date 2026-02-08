<?php

namespace App\Controller;

use App\Service\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private AuthenticationService $authenticationService,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * POST /api/auth/login - Connexion utilisateur
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/login', name: 'login', methods: ['POST', 'OPTIONS'])]
    public function login(Request $request): JsonResponse
    {
        // Gérer les requêtes OPTIONS (preflight CORS)
        if ($request->getMethod() === 'OPTIONS') {
            return $this->json([], Response::HTTP_OK);
        }

        try {
            $content = $request->getContent();
            
            if (empty($content)) {
                return $this->json(
                    ['error' => 'Corps de la requête vide'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $data = json_decode($content, true);

            if ($data === null) {
                return $this->json(
                    ['error' => 'JSON invalide: ' . json_last_error_msg()],
                    Response::HTTP_BAD_REQUEST
                );
            }

            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json(
                    ['error' => 'Email et mot de passe requis'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $result = $this->authenticationService->authenticate(
                $data['email'],
                $data['password']
            );

            return $this->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            $statusCode = (int)$e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
            
            // Limiter le code HTTP à des valeurs valides
            if ($statusCode < 400 || $statusCode >= 600) {
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return $this->json(
                [
                    'error' => $e->getMessage(),
                    'code' => $statusCode
                ],
                $statusCode
            );
        }
    }

    /**
     * POST /api/auth/register - Inscription utilisateur
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return $this->json(
                    ['error' => 'Données invalides'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $utilisateur = $this->authenticationService->register($data);
            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Inscription réussie',
                'data' => [
                    'id' => $utilisateur->getId(),
                    'email' => $utilisateur->getEmail(),
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                    'roles' => $utilisateur->getRoles()
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
            return $this->json(
                ['error' => $e->getMessage()],
                $statusCode
            );
        }
    }

    /**
     * POST /api/auth/refresh - Rafraîchir le token JWT
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        try {
            $authHeader = $request->headers->get('Authorization');
            
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return $this->json(
                    ['error' => 'Token manquant'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $token = substr($authHeader, 7);
            $utilisateur = $this->authenticationService->validateToken($token);

            if (!$utilisateur) {
                return $this->json(
                    ['error' => 'Token invalide'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            if (!$utilisateur->isActive()) {
                return $this->json(
                    ['error' => 'Compte désactivé'],
                    Response::HTTP_FORBIDDEN
                );
            }

            return $this->json([
                'success' => true,
                'message' => 'Token rafraîchi',
                'user' => [
                    'id' => $utilisateur->getId(),
                    'email' => $utilisateur->getEmail(),
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                    'roles' => $utilisateur->getRoles()
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * GET /api/auth/me - Récupère les informations de l'utilisateur connecté
     * 
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        try {
            $authHeader = $request->headers->get('Authorization');
            
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return $this->json(
                    ['error' => 'Token manquant'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $token = substr($authHeader, 7);
            $utilisateur = $this->authenticationService->validateToken($token);

            if (!$utilisateur) {
                return $this->json(
                    ['error' => 'Token invalide'],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return $this->json([
                'success' => true,
                'data' => [
                    'id' => $utilisateur->getId(),
                    'email' => $utilisateur->getEmail(),
                    'nom' => $utilisateur->getNom(),
                    'prenom' => $utilisateur->getPrenom(),
                    'phone' => $utilisateur->getPhone(),
                    'roles' => $utilisateur->getRoles(),
                    'isActive' => $utilisateur->isActive(),
                    'createdAt' => $utilisateur->getCreatedAt(),
                    'updatedAt' => $utilisateur->getUpdatedAt()
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
