<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222104513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lab (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, user_id INT DEFAULT NULL, assistant_id INT DEFAULT NULL, consultation_id INT NOT NULL, note LONGTEXT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_61D6B1C463DBB69 (hospital_id), INDEX IDX_61D6B1C4A76ED395 (user_id), INDEX IDX_61D6B1C4E05387EF (assistant_id), UNIQUE INDEX UNIQ_61D6B1C462FF6CDF (consultation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lab_result (id INT AUTO_INCREMENT NOT NULL, lab_id INT NOT NULL, exam_id INT NOT NULL, results LONGTEXT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_86B24747628913D5 (lab_id), INDEX IDX_86B24747578D5E91 (exam_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C4E05387EF FOREIGN KEY (assistant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C462FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE lab_result ADD CONSTRAINT FK_86B24747628913D5 FOREIGN KEY (lab_id) REFERENCES lab (id)');
        $this->addSql('ALTER TABLE lab_result ADD CONSTRAINT FK_86B24747578D5E91 FOREIGN KEY (exam_id) REFERENCES exam (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C463DBB69');
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C4A76ED395');
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C4E05387EF');
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C462FF6CDF');
        $this->addSql('ALTER TABLE lab_result DROP FOREIGN KEY FK_86B24747628913D5');
        $this->addSql('ALTER TABLE lab_result DROP FOREIGN KEY FK_86B24747578D5E91');
        $this->addSql('DROP TABLE lab');
        $this->addSql('DROP TABLE lab_result');
    }
}
