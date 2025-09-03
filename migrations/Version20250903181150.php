<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250903181150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling DROP FOREIGN KEY FK_6CC153F1A0EF1B80');
        $this->addSql('DROP INDEX IDX_6CC153F1A0EF1B80 ON carpooling');
        $this->addSql('ALTER TABLE carpooling CHANGE car_id_id car_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F1C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('CREATE INDEX IDX_6CC153F1C3C6F69F ON carpooling (car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carpooling DROP FOREIGN KEY FK_6CC153F1C3C6F69F');
        $this->addSql('DROP INDEX IDX_6CC153F1C3C6F69F ON carpooling');
        $this->addSql('ALTER TABLE carpooling CHANGE car_id car_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carpooling ADD CONSTRAINT FK_6CC153F1A0EF1B80 FOREIGN KEY (car_id_id) REFERENCES car (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6CC153F1A0EF1B80 ON carpooling (car_id_id)');
    }
}
