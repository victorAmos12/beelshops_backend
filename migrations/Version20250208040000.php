<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter la gestion des rôles aux utilisateurs
 */
final class Version20250208040000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles column to utilisateur table for role-based access control';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('UPDATE utilisateur SET roles = \'["ROLE_CLIENT"]\' WHERE roles IS NULL');
        $this->addSql('ALTER TABLE utilisateur ADD UNIQUE KEY uk_email (email(100))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateur DROP KEY uk_email');
        $this->addSql('ALTER TABLE utilisateur DROP COLUMN roles');
    }
}
