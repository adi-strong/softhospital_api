<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218125656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE medicine_invoice (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, released DATETIME DEFAULT NULL, INDEX IDX_9D9E605AA76ED395 (user_id), INDEX IDX_9D9E605A63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicines_sold (id INT AUTO_INCREMENT NOT NULL, medicine_id INT NOT NULL, invoice_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, sum NUMERIC(10, 2) NOT NULL, INDEX IDX_A1116C852F7D140A (medicine_id), INDEX IDX_A1116C852989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medicine_invoice ADD CONSTRAINT FK_9D9E605AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medicine_invoice ADD CONSTRAINT FK_9D9E605A63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE medicines_sold ADD CONSTRAINT FK_A1116C852F7D140A FOREIGN KEY (medicine_id) REFERENCES medicine (id)');
        $this->addSql('ALTER TABLE medicines_sold ADD CONSTRAINT FK_A1116C852989F1FD FOREIGN KEY (invoice_id) REFERENCES medicine_invoice (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medicine_invoice DROP FOREIGN KEY FK_9D9E605AA76ED395');
        $this->addSql('ALTER TABLE medicine_invoice DROP FOREIGN KEY FK_9D9E605A63DBB69');
        $this->addSql('ALTER TABLE medicines_sold DROP FOREIGN KEY FK_A1116C852F7D140A');
        $this->addSql('ALTER TABLE medicines_sold DROP FOREIGN KEY FK_A1116C852989F1FD');
        $this->addSql('DROP TABLE medicine_invoice');
        $this->addSql('DROP TABLE medicines_sold');
    }
}
