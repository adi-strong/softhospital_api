<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230323092853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply_medicine ADD currency VARCHAR(50) DEFAULT NULL');
        $this->addSql('DROP INDEX wording_IDX ON medicine');
        $this->addSql('DROP INDEX code_IDX ON medicine');
        $this->addSql('DROP INDEX wording_IDX ON provider');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE drugstore_supply_medicine DROP currency');
        $this->addSql('CREATE INDEX wording_IDX ON medicine (wording)');
        $this->addSql('CREATE INDEX code_IDX ON medicine (code)');
        $this->addSql('CREATE INDEX wording_IDX ON provider (wording)');
    }
}
