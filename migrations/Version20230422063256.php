<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422063256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent CHANGE phone phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE covenant CHANGE tel tel VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE hospital CHANGE tel tel VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
        $this->addSql('ALTER TABLE patient CHANGE tel tel VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent CHANGE phone phone VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE covenant CHANGE tel tel VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE hospital CHANGE tel tel VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE patient CHANGE tel tel VARCHAR(20) DEFAULT NULL');
    }
}
