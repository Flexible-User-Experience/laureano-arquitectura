<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230626060528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_project_category (project_id INT NOT NULL, project_category_id INT NOT NULL, INDEX IDX_86875173166D1F9C (project_id), INDEX IDX_86875173DA896A19 (project_category_id), PRIMARY KEY(project_id, project_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_project_category ADD CONSTRAINT FK_86875173166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_project_category ADD CONSTRAINT FK_86875173DA896A19 FOREIGN KEY (project_category_id) REFERENCES project_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEDA896A19');
        $this->addSql('DROP INDEX IDX_2FB3D0EEDA896A19 ON project');
        $this->addSql('ALTER TABLE project DROP project_category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_project_category DROP FOREIGN KEY FK_86875173166D1F9C');
        $this->addSql('ALTER TABLE project_project_category DROP FOREIGN KEY FK_86875173DA896A19');
        $this->addSql('DROP TABLE project_project_category');
        $this->addSql('ALTER TABLE project ADD project_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEDA896A19 FOREIGN KEY (project_category_id) REFERENCES project_category (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEDA896A19 ON project (project_category_id)');
    }
}
