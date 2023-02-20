<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216032223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE drugstore_supply (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, document VARCHAR(255) NOT NULL, released DATETIME NOT NULL, INDEX IDX_7F862433A76ED395 (user_id), INDEX IDX_7F86243363DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drugstore_supply_medicine (drugstore_supply_id INT NOT NULL, medicine_id INT NOT NULL, INDEX IDX_DC49F8AB55F5935 (drugstore_supply_id), INDEX IDX_DC49F8A2F7D140A (medicine_id), PRIMARY KEY(drugstore_supply_id, medicine_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE drugstore_supply ADD CONSTRAINT FK_7F862433A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE drugstore_supply ADD CONSTRAINT FK_7F86243363DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD CONSTRAINT FK_DC49F8AB55F5935 FOREIGN KEY (drugstore_supply_id) REFERENCES drugstore_supply (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD CONSTRAINT FK_DC49F8A2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply DROP FOREIGN KEY FK_7F862433A76ED395');
        $this->addSql('ALTER TABLE drugstore_supply DROP FOREIGN KEY FK_7F86243363DBB69');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP FOREIGN KEY FK_DC49F8AB55F5935');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP FOREIGN KEY FK_DC49F8A2F7D140A');
        $this->addSql('DROP TABLE drugstore_supply');
        $this->addSql('DROP TABLE drugstore_supply_medicine');
    }
}
