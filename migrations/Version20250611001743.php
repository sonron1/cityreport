<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611001743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ADD moderateur_suppression_statut_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ADD commentaire_suppression_statut TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER etat_validation TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114B5F5F7C9 FOREIGN KEY (moderateur_suppression_statut_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F4B55114B5F5F7C9 ON signalement (moderateur_suppression_statut_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement DROP CONSTRAINT FK_F4B55114B5F5F7C9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F4B55114B5F5F7C9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement DROP moderateur_suppression_statut_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement DROP commentaire_suppression_statut
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER etat_validation TYPE VARCHAR(50)
        SQL);
    }
}
