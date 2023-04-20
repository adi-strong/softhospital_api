<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230419110751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C48B6F6CB8');
        $this->addSql('DROP INDEX IDX_61D6B1C48B6F6CB8 ON lab');
        $this->addSql('ALTER TABLE lab CHANGE user_presciber_id user_prescriber_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C476EF8C3F FOREIGN KEY (user_prescriber_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_61D6B1C476EF8C3F ON lab (user_prescriber_id)');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab DROP FOREIGN KEY FK_61D6B1C476EF8C3F');
        $this->addSql('DROP INDEX IDX_61D6B1C476EF8C3F ON lab');
        $this->addSql('ALTER TABLE lab CHANGE user_prescriber_id user_presciber_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lab ADD CONSTRAINT FK_61D6B1C48B6F6CB8 FOREIGN KEY (user_presciber_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_61D6B1C48B6F6CB8 ON lab (user_presciber_id)');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
    }
}
