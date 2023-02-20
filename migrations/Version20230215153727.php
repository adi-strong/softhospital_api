<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215153727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, user_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, focal VARCHAR(255) NOT NULL, tel VARCHAR(20) NOT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_92C4739C63DBB69 (hospital_id), INDEX IDX_92C4739CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739C63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medicine ADD nb_sales INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE provider DROP FOREIGN KEY FK_92C4739C63DBB69');
        $this->addSql('ALTER TABLE provider DROP FOREIGN KEY FK_92C4739CA76ED395');
        $this->addSql('DROP TABLE provider');
        $this->addSql('ALTER TABLE medicine DROP nb_sales');
    }
}
