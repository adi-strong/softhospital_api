<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230110181849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hospital (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, denomination VARCHAR(255) NOT NULL, unit_name VARCHAR(20) DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4282C85BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD hospital_center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64994CF6872 FOREIGN KEY (hospital_center_id) REFERENCES hospital (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64994CF6872 ON user (hospital_center_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64994CF6872');
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85BA76ED395');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP INDEX IDX_8D93D64994CF6872 ON user');
        $this->addSql('ALTER TABLE user DROP hospital_center_id');
    }
}
