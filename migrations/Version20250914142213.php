<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914142213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_review (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, review_id INT NOT NULL, affected_user_id INT DEFAULT NULL, INDEX IDX_1C119AFBA76ED395 (user_id), INDEX IDX_1C119AFB3E2E969B (review_id), INDEX IDX_1C119AFBEDE70614 (affected_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFB3E2E969B FOREIGN KEY (review_id) REFERENCES review (id)');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFBEDE70614 FOREIGN KEY (affected_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFBA76ED395');
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFB3E2E969B');
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFBEDE70614');
        $this->addSql('DROP TABLE user_review');
    }
}
