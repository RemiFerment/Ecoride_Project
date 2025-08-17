<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817092053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, model VARCHAR(50) NOT NULL, registration VARCHAR(50) NOT NULL, power_engine VARCHAR(50) NOT NULL, first_date_registration DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', marque_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carpooling (id INT AUTO_INCREMENT NOT NULL, start_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', start_hour TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\', start_place VARCHAR(255) NOT NULL, end_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', end_hour TIME NOT NULL COMMENT \'(DC2Type:time_immutable)\', end_place VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, avaible_seat INT NOT NULL, price_per_person INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, comment LONGTEXT NOT NULL, role_id INT NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE carpooling');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE review');
    }
}
