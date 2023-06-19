<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619134151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, legacy_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, UNIQUE INDEX UNIQ_3B02921A184998FC (legacy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project ADD project_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEDA896A19 FOREIGN KEY (project_category_id) REFERENCES project_category (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEDA896A19 ON project (project_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEDA896A19');
        $this->addSql('DROP TABLE project_category');
        $this->addSql('DROP INDEX IDX_2FB3D0EEDA896A19 ON project');
        $this->addSql('ALTER TABLE project DROP project_category_id');
    }
}
