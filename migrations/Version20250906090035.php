<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250906090035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling CHANGE create_by created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6CC153F1B03A8386 ON carpooling (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling DROP FOREIGN KEY FK_6CC153F1B03A8386');
        $this->addSql('DROP INDEX IDX_6CC153F1B03A8386 ON carpooling');
        $this->addSql('ALTER TABLE carpooling CHANGE created_by_id create_by INT NOT NULL');
    }
}
