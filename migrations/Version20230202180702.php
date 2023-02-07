<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202180702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exam_category (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_452856F263DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exam_category ADD CONSTRAINT FK_452856F263DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE exam ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C612469DE2 FOREIGN KEY (category_id) REFERENCES exam_category (id)');
        $this->addSql('CREATE INDEX IDX_38BBA6C612469DE2 ON exam (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C612469DE2');
        $this->addSql('ALTER TABLE exam_category DROP FOREIGN KEY FK_452856F263DBB69');
        $this->addSql('DROP TABLE exam_category');
        $this->addSql('DROP INDEX IDX_38BBA6C612469DE2 ON exam');
        $this->addSql('ALTER TABLE exam DROP category_id');
    }
}
