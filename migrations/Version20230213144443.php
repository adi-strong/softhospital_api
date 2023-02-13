<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213144443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consumption_unit (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_114B66C463DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicine (id INT AUTO_INCREMENT NOT NULL, consumption_unit_id INT DEFAULT NULL, category_id INT DEFAULT NULL, sub_category_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, user_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, wording VARCHAR(255) NOT NULL, cost NUMERIC(10, 2) DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, quantity INT DEFAULT NULL, expiry_date DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) NOT NULL, INDEX IDX_58362A8D224EAF84 (consumption_unit_id), INDEX IDX_58362A8D12469DE2 (category_id), INDEX IDX_58362A8DF7BFE87C (sub_category_id), INDEX IDX_58362A8D63DBB69 (hospital_id), INDEX IDX_58362A8DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicine_categories (id INT AUTO_INCREMENT NOT NULL, hospital_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_665A0CBA63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicine_sub_categories (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, hospital_id INT DEFAULT NULL, wording VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_FA70B6DE12469DE2 (category_id), INDEX IDX_FA70B6DE63DBB69 (hospital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consumption_unit ADD CONSTRAINT FK_114B66C463DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE medicine ADD CONSTRAINT FK_58362A8D224EAF84 FOREIGN KEY (consumption_unit_id) REFERENCES consumption_unit (id)');
        $this->addSql('ALTER TABLE medicine ADD CONSTRAINT FK_58362A8D12469DE2 FOREIGN KEY (category_id) REFERENCES medicine_categories (id)');
        $this->addSql('ALTER TABLE medicine ADD CONSTRAINT FK_58362A8DF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES medicine_sub_categories (id)');
        $this->addSql('ALTER TABLE medicine ADD CONSTRAINT FK_58362A8D63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE medicine ADD CONSTRAINT FK_58362A8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE medicine_categories ADD CONSTRAINT FK_665A0CBA63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE medicine_sub_categories ADD CONSTRAINT FK_FA70B6DE12469DE2 FOREIGN KEY (category_id) REFERENCES medicine_categories (id)');
        $this->addSql('ALTER TABLE medicine_sub_categories ADD CONSTRAINT FK_FA70B6DE63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consumption_unit DROP FOREIGN KEY FK_114B66C463DBB69');
        $this->addSql('ALTER TABLE medicine DROP FOREIGN KEY FK_58362A8D224EAF84');
        $this->addSql('ALTER TABLE medicine DROP FOREIGN KEY FK_58362A8D12469DE2');
        $this->addSql('ALTER TABLE medicine DROP FOREIGN KEY FK_58362A8DF7BFE87C');
        $this->addSql('ALTER TABLE medicine DROP FOREIGN KEY FK_58362A8D63DBB69');
        $this->addSql('ALTER TABLE medicine DROP FOREIGN KEY FK_58362A8DA76ED395');
        $this->addSql('ALTER TABLE medicine_categories DROP FOREIGN KEY FK_665A0CBA63DBB69');
        $this->addSql('ALTER TABLE medicine_sub_categories DROP FOREIGN KEY FK_FA70B6DE12469DE2');
        $this->addSql('ALTER TABLE medicine_sub_categories DROP FOREIGN KEY FK_FA70B6DE63DBB69');
        $this->addSql('DROP TABLE consumption_unit');
        $this->addSql('DROP TABLE medicine');
        $this->addSql('DROP TABLE medicine_categories');
        $this->addSql('DROP TABLE medicine_sub_categories');
    }
}
