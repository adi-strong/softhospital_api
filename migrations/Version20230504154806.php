<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230504154806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation char(1) default null, CHANGE l_operation l_operation char(1) default null');
        $this->addSql('ALTER TABLE reset_pass_notifier ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reset_pass_notifier ADD CONSTRAINT FK_C158191CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C158191CA76ED395 ON reset_pass_notifier (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parameters CHANGE f_operation f_operation CHAR(1) DEFAULT NULL, CHANGE l_operation l_operation CHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE reset_pass_notifier DROP FOREIGN KEY FK_C158191CA76ED395');
        $this->addSql('DROP INDEX IDX_C158191CA76ED395 ON reset_pass_notifier');
        $this->addSql('ALTER TABLE reset_pass_notifier DROP user_id');
    }
}
