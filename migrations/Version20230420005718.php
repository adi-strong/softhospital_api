<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230420005718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab DROP descriptions');
        $this->addSql('ALTER TABLE lab_result ADD results JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab ADD descriptions LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lab_result DROP results');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
    }
}
