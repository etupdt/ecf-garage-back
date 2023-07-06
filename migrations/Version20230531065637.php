<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230531065637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car ADD brand VARCHAR(255) NOT NULL, ADD model VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX `primary` ON garage_service');
        $this->addSql('ALTER TABLE garage_service DROP FOREIGN KEY FK_A1E1643DED5CA9E6');
        $this->addSql('ALTER TABLE garage_service DROP FOREIGN KEY FK_A1E1643DC4FFF555');
        $this->addSql('ALTER TABLE garage_service ADD PRIMARY KEY (garage_id, service_id)');
        $this->addSql('DROP INDEX idx_a1e1643dc4fff555 ON garage_service');
        $this->addSql('CREATE INDEX IDX_67DD7642C4FFF555 ON garage_service (garage_id)');
        $this->addSql('DROP INDEX idx_a1e1643ded5ca9e6 ON garage_service');
        $this->addSql('CREATE INDEX IDX_67DD7642ED5CA9E6 ON garage_service (service_id)');
        $this->addSql('ALTER TABLE garage_service ADD CONSTRAINT FK_A1E1643DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE garage_service ADD CONSTRAINT FK_A1E1643DC4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP brand, DROP model');
        $this->addSql('DROP INDEX `PRIMARY` ON garage_service');
        $this->addSql('ALTER TABLE garage_service DROP FOREIGN KEY FK_67DD7642C4FFF555');
        $this->addSql('ALTER TABLE garage_service DROP FOREIGN KEY FK_67DD7642ED5CA9E6');
        $this->addSql('ALTER TABLE garage_service ADD PRIMARY KEY (service_id, garage_id)');
        $this->addSql('DROP INDEX idx_67dd7642ed5ca9e6 ON garage_service');
        $this->addSql('CREATE INDEX IDX_A1E1643DED5CA9E6 ON garage_service (service_id)');
        $this->addSql('DROP INDEX idx_67dd7642c4fff555 ON garage_service');
        $this->addSql('CREATE INDEX IDX_A1E1643DC4FFF555 ON garage_service (garage_id)');
        $this->addSql('ALTER TABLE garage_service ADD CONSTRAINT FK_67DD7642C4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE garage_service ADD CONSTRAINT FK_67DD7642ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }
}
