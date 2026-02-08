<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/utilisateurs', name: 'api_utilisateurs_')]
class UtilisateurController extends AbstractController
{
    public function __construct(
        private UtilisateurRepository $utilisateurRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * GET /api/utilisateurs - Récupère tous les utilisateurs (admin only)
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $this->utilisateurRepository->createQueryBuilder('u');

        $total = count($query->getQuery()->getResult());
        $utilisateurs = $query
            ->orderBy('u.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->json([
            'success' => true,
            'data' => $utilisateurs,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/utilisateurs/{id} - Récupère un utilisateur par ID
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => [
                'id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'phone' => $utilisateur->getPhone(),
                'isActive' => $utilisateur->isActive(),
                'createdAt' => $utilisateur->getCreatedAt(),
                'updatedAt' => $utilisateur->getUpdatedAt()
            ]
        ]);
    }

    /**
     * POST /api/utilisateurs - Crée un nouvel utilisateur (inscription)
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        // Validation des champs requis
        $required = ['email', 'password', 'nom', 'prenom'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return $this->json(
                    ['error' => "Le champ '$field' est requis"],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->utilisateurRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(
                ['error' => 'Cet email est déjà utilisé'],
                Response::HTTP_CONFLICT
            );
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($data['email']);
        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $utilisateur->setPhone($data['phone'] ?? null);
        $utilisateur->setIsActive(true);
        $utilisateur->setCreatedAt(new \DateTimeImmutable());
        $utilisateur->setUpdatedAt(new \DateTimeImmutable());

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $data['password']);
        $utilisateur->setPassword($hashedPassword);

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'data' => [
                'id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom()
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * PUT /api/utilisateurs/{id} - Met à jour un utilisateur
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Utilisateur $utilisateur, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['email'])) {
            // Vérifier que le nouvel email n'existe pas
            $existingUser = $this->utilisateurRepository->findOneBy(['email' => $data['email']]);
            if ($existingUser && $existingUser->getId() !== $utilisateur->getId()) {
                return $this->json(
                    ['error' => 'Cet email est déjà utilisé'],
                    Response::HTTP_CONFLICT
                );
            }
            $utilisateur->setEmail($data['email']);
        }

        if (isset($data['nom'])) {
            $utilisateur->setNom($data['nom']);
        }
        if (isset($data['prenom'])) {
            $utilisateur->setPrenom($data['prenom']);
        }
        if (isset($data['phone'])) {
            $utilisateur->setPhone($data['phone']);
        }
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $data['password']);
            $utilisateur->setPassword($hashedPassword);
        }
        if (isset($data['isActive'])) {
            $utilisateur->setIsActive($data['isActive']);
        }

        $utilisateur->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => [
                'id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'phone' => $utilisateur->getPhone(),
                'isActive' => $utilisateur->isActive()
            ]
        ]);
    }

    /**
     * DELETE /api/utilisateurs/{id} - Supprime un utilisateur
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Utilisateur $utilisateur): JsonResponse
    {
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    /**
     * POST /api/utilisateurs/login - Connexion utilisateur
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(
                ['error' => 'Email et mot de passe requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $utilisateur = $this->utilisateurRepository->findOneBy(['email' => $data['email']]);

        if (!$utilisateur) {
            return $this->json(
                ['error' => 'Email ou mot de passe incorrect'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!$this->passwordHasher->isPasswordValid($utilisateur, $data['password'])) {
            return $this->json(
                ['error' => 'Email ou mot de passe incorrect'],
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
            'message' => 'Connexion réussie',
            'data' => [
                'id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom()
            ]
        ]);
    }

    /**
     * GET /api/utilisateurs/{id}/commandes - Récupère les commandes d'un utilisateur
     */
    #[Route('/{id}/commandes', name: 'commandes', methods: ['GET'])]
    public function commandes(Utilisateur $utilisateur): JsonResponse
    {
        return $this->json([
            'success' => true,
            'data' => $utilisateur->getCommandes() ?? []
        ]);
    }
}
