<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230128115413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE box_expense ADD box_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE box_expense ADD CONSTRAINT FK_34F81397D8177B3F FOREIGN KEY (box_id) REFERENCES box (id)');
        $this->addSql('CREATE INDEX IDX_34F81397D8177B3F ON box_expense (box_id)');
        $this->addSql('ALTER TABLE box_input ADD box_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE box_input ADD CONSTRAINT FK_A102EE3FD8177B3F FOREIGN KEY (box_id) REFERENCES box (id)');
        $this->addSql('CREATE INDEX IDX_A102EE3FD8177B3F ON box_input (box_id)');
        $this->addSql('ALTER TABLE box_output ADD box_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE box_output ADD CONSTRAINT FK_62765408D8177B3F FOREIGN KEY (box_id) REFERENCES box (id)');
        $this->addSql('CREATE INDEX IDX_62765408D8177B3F ON box_output (box_id)');
        $this->addSql('ALTER TABLE expense_category ADD created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE box_expense DROP FOREIGN KEY FK_34F81397D8177B3F');
        $this->addSql('DROP INDEX IDX_34F81397D8177B3F ON box_expense');
        $this->addSql('ALTER TABLE box_expense DROP box_id');
        $this->addSql('ALTER TABLE box_input DROP FOREIGN KEY FK_A102EE3FD8177B3F');
        $this->addSql('DROP INDEX IDX_A102EE3FD8177B3F ON box_input');
        $this->addSql('ALTER TABLE box_input DROP box_id');
        $this->addSql('ALTER TABLE box_output DROP FOREIGN KEY FK_62765408D8177B3F');
        $this->addSql('DROP INDEX IDX_62765408D8177B3F ON box_output');
        $this->addSql('ALTER TABLE box_output DROP box_id');
        $this->addSql('ALTER TABLE expense_category DROP created_at');
    }
}
