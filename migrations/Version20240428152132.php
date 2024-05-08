<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240428152132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, ref_series_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, release_date DATE NOT NULL, discord_link VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, rewrite VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_FF232B31212230D9 (ref_series_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE series (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supports (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supports_games (supports_id INT NOT NULL, games_id INT NOT NULL, INDEX IDX_F5FC74B597185C1E (supports_id), INDEX IDX_F5FC74B597FFC673 (games_id), PRIMARY KEY(supports_id, games_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31212230D9 FOREIGN KEY (ref_series_id) REFERENCES series (id)');
        $this->addSql('ALTER TABLE supports_games ADD CONSTRAINT FK_F5FC74B597185C1E FOREIGN KEY (supports_id) REFERENCES supports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supports_games ADD CONSTRAINT FK_F5FC74B597FFC673 FOREIGN KEY (games_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF3466811E800F4 FOREIGN KEY (ref_games_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668864243D7 FOREIGN KEY (ref_categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE field_data ADD CONSTRAINT FK_BE3F9AD9BFE80BA2 FOREIGN KEY (ref_runs_id) REFERENCES runs (id)');
        $this->addSql('ALTER TABLE field_data ADD CONSTRAINT FK_BE3F9AD95F0E38EB FOREIGN KEY (ref_fields_id) REFERENCES fields (id)');
        $this->addSql('ALTER TABLE fields ADD CONSTRAINT FK_7EE5E388864243D7 FOREIGN KEY (ref_categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE fields ADD CONSTRAINT FK_7EE5E388151C8595 FOREIGN KEY (ref_field_types_id) REFERENCES field_types (id)');
        $this->addSql('ALTER TABLE moderations ADD CONSTRAINT FK_CA754290BED09743 FOREIGN KEY (ref_roles_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE moderations ADD CONSTRAINT FK_CA754290E1A472BA FOREIGN KEY (ref_users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE moderations ADD CONSTRAINT FK_CA75429011E800F4 FOREIGN KEY (ref_games_id) REFERENCES games (id)');
        $this->addSql('ALTER TABLE runs ADD CONSTRAINT FK_803A7B1F864243D7 FOREIGN KEY (ref_categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE runs ADD CONSTRAINT FK_803A7B1F18AD01F8 FOREIGN KEY (ref_status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE runs_users ADD CONSTRAINT FK_9C9A2BA07BFC5872 FOREIGN KEY (runs_id) REFERENCES runs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE runs_users ADD CONSTRAINT FK_9C9A2BA067B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF3466811E800F4');
        $this->addSql('ALTER TABLE moderations DROP FOREIGN KEY FK_CA75429011E800F4');
        $this->addSql('ALTER TABLE moderations DROP FOREIGN KEY FK_CA754290BED09743');
        $this->addSql('ALTER TABLE moderations DROP FOREIGN KEY FK_CA754290E1A472BA');
        $this->addSql('ALTER TABLE runs_users DROP FOREIGN KEY FK_9C9A2BA067B3B43D');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31212230D9');
        $this->addSql('ALTER TABLE supports_games DROP FOREIGN KEY FK_F5FC74B597185C1E');
        $this->addSql('ALTER TABLE supports_games DROP FOREIGN KEY FK_F5FC74B597FFC673');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE series');
        $this->addSql('DROP TABLE supports');
        $this->addSql('DROP TABLE supports_games');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668864243D7');
        $this->addSql('ALTER TABLE field_data DROP FOREIGN KEY FK_BE3F9AD9BFE80BA2');
        $this->addSql('ALTER TABLE field_data DROP FOREIGN KEY FK_BE3F9AD95F0E38EB');
        $this->addSql('ALTER TABLE fields DROP FOREIGN KEY FK_7EE5E388864243D7');
        $this->addSql('ALTER TABLE fields DROP FOREIGN KEY FK_7EE5E388151C8595');
        $this->addSql('ALTER TABLE runs DROP FOREIGN KEY FK_803A7B1F864243D7');
        $this->addSql('ALTER TABLE runs DROP FOREIGN KEY FK_803A7B1F18AD01F8');
        $this->addSql('ALTER TABLE runs_users DROP FOREIGN KEY FK_9C9A2BA07BFC5872');
    }
}
