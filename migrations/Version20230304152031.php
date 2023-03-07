<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230304152031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exams_invoice_basket CHANGE invoice_id invoice_id INT DEFAULT NULL, CHANGE exam_id exam_id INT DEFAULT NULL, CHANGE price price NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing ADD patient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing ADD CONSTRAINT FK_883C8A146B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('CREATE INDEX IDX_883C8A146B899279 ON nursing (patient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exams_invoice_basket CHANGE invoice_id invoice_id INT NOT NULL, CHANGE exam_id exam_id INT NOT NULL, CHANGE price price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE nursing DROP FOREIGN KEY FK_883C8A146B899279');
        $this->addSql('DROP INDEX IDX_883C8A146B899279 ON nursing');
        $this->addSql('ALTER TABLE nursing DROP patient_id');
    }
}
