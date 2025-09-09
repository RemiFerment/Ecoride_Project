<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909144333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497BC9AA2C');
        $this->addSql('DROP INDEX IDX_8D93D6497BC9AA2C ON user');
        $this->addSql('ALTER TABLE user CHANGE current_car_id_id current_car_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C40F2CF1 FOREIGN KEY (current_car_id) REFERENCES marque (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649C40F2CF1 ON user (current_car_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C40F2CF1');
        $this->addSql('DROP INDEX IDX_8D93D649C40F2CF1 ON user');
        $this->addSql('ALTER TABLE user CHANGE current_car_id current_car_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497BC9AA2C FOREIGN KEY (current_car_id_id) REFERENCES marque (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D6497BC9AA2C ON user (current_car_id_id)');
    }
}
