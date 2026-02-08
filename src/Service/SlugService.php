<?php

namespace App\Service;

/**
 * Service utilitaire pour les slugs et transformations
 */
class SlugService
{
    /**
     * Convertit une chaîne en slug
     * 
     * @param string $text Texte à convertir
     * @return string Slug généré
     */
    public function slugify(string $text): string
    {
        // Remplacer les caractères spéciaux par des tirets
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Translittérer les caractères accentués
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        
        // Supprimer les caractères non alphanumériques
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Remplacer les tirets multiples par un seul
        $text = preg_replace('~-+~', '-', $text);
        
        // Supprimer les tirets au début et à la fin
        $text = trim($text, '-');
        
        return strtolower($text);
    }

    /**
     * Génère un numéro de commande unique
     * 
     * @return string Numéro de commande
     */
    public function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return "CMD-{$date}-{$random}";
    }

    /**
     * Valide une adresse email
     * 
     * @param string $email Email à valider
     * @return bool True si valide
     */
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valide un numéro de téléphone
     * 
     * @param string $phone Téléphone à valider
     * @return bool True si valide
     */
    public function isValidPhone(string $phone): bool
    {
        // Format français: 0X XX XX XX XX
        return preg_match('/^0[1-9](?:[0-9]{8})$/', str_replace(' ', '', $phone)) === 1;
    }

    /**
     * Formate un prix
     * 
     * @param float|string $price Prix à formater
     * @return string Prix formaté
     */
    public function formatPrice($price): string
    {
        return number_format((float)$price, 2, '.', '');
    }

    /**
     * Calcule le prix total avec TVA
     * 
     * @param float|string $price Prix HT
     * @param float $taxRate Taux de TVA (ex: 0.20 pour 20%)
     * @return string Prix TTC
     */
    public function calculatePriceWithTax($price, float $taxRate = 0.20): string
    {
        $total = ((float)$price) * (1 + $taxRate);
        return $this->formatPrice($total);
    }
}
