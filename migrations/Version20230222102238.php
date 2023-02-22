<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222102238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospitalization ADD hospital_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hospitalization ADD CONSTRAINT FK_40CF089163DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('CREATE INDEX IDX_40CF089163DBB69 ON hospitalization (hospital_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospitalization DROP FOREIGN KEY FK_40CF089163DBB69');
        $this->addSql('DROP INDEX IDX_40CF089163DBB69 ON hospitalization');
        $this->addSql('ALTER TABLE hospitalization DROP hospital_id');
    }
}
