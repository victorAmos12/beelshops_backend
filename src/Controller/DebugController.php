<?php
/**
 * Script de diagnostic pour l'authentification JWT
 * Exécutez: php bin/console debug:config lexik_jwt_authentication
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/debug', name: 'api_debug_')]
class DebugController extends AbstractController
{
    /**
     * GET /api/debug/health - Vérifier la santé de l'API
     */
    #[Route('/health', name: 'health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => $_ENV['APP_ENV'] ?? 'unknown',
            'php_version' => phpversion(),
            'extensions' => [
                'openssl' => extension_loaded('openssl'),
                'json' => extension_loaded('json'),
                'pdo' => extension_loaded('pdo'),
            ]
        ]);
    }

    /**
     * GET /api/debug/jwt - Vérifier la configuration JWT
     */
    #[Route('/jwt', name: 'jwt', methods: ['GET'])]
    public function jwt(): JsonResponse
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $privateKeyPath = $projectDir . '/config/jwt/private.pem';
        $publicKeyPath = $projectDir . '/config/jwt/public.pem';

        return $this->json([
            'jwt_config' => [
                'private_key_exists' => file_exists($privateKeyPath),
                'public_key_exists' => file_exists($publicKeyPath),
                'private_key_readable' => is_readable($privateKeyPath),
                'public_key_readable' => is_readable($publicKeyPath),
                'private_key_path' => $privateKeyPath,
                'public_key_path' => $publicKeyPath,
            ],
            'environment_vars' => [
                'JWT_SECRET_KEY' => $_ENV['JWT_SECRET_KEY'] ?? 'NOT SET',
                'JWT_PUBLIC_KEY' => $_ENV['JWT_PUBLIC_KEY'] ?? 'NOT SET',
                'JWT_PASSPHRASE' => isset($_ENV['JWT_PASSPHRASE']) ? '***' : 'NOT SET',
            ]
        ]);
    }

    /**
     * GET /api/debug/cors - Vérifier la configuration CORS
     */
    #[Route('/cors', name: 'cors', methods: ['GET', 'OPTIONS'])]
    public function cors(): JsonResponse
    {
        return $this->json([
            'cors_config' => [
                'allow_origin' => $_ENV['CORS_ALLOW_ORIGIN'] ?? 'NOT SET',
                'request_origin' => $_SERVER['HTTP_ORIGIN'] ?? 'NOT SET',
            ]
        ]);
    }
}
