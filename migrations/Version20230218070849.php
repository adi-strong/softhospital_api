<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218070849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply CHANGE released released DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP FOREIGN KEY FK_DC49F8A2F7D140A');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP FOREIGN KEY FK_DC49F8AB55F5935');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD id INT AUTO_INCREMENT NOT NULL, ADD cost NUMERIC(10, 2) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD CONSTRAINT FK_DC49F8A2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id)');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD CONSTRAINT FK_DC49F8AB55F5935 FOREIGN KEY (drugstore_supply_id) REFERENCES drugstore_supply (id)');
        $this->addSql('DROP INDEX wording_idx ON medicine');
        $this->addSql('DROP INDEX code_idx ON medicine');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply CHANGE released released DATETIME NOT NULL');
        $this->addSql('ALTER TABLE drugstore_supply_medicine MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP FOREIGN KEY FK_DC49F8AB55F5935');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP FOREIGN KEY FK_DC49F8A2F7D140A');
        $this->addSql('DROP INDEX `PRIMARY` ON drugstore_supply_medicine');
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP id, DROP cost');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD CONSTRAINT FK_DC49F8AB55F5935 FOREIGN KEY (drugstore_supply_id) REFERENCES drugstore_supply (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD CONSTRAINT FK_DC49F8A2F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD PRIMARY KEY (drugstore_supply_id, medicine_id)');
        $this->addSql('CREATE INDEX wording_idx ON medicine (wording)');
        $this->addSql('CREATE INDEX code_idx ON medicine (code)');
    }
}
