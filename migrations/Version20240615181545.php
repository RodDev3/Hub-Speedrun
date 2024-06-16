<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240615181545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields DROP display, DROP quick_filter');
        $this->addSql('ALTER TABLE games CHANGE discord_link discord_link VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields ADD display TINYINT(1) NOT NULL, ADD quick_filter TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE games CHANGE discord_link discord_link VARCHAR(255) NOT NULL');
    }
}
