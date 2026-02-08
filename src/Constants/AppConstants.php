<?php

namespace App\Constants;

/**
 * Constantes de l'application BeelShops
 */
class AppConstants
{
    // Statuts de commande
    public const ORDER_STATUS_PENDING = 'pending';
    public const ORDER_STATUS_CONFIRMED = 'confirmed';
    public const ORDER_STATUS_SHIPPED = 'shipped';
    public const ORDER_STATUS_DELIVERED = 'delivered';
    public const ORDER_STATUS_CANCELLED = 'cancelled';

    public const ORDER_STATUSES = [
        self::ORDER_STATUS_PENDING,
        self::ORDER_STATUS_CONFIRMED,
        self::ORDER_STATUS_SHIPPED,
        self::ORDER_STATUS_DELIVERED,
        self::ORDER_STATUS_CANCELLED,
    ];

    // Matériaux de bijoux
    public const MATERIAL_GOLD = 'or';
    public const MATERIAL_SILVER = 'argent';
    public const MATERIAL_STEEL = 'acier';
    public const MATERIAL_PLATINUM = 'platine';
    public const MATERIAL_BRONZE = 'bronze';

    public const MATERIALS = [
        self::MATERIAL_GOLD,
        self::MATERIAL_SILVER,
        self::MATERIAL_STEEL,
        self::MATERIAL_PLATINUM,
        self::MATERIAL_BRONZE,
    ];

    // Pagination
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_LIMIT = 10;
    public const MAX_LIMIT = 100;

    // Validation
    public const MIN_PASSWORD_LENGTH = 8;
    public const MAX_PRODUCT_NAME_LENGTH = 100;
    public const MAX_CATEGORY_NAME_LENGTH = 100;

    // Messages
    public const MSG_SUCCESS = 'Opération réussie';
    public const MSG_CREATED = 'Ressource créée avec succès';
    public const MSG_UPDATED = 'Ressource mise à jour avec succès';
    public const MSG_DELETED = 'Ressource supprimée avec succès';
    public const MSG_ERROR = 'Une erreur est survenue';
    public const MSG_NOT_FOUND = 'Ressource non trouvée';
    public const MSG_INVALID_DATA = 'Données invalides';
    public const MSG_UNAUTHORIZED = 'Non autorisé';
    public const MSG_FORBIDDEN = 'Accès refusé';
}
