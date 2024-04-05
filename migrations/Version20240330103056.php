<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330103056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD first_name VARCHAR(55) NOT NULL, ADD last_name VARCHAR(55) NOT NULL, ADD sexe VARCHAR(55) NOT NULL, ADD birth_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP fullname');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD fullname VARCHAR(90) NOT NULL, DROP first_name, DROP last_name, DROP sexe, DROP birth_date');
    }
}
