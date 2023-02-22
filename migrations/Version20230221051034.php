<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221051034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acts_invoice_basket (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, act_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_18E3BB2E2989F1FD (invoice_id), INDEX IDX_18E3BB2ED1A55B28 (act_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE exams_invoice_basket (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, exam_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_7782193A2989F1FD (invoice_id), INDEX IDX_7782193A578D5E91 (exam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nursing (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_883C8A1462FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nursing_medicines (id INT AUTO_INCREMENT NOT NULL, nursing_id INT NOT NULL, medicine_id INT DEFAULT NULL, quantity INT DEFAULT NULL, INDEX IDX_7166EE45936DC252 (nursing_id), INDEX IDX_7166EE452F7D140A (medicine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nursing_treatment (id INT AUTO_INCREMENT NOT NULL, nursing_id INT NOT NULL, treatment_id INT NOT NULL, INDEX IDX_6138D4C8936DC252 (nursing_id), INDEX IDX_6138D4C8471C0366 (treatment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment_invoice_basket (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, treatment_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_63312602989F1FD (invoice_id), INDEX IDX_6331260471C0366 (treatment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acts_invoice_basket ADD CONSTRAINT FK_18E3BB2E2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE acts_invoice_basket ADD CONSTRAINT FK_18E3BB2ED1A55B28 FOREIGN KEY (act_id) REFERENCES act (id)');
        $this->addSql('ALTER TABLE exams_invoice_basket ADD CONSTRAINT FK_7782193A2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE exams_invoice_basket ADD CONSTRAINT FK_7782193A578D5E91 FOREIGN KEY (exam_id) REFERENCES exam (id)');
        $this->addSql('ALTER TABLE nursing ADD CONSTRAINT FK_883C8A1462FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE nursing_medicines ADD CONSTRAINT FK_7166EE45936DC252 FOREIGN KEY (nursing_id) REFERENCES nursing_treatment (id)');
        $this->addSql('ALTER TABLE nursing_medicines ADD CONSTRAINT FK_7166EE452F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id)');
        $this->addSql('ALTER TABLE nursing_treatment ADD CONSTRAINT FK_6138D4C8936DC252 FOREIGN KEY (nursing_id) REFERENCES nursing (id)');
        $this->addSql('ALTER TABLE nursing_treatment ADD CONSTRAINT FK_6138D4C8471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id)');
        $this->addSql('ALTER TABLE treatment_invoice_basket ADD CONSTRAINT FK_63312602989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE treatment_invoice_basket ADD CONSTRAINT FK_6331260471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id)');
        $this->addSql('ALTER TABLE appointment ADD description LONGTEXT DEFAULT NULL, ADD reason VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_90651744A76ED395 ON invoice (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acts_invoice_basket DROP FOREIGN KEY FK_18E3BB2E2989F1FD');
        $this->addSql('ALTER TABLE acts_invoice_basket DROP FOREIGN KEY FK_18E3BB2ED1A55B28');
        $this->addSql('ALTER TABLE exams_invoice_basket DROP FOREIGN KEY FK_7782193A2989F1FD');
        $this->addSql('ALTER TABLE exams_invoice_basket DROP FOREIGN KEY FK_7782193A578D5E91');
        $this->addSql('ALTER TABLE nursing DROP FOREIGN KEY FK_883C8A1462FF6CDF');
        $this->addSql('ALTER TABLE nursing_medicines DROP FOREIGN KEY FK_7166EE45936DC252');
        $this->addSql('ALTER TABLE nursing_medicines DROP FOREIGN KEY FK_7166EE452F7D140A');
        $this->addSql('ALTER TABLE nursing_treatment DROP FOREIGN KEY FK_6138D4C8936DC252');
        $this->addSql('ALTER TABLE nursing_treatment DROP FOREIGN KEY FK_6138D4C8471C0366');
        $this->addSql('ALTER TABLE treatment_invoice_basket DROP FOREIGN KEY FK_63312602989F1FD');
        $this->addSql('ALTER TABLE treatment_invoice_basket DROP FOREIGN KEY FK_6331260471C0366');
        $this->addSql('DROP TABLE acts_invoice_basket');
        $this->addSql('DROP TABLE exams_invoice_basket');
        $this->addSql('DROP TABLE nursing');
        $this->addSql('DROP TABLE nursing_medicines');
        $this->addSql('DROP TABLE nursing_treatment');
        $this->addSql('DROP TABLE treatment_invoice_basket');
        $this->addSql('ALTER TABLE appointment DROP description, DROP reason');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A76ED395');
        $this->addSql('DROP INDEX IDX_90651744A76ED395 ON invoice');
        $this->addSql('ALTER TABLE invoice DROP user_id');
    }
}
