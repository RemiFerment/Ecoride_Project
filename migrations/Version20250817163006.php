<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817163006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_UUID ON user');
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(255) NOT NULL, ADD postal_adress VARCHAR(255) NOT NULL, DROP uuid, DROP lastname, DROP adress, CHANGE email email VARCHAR(180) NOT NULL, CHANGE phone phone_number VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD uuid VARCHAR(180) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD adress VARCHAR(255) NOT NULL, DROP last_name, DROP postal_adress, CHANGE email email VARCHAR(255) NOT NULL, CHANGE phone_number phone VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_UUID ON user (uuid)');
    }
}
