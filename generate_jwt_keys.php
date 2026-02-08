<?php
/**
 * Script de génération des clés JWT
 * Exécutez: php generate_jwt_keys.php
 */

$jwtDir = __DIR__ . '/config/jwt';

// Créer le répertoire s'il n'existe pas
if (!is_dir($jwtDir)) {
    mkdir($jwtDir, 0755, true);
    echo "✓ Répertoire créé: $jwtDir\n";
}

$privateKeyPath = $jwtDir . '/private.pem';
$publicKeyPath = $jwtDir . '/public.pem';

// Vérifier si les clés existent déjà
if (file_exists($privateKeyPath) && file_exists($publicKeyPath)) {
    echo "✓ Les clés JWT existent déjà.\n";
    exit(0);
}

// Configuration pour la génération des clés
$config = [
    'private_key_bits' => 4096,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    
];

echo "Génération des clés JWT...\n";

// Générer la paire de clés
$res = openssl_pkey_new($config);

if ($res === false) {
    echo "✗ Erreur: Impossible de générer la paire de clés.\n";
    echo "Erreur OpenSSL: " . openssl_error_string() . "\n";
    exit(1);
}

// Extraire la clé privée
openssl_pkey_export($res, $privateKey, 'beelshop_jwt_secret_key_2025');

if ($privateKey === false) {
    echo "✗ Erreur: Impossible d'exporter la clé privée.\n";
    echo "Erreur OpenSSL: " . openssl_error_string() . "\n";
    exit(1);
}

// Extraire la clé publique
$publicKeyDetails = openssl_pkey_get_details($res);
$publicKey = $publicKeyDetails['key'];

if ($publicKey === false) {
    echo "✗ Erreur: Impossible d'extraire la clé publique.\n";
    echo "Erreur OpenSSL: " . openssl_error_string() . "\n";
    exit(1);
}

// Écrire la clé privée
if (file_put_contents($privateKeyPath, $privateKey) === false) {
    echo "✗ Erreur: Impossible d'écrire la clé privée.\n";
    exit(1);
}
chmod($privateKeyPath, 0600);
echo "✓ Clé privée créée: $privateKeyPath\n";

// Écrire la clé publique
if (file_put_contents($publicKeyPath, $publicKey) === false) {
    echo "✗ Erreur: Impossible d'écrire la clé publique.\n";
    exit(1);
}
chmod($publicKeyPath, 0644);
echo "✓ Clé publique créée: $publicKeyPath\n";

echo "\n✓ Les clés JWT ont été générées avec succès!\n";
echo "Vous pouvez maintenant utiliser l'authentification JWT.\n";
?>
