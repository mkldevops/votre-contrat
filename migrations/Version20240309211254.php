<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309211254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ACTIVITY_NUMBER ON company (activity_number)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_RCS ON company (rcs)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_NAME ON company (name)');
        $this->addSql('ALTER TABLE contract ALTER location TYPE VARCHAR(50)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contract ALTER location TYPE VARCHAR(255)');
        $this->addSql('DROP INDEX UNIQ_ACTIVITY_NUMBER');
        $this->addSql('DROP INDEX UNIQ_RCS');
        $this->addSql('DROP INDEX UNIQ_NAME');
    }
}
