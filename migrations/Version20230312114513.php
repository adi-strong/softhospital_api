<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312114513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nursing ADD hospital_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing ADD CONSTRAINT FK_883C8A1463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('CREATE INDEX IDX_883C8A1463DBB69 ON nursing (hospital_id)');
        $this->addSql('ALTER TABLE nursing_treatment ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing_treatment ADD CONSTRAINT FK_6138D4C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6138D4C8A76ED395 ON nursing_treatment (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nursing DROP FOREIGN KEY FK_883C8A1463DBB69');
        $this->addSql('DROP INDEX IDX_883C8A1463DBB69 ON nursing');
        $this->addSql('ALTER TABLE nursing DROP hospital_id');
        $this->addSql('ALTER TABLE nursing_treatment DROP FOREIGN KEY FK_6138D4C8A76ED395');
        $this->addSql('DROP INDEX IDX_6138D4C8A76ED395 ON nursing_treatment');
        $this->addSql('ALTER TABLE nursing_treatment DROP user_id');
    }
}
