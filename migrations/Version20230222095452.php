<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222095452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hospitalization (id INT AUTO_INCREMENT NOT NULL, consultation_id INT NOT NULL, bed_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, released_at DATETIME DEFAULT NULL, leave_at DATETIME DEFAULT NULL, days_counter INT NOT NULL, UNIQUE INDEX UNIQ_40CF089162FF6CDF (consultation_id), INDEX IDX_40CF089188688BB9 (bed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospitalization ADD CONSTRAINT FK_40CF089162FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE hospitalization ADD CONSTRAINT FK_40CF089188688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id)');
        $this->addSql('ALTER TABLE consultation ADD note LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospitalization DROP FOREIGN KEY FK_40CF089162FF6CDF');
        $this->addSql('ALTER TABLE hospitalization DROP FOREIGN KEY FK_40CF089188688BB9');
        $this->addSql('DROP TABLE hospitalization');
        $this->addSql('ALTER TABLE consultation DROP note');
    }
}
