<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319221919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_storic ADD nursing_id INT DEFAULT NULL, CHANGE invoice_id invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice_storic ADD CONSTRAINT FK_DFA4BEF7936DC252 FOREIGN KEY (nursing_id) REFERENCES nursing (id)');
        $this->addSql('CREATE INDEX IDX_DFA4BEF7936DC252 ON invoice_storic (nursing_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_storic DROP FOREIGN KEY FK_DFA4BEF7936DC252');
        $this->addSql('DROP INDEX IDX_DFA4BEF7936DC252 ON invoice_storic');
        $this->addSql('ALTER TABLE invoice_storic DROP nursing_id, CHANGE invoice_id invoice_id INT NOT NULL');
    }
}
