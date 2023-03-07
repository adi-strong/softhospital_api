<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226185312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX wording_idx ON act');
        $this->addSql('DROP INDEX FIRSTNAME_NAME_LASTNAME_IDX ON agent');
        $this->addSql('DROP INDEX NAME_FIRSTNAME_LASTNAME_IDX ON agent');
        $this->addSql('DROP INDEX NAME_LASTNAME_FIRSTNAME_IDX ON agent');
        $this->addSql('ALTER TABLE consultation CHANGE temperature temperature NUMERIC(9, 0) DEFAULT NULL, CHANGE weight weight NUMERIC(9, 2) DEFAULT NULL');
        $this->addSql('DROP INDEX wording_idx ON consultations_type');
        $this->addSql('DROP INDEX wording_idx ON exam');
        $this->addSql('DROP INDEX name_idx ON patient');
        $this->addSql('DROP INDEX wording_idx ON treatment');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX wording_idx ON act (wording)');
        $this->addSql('CREATE INDEX FIRSTNAME_NAME_LASTNAME_IDX ON agent (first_name, name, last_name)');
        $this->addSql('CREATE INDEX NAME_FIRSTNAME_LASTNAME_IDX ON agent (name, first_name, last_name)');
        $this->addSql('CREATE INDEX NAME_LASTNAME_FIRSTNAME_IDX ON agent (name, last_name, first_name)');
        $this->addSql('ALTER TABLE consultation CHANGE temperature temperature NUMERIC(3, 0) DEFAULT NULL, CHANGE weight weight NUMERIC(3, 2) DEFAULT NULL');
        $this->addSql('CREATE INDEX wording_idx ON consultations_type (wording)');
        $this->addSql('CREATE INDEX wording_idx ON exam (wording)');
        $this->addSql('CREATE INDEX name_idx ON patient (name, first_name, last_name)');
        $this->addSql('CREATE INDEX wording_idx ON treatment (wording)');
    }
}
