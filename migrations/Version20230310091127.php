<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310091127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prescription (id INT AUTO_INCREMENT NOT NULL, consultation_id INT DEFAULT NULL, lab_id INT DEFAULT NULL, patient_id INT DEFAULT NULL, user_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, descriptions LONGTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, is_published TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_1FBFB8D962FF6CDF (consultation_id), UNIQUE INDEX UNIQ_1FBFB8D9628913D5 (lab_id), INDEX IDX_1FBFB8D96B899279 (patient_id), INDEX IDX_1FBFB8D9A76ED395 (user_id), INDEX IDX_1FBFB8D963DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D962FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9628913D5 FOREIGN KEY (lab_id) REFERENCES lab (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D963DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D962FF6CDF');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D9628913D5');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D96B899279');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D9A76ED395');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D963DBB69');
        $this->addSql('DROP TABLE prescription');
    }
}
