<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250604154909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ADD cout NUMERIC(10, 2) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ADD commentaire TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ALTER utilisateur_id DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ALTER description DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ALTER statut TYPE VARCHAR(50)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation DROP cout
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation DROP commentaire
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ALTER utilisateur_id SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ALTER description SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reparation ALTER statut TYPE VARCHAR(255)
        SQL);
    }
}
