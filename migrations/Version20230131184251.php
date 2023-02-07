<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131184251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE act (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_AFECF54463DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exam (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_38BBA6C663DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, INDEX IDX_98013C3163DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE act ADD CONSTRAINT FK_AFECF54463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C663DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C3163DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE act DROP FOREIGN KEY FK_AFECF54463DBB69');
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C663DBB69');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C3163DBB69');
        $this->addSql('DROP TABLE act');
        $this->addSql('DROP TABLE exam');
        $this->addSql('DROP TABLE treatment');
    }
}
