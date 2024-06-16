<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610172142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE runs ADD verified_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE runs ADD CONSTRAINT FK_803A7B1F69F4B775 FOREIGN KEY (verified_by_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_803A7B1F69F4B775 ON runs (verified_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE runs DROP FOREIGN KEY FK_803A7B1F69F4B775');
        $this->addSql('DROP INDEX IDX_803A7B1F69F4B775 ON runs');
        $this->addSql('ALTER TABLE runs DROP verified_by_id');
    }
}
