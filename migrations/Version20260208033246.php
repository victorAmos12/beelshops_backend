<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208033246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_3B0252169AF8E3A3 ON article_commande');
        $this->addSql('ALTER TABLE article_commande ADD produit_id INT NOT NULL, CHANGE quantite quantite INT NOT NULL, CHANGE prix_unitaire prix_unitaire NUMERIC(10, 2) NOT NULL, CHANGE prix_total prix_total NUMERIC(10, 2) NOT NULL, CHANGE id_commande_id commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE article_commande ADD CONSTRAINT FK_3B02521682EA2E54 FOREIGN KEY (commande_id) REFERENCES commandes (id)');
        $this->addSql('ALTER TABLE article_commande ADD CONSTRAINT FK_3B025216F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_3B02521682EA2E54 ON article_commande (commande_id)');
        $this->addSql('CREATE INDEX IDX_3B025216F347EFB ON article_commande (produit_id)');
        $this->addSql('ALTER TABLE avis_client CHANGE rating rating INT NOT NULL');
        $this->addSql('ALTER TABLE avis_client ADD CONSTRAINT FK_708E90EFAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE avis_client ADD CONSTRAINT FK_708E90EFC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('DROP INDEX IDX_35D4282CB981C689 ON commandes');
        $this->addSql('ALTER TABLE commandes CHANGE prix_total prix_total NUMERIC(10, 2) NOT NULL, CHANGE addresse_appartement adresse_appartement VARCHAR(50) DEFAULT NULL, CHANGE update_at updated_at DATETIME NOT NULL, CHANGE utilisateur_id_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_35D4282CFB88E14F ON commandes (utilisateur_id)');
        $this->addSql('ALTER TABLE liste_souhaits ADD CONSTRAINT FK_92B072AFC6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE liste_souhaits ADD CONSTRAINT FK_92B072AFAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('DROP INDEX IDX_24CC0DF2C6EE5C49 ON panier');
        $this->addSql('ALTER TABLE panier CHANGE id_utilisateur_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF2FB88E14F ON panier (utilisateur_id)');
        $this->addSql('ALTER TABLE panier_articles CHANGE quantite quantite INT NOT NULL');
        $this->addSql('ALTER TABLE panier_articles ADD CONSTRAINT FK_2598381A5669B1EA FOREIGN KEY (panier_id_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panier_articles ADD CONSTRAINT FK_2598381A4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('DROP INDEX IDX_29A5EC2718694433 ON produit');
        $this->addSql('ALTER TABLE produit DROP article_commande_id, CHANGE prix prix NUMERIC(10, 2) NOT NULL, CHANGE stock stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES categorie (id)');
        $this->addSql('DROP INDEX IDX_174D75504FD8F9C3 ON produit_images');
        $this->addSql('ALTER TABLE produit_images CHANGE produit_id_id produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit_images ADD CONSTRAINT FK_174D7550F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_174D7550F347EFB ON produit_images (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article_commande DROP FOREIGN KEY FK_3B02521682EA2E54');
        $this->addSql('ALTER TABLE article_commande DROP FOREIGN KEY FK_3B025216F347EFB');
        $this->addSql('DROP INDEX IDX_3B02521682EA2E54 ON article_commande');
        $this->addSql('DROP INDEX IDX_3B025216F347EFB ON article_commande');
        $this->addSql('ALTER TABLE article_commande ADD id_commande_id INT NOT NULL, DROP commande_id, DROP produit_id, CHANGE quantite quantite VARCHAR(5) NOT NULL, CHANGE prix_unitaire prix_unitaire VARCHAR(100) NOT NULL, CHANGE prix_total prix_total VARCHAR(100) NOT NULL');
        $this->addSql('CREATE INDEX IDX_3B0252169AF8E3A3 ON article_commande (id_commande_id)');
        $this->addSql('ALTER TABLE avis_client DROP FOREIGN KEY FK_708E90EFAABEFE2C');
        $this->addSql('ALTER TABLE avis_client DROP FOREIGN KEY FK_708E90EFC6EE5C49');
        $this->addSql('ALTER TABLE avis_client CHANGE rating rating VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CFB88E14F');
        $this->addSql('DROP INDEX IDX_35D4282CFB88E14F ON commandes');
        $this->addSql('ALTER TABLE commandes CHANGE prix_total prix_total VARCHAR(100) NOT NULL, CHANGE adresse_appartement addresse_appartement VARCHAR(50) DEFAULT NULL, CHANGE updated_at update_at DATETIME NOT NULL, CHANGE utilisateur_id utilisateur_id_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_35D4282CB981C689 ON commandes (utilisateur_id_id)');
        $this->addSql('ALTER TABLE liste_souhaits DROP FOREIGN KEY FK_92B072AFC6EE5C49');
        $this->addSql('ALTER TABLE liste_souhaits DROP FOREIGN KEY FK_92B072AFAABEFE2C');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2FB88E14F');
        $this->addSql('DROP INDEX UNIQ_24CC0DF2FB88E14F ON panier');
        $this->addSql('ALTER TABLE panier CHANGE utilisateur_id id_utilisateur_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_24CC0DF2C6EE5C49 ON panier (id_utilisateur_id)');
        $this->addSql('ALTER TABLE panier_articles DROP FOREIGN KEY FK_2598381A5669B1EA');
        $this->addSql('ALTER TABLE panier_articles DROP FOREIGN KEY FK_2598381A4FD8F9C3');
        $this->addSql('ALTER TABLE panier_articles CHANGE quantite quantite VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE produit ADD article_commande_id INT NOT NULL, CHANGE prix prix VARCHAR(10) NOT NULL, CHANGE stock stock VARCHAR(100) DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_29A5EC2718694433 ON produit (article_commande_id)');
        $this->addSql('ALTER TABLE produit_images DROP FOREIGN KEY FK_174D7550F347EFB');
        $this->addSql('DROP INDEX IDX_174D7550F347EFB ON produit_images');
        $this->addSql('ALTER TABLE produit_images CHANGE produit_id produit_id_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_174D75504FD8F9C3 ON produit_images (produit_id_id)');
    }
}
