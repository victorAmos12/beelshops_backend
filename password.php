<?php

// Mot de passe en clair (exemple)
$plainPassword = "MonMotDePasse123";

// Génération du hash bcrypt
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

// Affichage du hash
echo $hashedPassword;
