<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312112906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nursing_medicines DROP FOREIGN KEY FK_7166EE45936DC252');
        $this->addSql('DROP TABLE nursing_medicines');
        $this->addSql('ALTER TABLE nursing ADD is_published TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nursing_medicines (id INT AUTO_INCREMENT NOT NULL, nursing_id INT NOT NULL, quantity INT DEFAULT NULL, INDEX IDX_7166EE45936DC252 (nursing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE nursing_medicines ADD CONSTRAINT FK_7166EE45936DC252 FOREIGN KEY (nursing_id) REFERENCES nursing_treatment (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE nursing DROP is_published');
    }
}
