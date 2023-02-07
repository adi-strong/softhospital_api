<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203120620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE treatment_category (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_94E6519863DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE treatment_category ADD CONSTRAINT FK_94E6519863DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE treatment ADD category_id INT DEFAULT NULL, ADD price NUMERIC(10, 2) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD is_deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C3112469DE2 FOREIGN KEY (category_id) REFERENCES treatment_category (id)');
        $this->addSql('CREATE INDEX IDX_98013C3112469DE2 ON treatment (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C3112469DE2');
        $this->addSql('ALTER TABLE treatment_category DROP FOREIGN KEY FK_94E6519863DBB69');
        $this->addSql('DROP TABLE treatment_category');
        $this->addSql('DROP INDEX IDX_98013C3112469DE2 ON treatment');
        $this->addSql('ALTER TABLE treatment DROP category_id, DROP price, DROP created_at, DROP is_deleted');
    }
}
