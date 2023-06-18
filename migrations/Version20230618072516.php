<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230618072516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, fiscal_identification_code VARCHAR(20) NOT NULL, fiscal_name VARCHAR(255) NOT NULL, commercial_name VARCHAR(255) DEFAULT NULL, address1 VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, is_company TINYINT(1) DEFAULT 1 NOT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, locale VARCHAR(255) DEFAULT \'ca\' NOT NULL, mobile_number VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, total_invoiced_amount INT DEFAULT 0 NOT NULL, total_invoiced_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, UNIQUE INDEX UNIQ_81398E093BF940D3 (fiscal_identification_code), UNIQUE INDEX UNIQ_81398E09184998FC (legacy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, expense_category_id INT DEFAULT NULL, provider_id INT DEFAULT NULL, date DATE NOT NULL, description VARCHAR(255) NOT NULL, document_filename VARCHAR(255) DEFAULT NULL, category VARCHAR(255) DEFAULT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, has_been_paid TINYINT(1) DEFAULT 0 NOT NULL, payment_date DATETIME DEFAULT NULL, tax_base_amount INT DEFAULT 0 NOT NULL, tax_base_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, tax_percentage DOUBLE PRECISION DEFAULT \'21\' NOT NULL, total_amount INT DEFAULT 0 NOT NULL, total_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, UNIQUE INDEX UNIQ_2D3A8DA6184998FC (legacy_id), INDEX IDX_2D3A8DA66B2A3179 (expense_category_id), INDEX IDX_2D3A8DA6A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expense_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, total_invoiced_amount INT DEFAULT 0 NOT NULL, total_invoiced_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, UNIQUE INDEX UNIQ_C02DDB38184998FC (legacy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, date DATETIME NOT NULL, send_date DATETIME DEFAULT NULL, has_been_sent TINYINT(1) DEFAULT 0 NOT NULL, payment_method INT DEFAULT 0 NOT NULL, payment_comments VARCHAR(255) DEFAULT NULL, series VARCHAR(64) DEFAULT \'---\' NOT NULL, number INT DEFAULT 0 NOT NULL, is_intra_community_invoice TINYINT(1) DEFAULT 0 NOT NULL, customer_reference VARCHAR(255) DEFAULT NULL, discount_percentage DOUBLE PRECISION DEFAULT \'0\' NOT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, has_been_paid TINYINT(1) DEFAULT 0 NOT NULL, payment_date DATETIME DEFAULT NULL, tax_base_amount INT DEFAULT 0 NOT NULL, tax_base_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, tax_percentage DOUBLE PRECISION DEFAULT \'21\' NOT NULL, total_amount INT DEFAULT 0 NOT NULL, total_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, UNIQUE INDEX UNIQ_90651744184998FC (legacy_id), INDEX IDX_906517449395C3F3 (customer_id), UNIQUE INDEX series_number (series, number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_line (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, units DOUBLE PRECISION DEFAULT \'0\' NOT NULL, description VARCHAR(255) NOT NULL, discount DOUBLE PRECISION DEFAULT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, total_amount INT DEFAULT 0 NOT NULL, total_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, unit_price_amount INT DEFAULT 0 NOT NULL, unit_price_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, UNIQUE INDEX UNIQ_D3D1D693184998FC (legacy_id), INDEX IDX_D3D1D6932989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, fiscal_identification_code VARCHAR(20) NOT NULL, fiscal_name VARCHAR(255) NOT NULL, commercial_name VARCHAR(255) DEFAULT NULL, address1 VARCHAR(255) DEFAULT NULL, address2 VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, mobile_number VARCHAR(255) DEFAULT NULL, is_company TINYINT(1) DEFAULT 1 NOT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, locale VARCHAR(255) DEFAULT \'ca\' NOT NULL, website VARCHAR(255) DEFAULT NULL, total_invoiced_amount INT DEFAULT 0 NOT NULL, total_invoiced_currency VARCHAR(64) DEFAULT \'EUR\' NOT NULL, UNIQUE INDEX UNIQ_92C4739C3BF940D3 (fiscal_identification_code), UNIQUE INDEX UNIQ_92C4739C184998FC (legacy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA66B2A3179 FOREIGN KEY (expense_category_id) REFERENCES expense_category (id)');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE invoice_line ADD CONSTRAINT FK_D3D1D6932989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA66B2A3179');
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6A53A8AA');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449395C3F3');
        $this->addSql('ALTER TABLE invoice_line DROP FOREIGN KEY FK_D3D1D6932989F1FD');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE expense');
        $this->addSql('DROP TABLE expense_category');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_line');
        $this->addSql('DROP TABLE provider');
    }
}
