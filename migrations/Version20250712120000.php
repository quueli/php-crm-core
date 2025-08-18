<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250712120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial catalog schema: categories, subjects, attributes and the item matrix';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE category (
            id INT AUTO_INCREMENT NOT NULL,
            parent_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            sort_order INT DEFAULT NULL,
            level INT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_category_parent (parent_id),
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE subject (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            synonym VARCHAR(255) DEFAULT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_subject_category (category_id),
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE attribute (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(64) NOT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_attribute_code (code),
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE attribute_value (
            id INT AUTO_INCREMENT NOT NULL,
            attribute_id INT NOT NULL,
            value VARCHAR(255) NOT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_attribute_value_attribute (attribute_id),
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE audience (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            sort_order INT DEFAULT NULL,
            is_active TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE context (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            sort_order INT DEFAULT NULL,
            is_active TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE line (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE item (
            id INT AUTO_INCREMENT NOT NULL,
            line_id INT NOT NULL,
            audience_id INT NOT NULL,
            context_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_item_line (line_id),
            INDEX IDX_item_audience (audience_id),
            INDEX IDX_item_context (context_id),
            UNIQUE INDEX UNIQ_item_combo (line_id, audience_id, context_id),
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE item_category (
            id INT AUTO_INCREMENT NOT NULL,
            item_id INT NOT NULL,
            category_id INT DEFAULT NULL,
            subject_id INT DEFAULT NULL,
            segment VARCHAR(64) DEFAULT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_item_category_item (item_id),
            INDEX IDX_item_category_category (category_id),
            INDEX IDX_item_category_subject (subject_id),
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE item_category_attribute_value (
            id INT AUTO_INCREMENT NOT NULL,
            item_category_id INT NOT NULL,
            attribute_value_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            INDEX IDX_icav_item_category (item_category_id),
            INDEX IDX_icav_attribute_value (attribute_value_id),
            PRIMARY KEY(id)
        )');

        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_category_parent FOREIGN KEY (parent_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_subject_category FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT FK_av_attribute FOREIGN KEY (attribute_id) REFERENCES attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_item_line FOREIGN KEY (line_id) REFERENCES line (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_item_audience FOREIGN KEY (audience_id) REFERENCES audience (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_item_context FOREIGN KEY (context_id) REFERENCES context (id)');
        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_ic_item FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_ic_category FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE item_category ADD CONSTRAINT FK_ic_subject FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE item_category_attribute_value ADD CONSTRAINT FK_icav_ic FOREIGN KEY (item_category_id) REFERENCES item_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_category_attribute_value ADD CONSTRAINT FK_icav_av FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE item_category_attribute_value');
        $this->addSql('DROP TABLE item_category');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE line');
        $this->addSql('DROP TABLE context');
        $this->addSql('DROP TABLE audience');
        $this->addSql('DROP TABLE attribute_value');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE category');
    }
}
