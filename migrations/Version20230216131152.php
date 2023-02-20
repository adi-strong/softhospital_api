<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230216131152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply ADD provider_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE drugstore_supply ADD CONSTRAINT FK_7F862433A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('CREATE INDEX IDX_7F862433A53A8AA ON drugstore_supply (provider_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply DROP FOREIGN KEY FK_7F862433A53A8AA');
        $this->addSql('DROP INDEX IDX_7F862433A53A8AA ON drugstore_supply');
        $this->addSql('ALTER TABLE drugstore_supply DROP provider_id');
    }
}
