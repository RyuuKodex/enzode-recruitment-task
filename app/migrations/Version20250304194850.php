<?php

declare(strict_types=1);

namespace App\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250304194850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE attributes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE currencies_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_attributes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE products_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE attributes (id INT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_319B9E7077153098 ON attributes (code)');
        $this->addSql('CREATE TABLE categories (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('CREATE TABLE currencies (id INT NOT NULL, code VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_37C4469377153098 ON currencies (code)');
        $this->addSql('CREATE TABLE product_attributes (id INT NOT NULL, product_id INT NOT NULL, attribute_id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A2FCC15B4584665A ON product_attributes (product_id)');
        $this->addSql('CREATE INDEX IDX_A2FCC15BB6E62EFA ON product_attributes (attribute_id)');
        $this->addSql('CREATE TABLE products (id INT NOT NULL, currency_id INT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A38248176 ON products (currency_id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
        $this->addSql('COMMENT ON COLUMN products.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE product_attributes ADD CONSTRAINT FK_A2FCC15B4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_attributes ADD CONSTRAINT FK_A2FCC15BB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attributes (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A38248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE attributes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE categories_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE currencies_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_attributes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE products_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE product_attributes DROP CONSTRAINT FK_A2FCC15B4584665A');
        $this->addSql('ALTER TABLE product_attributes DROP CONSTRAINT FK_A2FCC15BB6E62EFA');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A38248176');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A12469DE2');
        $this->addSql('DROP TABLE attributes');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE product_attributes');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE users');
    }
}
