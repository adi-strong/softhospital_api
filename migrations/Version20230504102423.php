<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230504102423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reset_pass_notifier (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, released_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE nursing ADD CONSTRAINT FK_883C8A1462FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE nursing ADD CONSTRAINT FK_883C8A146B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE nursing ADD CONSTRAINT FK_883C8A1463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE nursing_treatment CHANGE medicines medicines JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing_treatment ADD CONSTRAINT FK_6138D4C8936DC252 FOREIGN KEY (nursing_id) REFERENCES nursing (id)');
        $this->addSql('ALTER TABLE nursing_treatment ADD CONSTRAINT FK_6138D4C8471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id)');
        $this->addSql('ALTER TABLE nursing_treatment ADD CONSTRAINT FK_6138D4C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B0263DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
        $this->addSql('ALTER TABLE parameters ADD CONSTRAINT FK_69348FE63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBDA91032A FOREIGN KEY (covenant_id) REFERENCES covenant (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBCCFA12B8 FOREIGN KEY (profile_id) REFERENCES image_object (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE personal_image_object ADD CONSTRAINT FK_E1053F56A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE prescription CHANGE orders orders JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D962FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9628913D5 FOREIGN KEY (lab_id) REFERENCES lab (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D963DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739C63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD263DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C3163DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE treatment ADD CONSTRAINT FK_98013C3112469DE2 FOREIGN KEY (category_id) REFERENCES treatment_category (id)');
        $this->addSql('ALTER TABLE treatment_category ADD CONSTRAINT FK_94E6519863DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64994CF6872 FOREIGN KEY (hospital_center_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CCFA12B8 FOREIGN KEY (profile_id) REFERENCES personal_image_object (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE reset_pass_notifier');
        $this->addSql('ALTER TABLE nursing DROP FOREIGN KEY FK_883C8A1462FF6CDF');
        $this->addSql('ALTER TABLE nursing DROP FOREIGN KEY FK_883C8A146B899279');
        $this->addSql('ALTER TABLE nursing DROP FOREIGN KEY FK_883C8A1463DBB69');
        $this->addSql('ALTER TABLE nursing_treatment DROP FOREIGN KEY FK_6138D4C8936DC252');
        $this->addSql('ALTER TABLE nursing_treatment DROP FOREIGN KEY FK_6138D4C8471C0366');
        $this->addSql('ALTER TABLE nursing_treatment DROP FOREIGN KEY FK_6138D4C8A76ED395');
        $this->addSql('ALTER TABLE nursing_treatment CHANGE medicines medicines LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE office DROP FOREIGN KEY FK_74516B0263DBB69');
        $this->addSql('ALTER TABLE parameters DROP FOREIGN KEY FK_69348FE63DBB69');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBA76ED395');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBDA91032A');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBCCFA12B8');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB63DBB69');
        $this->addSql('ALTER TABLE personal_image_object DROP FOREIGN KEY FK_E1053F56A76ED395');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D962FF6CDF');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D9628913D5');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D96B899279');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D9A76ED395');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D963DBB69');
        $this->addSql('ALTER TABLE prescription CHANGE orders orders LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE provider DROP FOREIGN KEY FK_92C4739C63DBB69');
        $this->addSql('ALTER TABLE provider DROP FOREIGN KEY FK_92C4739CA76ED395');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD263DBB69');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2AE80F5DF');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C3163DBB69');
        $this->addSql('ALTER TABLE treatment DROP FOREIGN KEY FK_98013C3112469DE2');
        $this->addSql('ALTER TABLE treatment_category DROP FOREIGN KEY FK_94E6519863DBB69');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64994CF6872');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CCFA12B8');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
