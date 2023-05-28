<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230527125233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_garage (service_id INT NOT NULL, garage_id INT NOT NULL, INDEX IDX_A1E1643DED5CA9E6 (service_id), INDEX IDX_A1E1643DC4FFF555 (garage_id), PRIMARY KEY(service_id, garage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_garage ADD CONSTRAINT FK_A1E1643DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_garage ADD CONSTRAINT FK_A1E1643DC4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_garage DROP FOREIGN KEY FK_A1E1643DED5CA9E6');
        $this->addSql('ALTER TABLE service_garage DROP FOREIGN KEY FK_A1E1643DC4FFF555');
        $this->addSql('DROP TABLE service_garage');
    }
}
