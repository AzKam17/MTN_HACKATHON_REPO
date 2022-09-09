<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220909124322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE t_transactions ADD state VARCHAR(255) NOT NULL, ADD montant DOUBLE PRECISION NOT NULL, ADD type VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA32C390F037AB0F ON t_users (tel)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA32C390989D9B62 ON t_users (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP INDEX UNIQ_AA32C390F037AB0F ON t_users');
        $this->addSql('DROP INDEX UNIQ_AA32C390989D9B62 ON t_users');
        $this->addSql('ALTER TABLE t_transactions DROP state, DROP montant, DROP type');
    }
}
