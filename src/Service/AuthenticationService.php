<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationService
{
    public function __construct(
        private UtilisateurRepository $utilisateurRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    /**
     * Authentifie un utilisateur et retourne un token JWT
     * 
     * @param string $email
     * @param string $password
     * @return array
     * @throws \Exception
     */
    public function authenticate(string $email, string $password): array
    {
        // Valider les paramètres
        if (empty($email) || empty($password)) {
            throw new \Exception('Email et mot de passe requis', 400);
        }

        // Chercher l'utilisateur
        $utilisateur = $this->utilisateurRepository->findOneBy(['email' => $email]);
        
        if (!$utilisateur) {
            throw new \Exception('Email ou mot de passe incorrect', 401);
        }

        // Vérifier le mot de passe
        if (!$this->passwordHasher->isPasswordValid($utilisateur, $password)) {
            throw new \Exception('Email ou mot de passe incorrect', 401);
        }

        // Vérifier que le compte est actif
        if (!$utilisateur->isActive()) {
            throw new \Exception('Compte désactivé', 403);
        }

        // Générer le token JWT
        $token = $this->jwtManager->create($utilisateur);

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'roles' => $utilisateur->getRoles(),
                'isActive' => $utilisateur->isActive()
            ]
        ];
    }

    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $data
     * @return Utilisateur
     * @throws \Exception
     */
    public function register(array $data): Utilisateur
    {
        // Valider les champs requis
        $required = ['email', 'password', 'nom', 'prenom'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \Exception("Le champ '$field' est requis", 400);
            }
        }

        // Vérifier que l'email n'existe pas
        $existingUser = $this->utilisateurRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            throw new \Exception('Cet email est déjà utilisé', 409);
        }

        // Créer l'utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($data['email']);
        $utilisateur->setNom($data['nom']);
        $utilisateur->setPrenom($data['prenom']);
        $utilisateur->setPhone($data['phone'] ?? null);
        $utilisateur->setIsActive(true);
        $utilisateur->setCreatedAt(new \DateTimeImmutable());
        $utilisateur->setUpdatedAt(new \DateTimeImmutable());
        
        // Définir le rôle (par défaut CLIENT)
        $role = $data['role'] ?? 'ROLE_CLIENT';
        if (!in_array($role, ['ROLE_CLIENT', 'ROLE_ADMIN'])) {
            $role = 'ROLE_CLIENT';
        }
        $utilisateur->setRoles([$role]);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $data['password']);
        $utilisateur->setPassword($hashedPassword);

        return $utilisateur;
    }

    /**
     * Valide un token JWT et retourne l'utilisateur
     * 
     * @param string $token
     * @return Utilisateur|null
     */
    public function validateToken(string $token): ?Utilisateur
    {
        try {
            $data = $this->jwtManager->parse($token);
            $userId = $data['id'] ?? null;
            
            if (!$userId) {
                return null;
            }

            return $this->utilisateurRepository->find($userId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
