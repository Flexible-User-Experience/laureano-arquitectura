<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230619143631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_category ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B02921A5E237E06 ON project_category (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B02921A989D9B62 ON project_category (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_3B02921A5E237E06 ON project_category');
        $this->addSql('DROP INDEX UNIQ_3B02921A989D9B62 ON project_category');
        $this->addSql('ALTER TABLE project_category DROP slug');
    }
}
