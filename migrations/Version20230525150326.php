<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230525150326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, garage_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, year INT NOT NULL, kilometer INT NOT NULL, UNIQUE INDEX UNIQ_773DE69D3DA5256D (image_id), INDEX IDX_773DE69DC4FFF555 (garage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_option (car_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_42EEEC42C3C6F69F (car_id), INDEX IDX_42EEEC42A7C41D6F (option_id), PRIMARY KEY(car_id, option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, comment LONGTEXT NOT NULL, note INT NOT NULL, is_approved TINYINT(1) NOT NULL, INDEX IDX_9474526CC4FFF555 (garage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_4C62E638C4FFF555 (garage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_1FD77566C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garage (id INT AUTO_INCREMENT NOT NULL, raison VARCHAR(255) NOT NULL, phone VARCHAR(16) NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) NOT NULL, zip VARCHAR(10) NOT NULL, locality VARCHAR(255) NOT NULL, day1hours VARCHAR(255) NOT NULL, day2hours VARCHAR(255) NOT NULL, day3hours VARCHAR(255) NOT NULL, day4hours VARCHAR(255) NOT NULL, day5hours VARCHAR(255) NOT NULL, day6hours VARCHAR(255) NOT NULL, day7hours VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garage_service (garage_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_67DD7642C4FFF555 (garage_id), INDEX IDX_67DD7642ED5CA9E6 (service_id), PRIMARY KEY(garage_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_C53D045FC3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, garage_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone VARCHAR(16) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649C4FFF555 (garage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DC4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)');
        $this->addSql('ALTER TABLE car_option ADD CONSTRAINT FK_42EEEC42C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE car_option ADD CONSTRAINT FK_42EEEC42A7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638C4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD77566C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE garage_service ADD CONSTRAINT FK_67DD7642C4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE garage_service ADD CONSTRAINT FK_67DD7642ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C4FFF555 FOREIGN KEY (garage_id) REFERENCES garage (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D3DA5256D');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DC4FFF555');
        $this->addSql('ALTER TABLE car_option DROP FOREIGN KEY FK_42EEEC42C3C6F69F');
        $this->addSql('ALTER TABLE car_option DROP FOREIGN KEY FK_42EEEC42A7C41D6F');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC4FFF555');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638C4FFF555');
        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD77566C3C6F69F');
        $this->addSql('ALTER TABLE garage_service DROP FOREIGN KEY FK_67DD7642C4FFF555');
        $this->addSql('ALTER TABLE garage_service DROP FOREIGN KEY FK_67DD7642ED5CA9E6');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FC3C6F69F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C4FFF555');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE car_option');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE garage');
        $this->addSql('DROP TABLE garage_service');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
    }
}
