<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208032020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_commande (id INT AUTO_INCREMENT NOT NULL, quantite VARCHAR(5) NOT NULL, prix_unitaire VARCHAR(100) NOT NULL, prix_total VARCHAR(100) NOT NULL, id_commande_id INT NOT NULL, INDEX IDX_3B0252169AF8E3A3 (id_commande_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE avis_client (id INT AUTO_INCREMENT NOT NULL, rating VARCHAR(5) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id_produit_id INT DEFAULT NULL, id_utilisateur_id INT DEFAULT NULL, INDEX IDX_708E90EFAABEFE2C (id_produit_id), INDEX IDX_708E90EFC6EE5C49 (id_utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, slug VARCHAR(30) NOT NULL, description VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, num_ordre VARCHAR(100) NOT NULL, prix_total VARCHAR(100) NOT NULL, status VARCHAR(20) NOT NULL, adresse_livraison VARCHAR(255) DEFAULT NULL, addresse_appartement VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, update_at DATETIME NOT NULL, utilisateur_id_id INT NOT NULL, INDEX IDX_35D4282CB981C689 (utilisateur_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE liste_souhaits (id INT AUTO_INCREMENT NOT NULL, added_at DATETIME NOT NULL, id_utilisateur_id INT NOT NULL, id_produit_id INT NOT NULL, INDEX IDX_92B072AFC6EE5C49 (id_utilisateur_id), INDEX IDX_92B072AFAABEFE2C (id_produit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id_utilisateur_id INT NOT NULL, INDEX IDX_24CC0DF2C6EE5C49 (id_utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE panier_articles (id INT AUTO_INCREMENT NOT NULL, quantite VARCHAR(10) NOT NULL, added_at DATETIME NOT NULL, panier_id_id INT NOT NULL, produit_id_id INT NOT NULL, INDEX IDX_2598381A5669B1EA (panier_id_id), INDEX IDX_2598381A4FD8F9C3 (produit_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, slug VARCHAR(30) DEFAULT NULL, description VARCHAR(255) NOT NULL, prix VARCHAR(10) NOT NULL, stock VARCHAR(100) DEFAULT NULL, weight VARCHAR(100) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT NOT NULL, article_commande_id INT NOT NULL, INDEX IDX_29A5EC2712469DE2 (category_id), INDEX IDX_29A5EC2718694433 (article_commande_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE produit_images (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) DEFAULT NULL, alt_text VARCHAR(50) DEFAULT NULL, position INT NOT NULL, produit_id_id INT DEFAULT NULL, INDEX IDX_174D75504FD8F9C3 (produit_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, is_active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE article_commande ADD CONSTRAINT FK_3B0252169AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commandes (id)');
        $this->addSql('ALTER TABLE avis_client ADD CONSTRAINT FK_708E90EFAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE avis_client ADD CONSTRAINT FK_708E90EFC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CB981C689 FOREIGN KEY (utilisateur_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE liste_souhaits ADD CONSTRAINT FK_92B072AFC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE liste_souhaits ADD CONSTRAINT FK_92B072AFAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE panier_articles ADD CONSTRAINT FK_2598381A5669B1EA FOREIGN KEY (panier_id_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panier_articles ADD CONSTRAINT FK_2598381A4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2718694433 FOREIGN KEY (article_commande_id) REFERENCES article_commande (id)');
        $this->addSql('ALTER TABLE produit_images ADD CONSTRAINT FK_174D75504FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_commande DROP FOREIGN KEY FK_3B0252169AF8E3A3');
        $this->addSql('ALTER TABLE avis_client DROP FOREIGN KEY FK_708E90EFAABEFE2C');
        $this->addSql('ALTER TABLE avis_client DROP FOREIGN KEY FK_708E90EFC6EE5C49');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CB981C689');
        $this->addSql('ALTER TABLE liste_souhaits DROP FOREIGN KEY FK_92B072AFC6EE5C49');
        $this->addSql('ALTER TABLE liste_souhaits DROP FOREIGN KEY FK_92B072AFAABEFE2C');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2C6EE5C49');
        $this->addSql('ALTER TABLE panier_articles DROP FOREIGN KEY FK_2598381A5669B1EA');
        $this->addSql('ALTER TABLE panier_articles DROP FOREIGN KEY FK_2598381A4FD8F9C3');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2718694433');
        $this->addSql('ALTER TABLE produit_images DROP FOREIGN KEY FK_174D75504FD8F9C3');
        $this->addSql('DROP TABLE article_commande');
        $this->addSql('DROP TABLE avis_client');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE liste_souhaits');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_articles');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_images');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
