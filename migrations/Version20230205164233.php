<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230205164233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bed (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, bedroom_id INT NOT NULL, number VARCHAR(255) NOT NULL, it_has_taken TINYINT(1) DEFAULT NULL, cost NUMERIC(10, 2) NOT NULL, price NUMERIC(10, 2) NOT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_E647FCFF63DBB69 (hospital_id), INDEX IDX_E647FCFFBDB6797C (bedroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bed ADD CONSTRAINT FK_E647FCFF63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE bed ADD CONSTRAINT FK_E647FCFFBDB6797C FOREIGN KEY (bedroom_id) REFERENCES bedroom (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bed DROP FOREIGN KEY FK_E647FCFF63DBB69');
        $this->addSql('ALTER TABLE bed DROP FOREIGN KEY FK_E647FCFFBDB6797C');
        $this->addSql('DROP TABLE bed');
    }
}
