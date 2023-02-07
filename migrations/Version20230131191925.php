<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131191925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE act_category (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_30337BDC63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE act_category ADD CONSTRAINT FK_30337BDC63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE act ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE act ADD CONSTRAINT FK_AFECF54412469DE2 FOREIGN KEY (category_id) REFERENCES act_category (id)');
        $this->addSql('CREATE INDEX IDX_AFECF54412469DE2 ON act (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE act DROP FOREIGN KEY FK_AFECF54412469DE2');
        $this->addSql('ALTER TABLE act_category DROP FOREIGN KEY FK_30337BDC63DBB69');
        $this->addSql('DROP TABLE act_category');
        $this->addSql('DROP INDEX IDX_AFECF54412469DE2 ON act');
        $this->addSql('ALTER TABLE act DROP category_id');
    }
}
