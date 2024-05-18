<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518153907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ALTER activity_number TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE company ALTER rcs TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE contract ADD status VARCHAR(20) DEFAULT \'draft\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contract DROP status');
        $this->addSql('ALTER TABLE company ALTER activity_number TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE company ALTER rcs TYPE VARCHAR(100)');
    }
}
