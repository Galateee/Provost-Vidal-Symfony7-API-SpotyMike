<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411200739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album CHANGE year year DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE artist ADD tel VARCHAR(55) NOT NULL');
        $this->addSql('ALTER TABLE artist_has_label CHANGE deleate_year delete_year DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album CHANGE year year INT NOT NULL');
        $this->addSql('ALTER TABLE artist DROP tel');
        $this->addSql('ALTER TABLE artist_has_label CHANGE delete_year deleate_year DATE NOT NULL');
    }
}
