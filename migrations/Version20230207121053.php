<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207121053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pdf_object (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE covenant ADD logo_id INT DEFAULT NULL, ADD file_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE covenant ADD CONSTRAINT FK_F4EB80F7F98F144A FOREIGN KEY (logo_id) REFERENCES image_object (id)');
        $this->addSql('CREATE INDEX IDX_F4EB80F7F98F144A ON covenant (logo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pdf_object');
        $this->addSql('ALTER TABLE covenant DROP FOREIGN KEY FK_F4EB80F7F98F144A');
        $this->addSql('DROP INDEX IDX_F4EB80F7F98F144A ON covenant');
        $this->addSql('ALTER TABLE covenant DROP logo_id, DROP file_path');
    }
}
