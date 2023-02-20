<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220125212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, doctor_id INT NOT NULL, consultation_id INT DEFAULT NULL, patient_id INT NOT NULL, hospital_id INT DEFAULT NULL, user_id INT DEFAULT NULL, is_complete TINYINT(1) NOT NULL, appointment_date DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_FE38F84487F4FB17 (doctor_id), UNIQUE INDEX UNIQ_FE38F84462FF6CDF (consultation_id), INDEX IDX_FE38F8446B899279 (patient_id), INDEX IDX_FE38F84463DBB69 (hospital_id), INDEX IDX_FE38F844A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84487F4FB17 FOREIGN KEY (doctor_id) REFERENCES agent (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84462FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8446B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consultation ADD doctor_id INT NOT NULL, ADD is_complete TINYINT(1) NOT NULL, ADD temperature NUMERIC(3, 0) DEFAULT NULL, ADD weight NUMERIC(3, 2) DEFAULT NULL, ADD arterial_tension VARCHAR(20) DEFAULT NULL, ADD cardiac_frequency VARCHAR(20) DEFAULT NULL, ADD respiratory_frequency VARCHAR(20) DEFAULT NULL, ADD oxygen_saturation VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A687F4FB17 FOREIGN KEY (doctor_id) REFERENCES agent (id)');
        $this->addSql('CREATE INDEX IDX_964685A687F4FB17 ON consultation (doctor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84487F4FB17');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84462FF6CDF');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8446B899279');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84463DBB69');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A76ED395');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A687F4FB17');
        $this->addSql('DROP INDEX IDX_964685A687F4FB17 ON consultation');
        $this->addSql('ALTER TABLE consultation DROP doctor_id, DROP is_complete, DROP temperature, DROP weight, DROP arterial_tension, DROP cardiac_frequency, DROP respiratory_frequency, DROP oxygen_saturation');
    }
}
