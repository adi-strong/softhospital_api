<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204023115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bedroom (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, number VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_E615435112469DE2 (category_id), INDEX IDX_E615435163DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bedroom_category (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_1F4C876C63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bedroom ADD CONSTRAINT FK_E615435112469DE2 FOREIGN KEY (category_id) REFERENCES bedroom_category (id)');
        $this->addSql('ALTER TABLE bedroom ADD CONSTRAINT FK_E615435163DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE bedroom_category ADD CONSTRAINT FK_1F4C876C63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bedroom DROP FOREIGN KEY FK_E615435112469DE2');
        $this->addSql('ALTER TABLE bedroom DROP FOREIGN KEY FK_E615435163DBB69');
        $this->addSql('ALTER TABLE bedroom_category DROP FOREIGN KEY FK_1F4C876C63DBB69');
        $this->addSql('DROP TABLE bedroom');
        $this->addSql('DROP TABLE bedroom_category');
    }
}
