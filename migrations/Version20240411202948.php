<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411202948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_label (artist_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_6EAB60BBB7970CF8 (artist_id), INDEX IDX_6EAB60BB33B92F39 (label_id), PRIMARY KEY(artist_id, label_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artist_has_label (id INT AUTO_INCREMENT NOT NULL, create_year DATE NOT NULL, delete_year DATE NOT NULL, id_artist VARCHAR(255) NOT NULL, id_label VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, id_label VARCHAR(90) NOT NULL, name VARCHAR(255) NOT NULL, year_creation DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_artist (user_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_640B8DBAA76ED395 (user_id), INDEX IDX_640B8DBAB7970CF8 (artist_id), PRIMARY KEY(user_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_playlist (user_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_370FF52DA76ED395 (user_id), INDEX IDX_370FF52D6BBD148 (playlist_id), PRIMARY KEY(user_id, playlist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_label ADD CONSTRAINT FK_6EAB60BBB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_label ADD CONSTRAINT FK_6EAB60BB33B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_playlist ADD CONSTRAINT FK_370FF52DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_playlist ADD CONSTRAINT FK_370FF52D6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album CHANGE year year DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE nom name VARCHAR(90) NOT NULL, CHANGE categ category VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE artist ADD full_name VARCHAR(55) NOT NULL, ADD sexe VARCHAR(55) NOT NULL, ADD tel VARCHAR(55) NOT NULL, ADD birth_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD update_at VARCHAR(255) NOT NULL, DROP fullname');
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(55) NOT NULL, ADD sexe VARCHAR(55) NOT NULL, ADD birth_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE name first_name VARCHAR(55) NOT NULL, CHANGE encrypte password VARCHAR(90) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_label DROP FOREIGN KEY FK_6EAB60BBB7970CF8');
        $this->addSql('ALTER TABLE artist_label DROP FOREIGN KEY FK_6EAB60BB33B92F39');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAA76ED395');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAB7970CF8');
        $this->addSql('ALTER TABLE user_playlist DROP FOREIGN KEY FK_370FF52DA76ED395');
        $this->addSql('ALTER TABLE user_playlist DROP FOREIGN KEY FK_370FF52D6BBD148');
        $this->addSql('DROP TABLE artist_label');
        $this->addSql('DROP TABLE artist_has_label');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE user_artist');
        $this->addSql('DROP TABLE user_playlist');
        $this->addSql('ALTER TABLE album CHANGE year year INT NOT NULL, CHANGE name nom VARCHAR(90) NOT NULL, CHANGE category categ VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE artist ADD fullname VARCHAR(90) NOT NULL, DROP full_name, DROP sexe, DROP tel, DROP birth_date, DROP create_at, DROP update_at');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(55) NOT NULL, DROP first_name, DROP last_name, DROP sexe, DROP birth_date, CHANGE password encrypte VARCHAR(90) NOT NULL');
    }
}
