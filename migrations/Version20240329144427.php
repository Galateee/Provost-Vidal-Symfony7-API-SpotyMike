<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329144427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(55) NOT NULL, ADD sexe VARCHAR(55) NOT NULL, ADD birth_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE name first_name VARCHAR(55) NOT NULL, CHANGE encrypte password VARCHAR(90) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496B3CA4B ON user (id_user)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D6496B3CA4B ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(55) NOT NULL, DROP first_name, DROP last_name, DROP sexe, DROP birth_date, CHANGE password encrypte VARCHAR(90) NOT NULL');
    }
}
