<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322235620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD quantity_label VARCHAR(255) DEFAULT NULL, ADD other_qty INT DEFAULT NULL');
        $this->addSql('ALTER TABLE medicine DROP nb_sales');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP quantity_label, DROP other_qty');
        $this->addSql('ALTER TABLE medicine ADD nb_sales INT DEFAULT NULL');
    }
}
