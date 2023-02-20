<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230220010511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice_storic (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, user_id INT DEFAULT NULL, amount NUMERIC(10, 2) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_DFA4BEF72989F1FD (invoice_id), INDEX IDX_DFA4BEF7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice_storic ADD CONSTRAINT FK_DFA4BEF72989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE invoice_storic ADD CONSTRAINT FK_DFA4BEF7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_storic DROP FOREIGN KEY FK_DFA4BEF72989F1FD');
        $this->addSql('ALTER TABLE invoice_storic DROP FOREIGN KEY FK_DFA4BEF7A76ED395');
        $this->addSql('DROP TABLE invoice_storic');
    }
}
