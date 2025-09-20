<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914195011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling ADD review_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F13E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        $this->addSql('CREATE INDEX IDX_6CC153F13E2E969B ON carpooling (review_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling DROP FOREIGN KEY FK_6CC153F13E2E969B');
        $this->addSql('DROP INDEX IDX_6CC153F13E2E969B ON carpooling');
        $this->addSql('ALTER TABLE carpooling DROP review_id');
    }
}
