<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220908231307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE t_cotisations (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tontine_id INT NOT NULL, tour INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_954A111DA76ED395 (user_id), INDEX IDX_954A111DDEB5C9FD (tontine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE t_liste_retraits (id INT AUTO_INCREMENT NOT NULL, tontine_id INT NOT NULL, membres LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_F7209B5ADEB5C9FD (tontine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE t_montant_tontines (id INT AUTO_INCREMENT NOT NULL, valeur INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE t_periodicite_tontines (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE t_tontines (id INT AUTO_INCREMENT NOT NULL, montant_id INT NOT NULL, periodicite_id INT NOT NULL, created_by_id INT NOT NULL, nom VARCHAR(255) NOT NULL, solde VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, compteur INT NOT NULL, INDEX IDX_F6AA9B71F8D148 (montant_id), INDEX IDX_F6AA9B72928752A (periodicite_id), INDEX IDX_F6AA9B7B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE t_user_tontines (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tontine_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_removed TINYINT(1) NOT NULL, INDEX IDX_1E1ED42CA76ED395 (user_id), INDEX IDX_1E1ED42CDEB5C9FD (tontine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE t_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, solde DOUBLE PRECISION NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_AA32C390F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE t_cotisations ADD CONSTRAINT FK_954A111DA76ED395 FOREIGN KEY (user_id) REFERENCES t_users (id)');
        $this->addSql('ALTER TABLE t_cotisations ADD CONSTRAINT FK_954A111DDEB5C9FD FOREIGN KEY (tontine_id) REFERENCES t_tontines (id)');
        $this->addSql('ALTER TABLE t_liste_retraits ADD CONSTRAINT FK_F7209B5ADEB5C9FD FOREIGN KEY (tontine_id) REFERENCES t_tontines (id)');
        $this->addSql('ALTER TABLE t_tontines ADD CONSTRAINT FK_F6AA9B71F8D148 FOREIGN KEY (montant_id) REFERENCES t_montant_tontines (id)');
        $this->addSql('ALTER TABLE t_tontines ADD CONSTRAINT FK_F6AA9B72928752A FOREIGN KEY (periodicite_id) REFERENCES t_periodicite_tontines (id)');
        $this->addSql('ALTER TABLE t_tontines ADD CONSTRAINT FK_F6AA9B7B03A8386 FOREIGN KEY (created_by_id) REFERENCES t_users (id)');
        $this->addSql('ALTER TABLE t_user_tontines ADD CONSTRAINT FK_1E1ED42CA76ED395 FOREIGN KEY (user_id) REFERENCES t_users (id)');
        $this->addSql('ALTER TABLE t_user_tontines ADD CONSTRAINT FK_1E1ED42CDEB5C9FD FOREIGN KEY (tontine_id) REFERENCES t_tontines (id)');
        $this->addSql('DROP TABLE user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE t_cotisations DROP FOREIGN KEY FK_954A111DA76ED395');
        $this->addSql('ALTER TABLE t_cotisations DROP FOREIGN KEY FK_954A111DDEB5C9FD');
        $this->addSql('ALTER TABLE t_liste_retraits DROP FOREIGN KEY FK_F7209B5ADEB5C9FD');
        $this->addSql('ALTER TABLE t_tontines DROP FOREIGN KEY FK_F6AA9B71F8D148');
        $this->addSql('ALTER TABLE t_tontines DROP FOREIGN KEY FK_F6AA9B72928752A');
        $this->addSql('ALTER TABLE t_tontines DROP FOREIGN KEY FK_F6AA9B7B03A8386');
        $this->addSql('ALTER TABLE t_user_tontines DROP FOREIGN KEY FK_1E1ED42CA76ED395');
        $this->addSql('ALTER TABLE t_user_tontines DROP FOREIGN KEY FK_1E1ED42CDEB5C9FD');
        $this->addSql('DROP TABLE t_cotisations');
        $this->addSql('DROP TABLE t_liste_retraits');
        $this->addSql('DROP TABLE t_montant_tontines');
        $this->addSql('DROP TABLE t_periodicite_tontines');
        $this->addSql('DROP TABLE t_tontines');
        $this->addSql('DROP TABLE t_user_tontines');
        $this->addSql('DROP TABLE t_users');
    }
}
