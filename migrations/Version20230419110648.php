<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419110648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE act CHANGE procedures procedures JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE consultation CHANGE followed followed JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE lab ADD user_presciber_id INT DEFAULT NULL, ADD user_publisher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C48B6F6CB8 FOREIGN KEY (user_presciber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C4C00413CC FOREIGN KEY (user_publisher_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_61D6B1C48B6F6CB8 ON lab (user_presciber_id)');
        $this->addSql('CREATE INDEX IDX_61D6B1C4C00413CC ON lab (user_publisher_id)');
        $this->addSql('ALTER TABLE nursing CHANGE arrival_dates arrival_dates JSON DEFAULT NULL, CHANGE released_at_items released_at_items JSON DEFAULT NULL, CHANGE acts acts JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE nursing_treatment CHANGE medicines medicines JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
        $this->addSql('ALTER TABLE prescription CHANGE orders orders JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE act CHANGE procedures procedures LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE consultation CHANGE followed followed LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C48B6F6CB8');
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C4C00413CC');
        $this->addSql('DROP INDEX IDX_61D6B1C48B6F6CB8 ON lab');
        $this->addSql('DROP INDEX IDX_61D6B1C4C00413CC ON lab');
        $this->addSql('ALTER TABLE lab DROP user_presciber_id, DROP user_publisher_id');
        $this->addSql('ALTER TABLE nursing CHANGE arrival_dates arrival_dates LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE released_at_items released_at_items LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`, CHANGE acts acts LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE nursing_treatment CHANGE medicines medicines LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE prescription CHANGE orders orders LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
