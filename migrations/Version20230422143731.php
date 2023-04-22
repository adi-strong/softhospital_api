<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422143731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE act CHANGE procedures procedures JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE consultation ADD age INT DEFAULT NULL, CHANGE followed followed JSON DEFAULT NULL, CHANGE medicines_prescriptions medicines_prescriptions JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE lab ADD age INT DEFAULT NULL, CHANGE results results JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing ADD age INT DEFAULT NULL, CHANGE arrival_dates arrival_dates JSON DEFAULT NULL, CHANGE released_at_items released_at_items JSON DEFAULT NULL, CHANGE acts acts JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing_treatment CHANGE medicines medicines JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
        $this->addSql('ALTER TABLE prescription CHANGE orders orders JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE act CHANGE procedures procedures LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE consultation DROP age, CHANGE followed followed LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE medicines_prescriptions medicines_prescriptions LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE lab DROP age, CHANGE results results LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE nursing DROP age, CHANGE arrival_dates arrival_dates LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE released_at_items released_at_items LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE acts acts LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE nursing_treatment CHANGE medicines medicines LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE prescription CHANGE orders orders LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
