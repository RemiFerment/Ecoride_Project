<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250915181629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFBAFB2200A FOREIGN KEY (carpooling_id) REFERENCES carpooling (id)');
        $this->addSql('CREATE INDEX IDX_1C119AFBAFB2200A ON user_review (carpooling_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFBAFB2200A');
        $this->addSql('DROP INDEX IDX_1C119AFBAFB2200A ON user_review');
    }
}
