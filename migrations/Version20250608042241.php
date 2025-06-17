<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608042241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ADD date_modification TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER photo_url DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER latitude TYPE NUMERIC(10, 7)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER longitude TYPE NUMERIC(10, 7)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement DROP date_modification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER photo_url SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER latitude TYPE DOUBLE PRECISION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE signalement ALTER longitude TYPE DOUBLE PRECISION
        SQL);
    }
}
