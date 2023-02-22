<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221051954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nursing_treatment_medicine (nursing_treatment_id INT NOT NULL, medicine_id INT NOT NULL, INDEX IDX_AF9A819134ABBA2C (nursing_treatment_id), INDEX IDX_AF9A81912F7D140A (medicine_id), PRIMARY KEY(nursing_treatment_id, medicine_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nursing_treatment_medicine ADD CONSTRAINT FK_AF9A819134ABBA2C FOREIGN KEY (nursing_treatment_id) REFERENCES nursing_treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE nursing_treatment_medicine ADD CONSTRAINT FK_AF9A81912F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nursing_treatment_medicine DROP FOREIGN KEY FK_AF9A819134ABBA2C');
        $this->addSql('ALTER TABLE nursing_treatment_medicine DROP FOREIGN KEY FK_AF9A81912F7D140A');
        $this->addSql('DROP TABLE nursing_treatment_medicine');
    }
}
