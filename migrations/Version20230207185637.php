<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207185637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covenant DROP FOREIGN KEY FK_F4EB80F7AB8EAE4A');
        $this->addSql('DROP INDEX UNIQ_F4EB80F7AB8EAE4A ON covenant');
        $this->addSql('ALTER TABLE covenant DROP pdf_object_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covenant ADD pdf_object_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE covenant ADD CONSTRAINT FK_F4EB80F7AB8EAE4A FOREIGN KEY (pdf_object_id) REFERENCES pdf_object (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4EB80F7AB8EAE4A ON covenant (pdf_object_id)');
    }
}
