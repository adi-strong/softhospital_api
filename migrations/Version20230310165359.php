<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310165359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab ADD descriptions LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE lab_result DROP results');
        $this->addSql('DROP INDEX isPublished_IDX ON prescription');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lab DROP descriptions');
        $this->addSql('ALTER TABLE lab_result ADD results LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX isPublished_IDX ON prescription (is_published)');
    }
}
