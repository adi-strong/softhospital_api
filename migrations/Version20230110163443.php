<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230110163443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE covenant (id INT AUTO_INCREMENT NOT NULL, denomination VARCHAR(255) NOT NULL, unit_name VARCHAR(255) DEFAULT NULL, focal VARCHAR(255) NOT NULL, tel VARCHAR(20) NOT NULL, email VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, u_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, covenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, sex VARCHAR(255) DEFAULT NULL, birth_date DATE NOT NULL, birth_place VARCHAR(255) DEFAULT NULL, marital_status VARCHAR(12) DEFAULT NULL, tel VARCHAR(20) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, father VARCHAR(255) DEFAULT NULL, mother VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, private_key VARCHAR(255) DEFAULT NULL, is_private_key_exists TINYINT(1) NOT NULL, u_id VARCHAR(255) DEFAULT NULL, INDEX IDX_1ADAD7EBA76ED395 (user_id), INDEX IDX_1ADAD7EBDA91032A (covenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBDA91032A FOREIGN KEY (covenant_id) REFERENCES covenant (id)');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL, ADD u_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBA76ED395');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBDA91032A');
        $this->addSql('DROP TABLE covenant');
        $this->addSql('DROP TABLE patient');
        $this->addSql('ALTER TABLE user DROP created_at, DROP u_id');
    }
}
