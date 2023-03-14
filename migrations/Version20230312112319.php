<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312112319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nursing_medicines DROP FOREIGN KEY FK_7166EE452F7D140A');
        $this->addSql('DROP INDEX IDX_7166EE452F7D140A ON nursing_medicines');
        $this->addSql('ALTER TABLE nursing_medicines DROP medicine_id');
        $this->addSql('ALTER TABLE nursing_treatment ADD treatments JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nursing_medicines ADD medicine_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing_medicines ADD CONSTRAINT FK_7166EE452F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7166EE452F7D140A ON nursing_medicines (medicine_id)');
        $this->addSql('ALTER TABLE nursing_treatment DROP treatments');
    }
}
