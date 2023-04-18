<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418063301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE destock_medicine_for_hospital (id INT AUTO_INCREMENT NOT NULL, medicine_id INT DEFAULT NULL, user_id INT DEFAULT NULL, quantity DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_5CD4372F2F7D140A (medicine_id), INDEX IDX_5CD4372FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE destock_medicine_for_hospital ADD CONSTRAINT FK_5CD4372F2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id)');
        $this->addSql('ALTER TABLE destock_medicine_for_hospital ADD CONSTRAINT FK_5CD4372FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE destock_medicine_for_hospital DROP FOREIGN KEY FK_5CD4372F2F7D140A');
        $this->addSql('ALTER TABLE destock_medicine_for_hospital DROP FOREIGN KEY FK_5CD4372FA76ED395');
        $this->addSql('DROP TABLE destock_medicine_for_hospital');
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
    }
}
