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
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE attribute (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(64) NOT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE attribute_value (
            id INT AUTO_INCREMENT NOT NULL,
            attribute_id INT NOT NULL,
            value VARCHAR(255) NOT NULL,
            sort_order INT DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
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
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE TABLE item_category_attribute_value (
            id INT AUTO_INCREMENT NOT NULL,
            item_category_id INT NOT NULL,
            attribute_value_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        )');
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
