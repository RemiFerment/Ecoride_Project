<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903185937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_carpooling (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_carpooling_user (user_carpooling_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_37A3CBA0413C8D5A (user_carpooling_id), INDEX IDX_37A3CBA0A76ED395 (user_id), PRIMARY KEY(user_carpooling_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_carpooling_user ADD CONSTRAINT FK_37A3CBA0413C8D5A FOREIGN KEY (user_carpooling_id) REFERENCES user_carpooling (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_carpooling_user ADD CONSTRAINT FK_37A3CBA0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user_carpolling');
        $this->addSql('ALTER TABLE carpooling ADD user_carpooling_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F1413C8D5A FOREIGN KEY (user_carpooling_id) REFERENCES user_carpooling (id)');
        $this->addSql('CREATE INDEX IDX_6CC153F1413C8D5A ON carpooling (user_carpooling_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling DROP FOREIGN KEY FK_6CC153F1413C8D5A');
        $this->addSql('CREATE TABLE user_carpolling (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, carpooling_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_carpooling_user DROP FOREIGN KEY FK_37A3CBA0413C8D5A');
        $this->addSql('ALTER TABLE user_carpooling_user DROP FOREIGN KEY FK_37A3CBA0A76ED395');
        $this->addSql('DROP TABLE user_carpooling');
        $this->addSql('DROP TABLE user_carpooling_user');
        $this->addSql('DROP INDEX IDX_6CC153F1413C8D5A ON carpooling');
        $this->addSql('ALTER TABLE carpooling DROP user_carpooling_id');
    }
}
