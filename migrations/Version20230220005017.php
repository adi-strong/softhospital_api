<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220005017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, file_id INT NOT NULL, hospital_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_964685A66B899279 (patient_id), INDEX IDX_964685A693CB796C (file_id), INDEX IDX_964685A663DBB69 (hospital_id), INDEX IDX_964685A6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation_act (consultation_id INT NOT NULL, act_id INT NOT NULL, INDEX IDX_D236ADFE62FF6CDF (consultation_id), INDEX IDX_D236ADFED1A55B28 (act_id), PRIMARY KEY(consultation_id, act_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation_exam (consultation_id INT NOT NULL, exam_id INT NOT NULL, INDEX IDX_1372260C62FF6CDF (consultation_id), INDEX IDX_1372260C578D5E91 (exam_id), PRIMARY KEY(consultation_id, exam_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation_treatment (consultation_id INT NOT NULL, treatment_id INT NOT NULL, INDEX IDX_3EF32AD662FF6CDF (consultation_id), INDEX IDX_3EF32AD6471C0366 (treatment_id), PRIMARY KEY(consultation_id, treatment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, patient_id INT DEFAULT NULL, consultation_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, total_amount NUMERIC(10, 2) DEFAULT NULL, paid NUMERIC(10, 2) DEFAULT NULL, leftover NUMERIC(10, 2) DEFAULT NULL, is_complete TINYINT(1) NOT NULL, released_at DATETIME DEFAULT NULL, INDEX IDX_9065174463DBB69 (hospital_id), INDEX IDX_906517446B899279 (patient_id), UNIQUE INDEX UNIQ_9065174462FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A66B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A693CB796C FOREIGN KEY (file_id) REFERENCES consultations_type (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A663DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consultation_act ADD CONSTRAINT FK_D236ADFE62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_act ADD CONSTRAINT FK_D236ADFED1A55B28 FOREIGN KEY (act_id) REFERENCES act (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_exam ADD CONSTRAINT FK_1372260C62FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_exam ADD CONSTRAINT FK_1372260C578D5E91 FOREIGN KEY (exam_id) REFERENCES exam (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_treatment ADD CONSTRAINT FK_3EF32AD662FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_treatment ADD CONSTRAINT FK_3EF32AD6471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517446B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174462FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A66B899279');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A693CB796C');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A663DBB69');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6A76ED395');
        $this->addSql('ALTER TABLE consultation_act DROP FOREIGN KEY FK_D236ADFE62FF6CDF');
        $this->addSql('ALTER TABLE consultation_act DROP FOREIGN KEY FK_D236ADFED1A55B28');
        $this->addSql('ALTER TABLE consultation_exam DROP FOREIGN KEY FK_1372260C62FF6CDF');
        $this->addSql('ALTER TABLE consultation_exam DROP FOREIGN KEY FK_1372260C578D5E91');
        $this->addSql('ALTER TABLE consultation_treatment DROP FOREIGN KEY FK_3EF32AD662FF6CDF');
        $this->addSql('ALTER TABLE consultation_treatment DROP FOREIGN KEY FK_3EF32AD6471C0366');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174463DBB69');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517446B899279');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174462FF6CDF');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE consultation_act');
        $this->addSql('DROP TABLE consultation_exam');
        $this->addSql('DROP TABLE consultation_treatment');
        $this->addSql('DROP TABLE invoice');
    }
}
