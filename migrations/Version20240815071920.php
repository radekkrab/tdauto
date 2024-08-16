<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240815071920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create brands, models, cars, programs, and requests tables with appropriate fields and relationships';
    }

    public function up(Schema $schema): void
    {
        // Создание таблицы brands
        $this->addSql('CREATE TABLE brands (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');

        // Создание таблицы models
        $this->addSql('CREATE TABLE models (
            id INT AUTO_INCREMENT NOT NULL, 
            brand_id INT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id),
            CONSTRAINT FK_MODELS_BRAND FOREIGN KEY (brand_id) REFERENCES brands (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');

        // Создание таблицы cars
        $this->addSql('CREATE TABLE cars (
            id INT AUTO_INCREMENT NOT NULL, 
            model_id INT NOT NULL, 
            photo VARCHAR(255) NOT NULL, 
            price INT NOT NULL, 
            PRIMARY KEY(id),
            CONSTRAINT FK_CARS_MODEL FOREIGN KEY (model_id) REFERENCES models (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');

        // Создание таблицы programs
        $this->addSql('CREATE TABLE programs (
            id INT AUTO_INCREMENT NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            interest_rate FLOAT NOT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');

        // Создание таблицы requests
        $this->addSql('CREATE TABLE requests (
            id INT AUTO_INCREMENT NOT NULL, 
            car_id INT NOT NULL, 
            program_id INT NOT NULL, 
            initial_payment INT NOT NULL, 
            loan_term INT NOT NULL, 
            PRIMARY KEY(id),
            CONSTRAINT FK_REQUESTS_CAR FOREIGN KEY (car_id) REFERENCES cars (id) ON DELETE CASCADE,
            CONSTRAINT FK_REQUESTS_PROGRAM FOREIGN KEY (program_id) REFERENCES programs (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // Удаление таблицы requests
        $this->addSql('DROP TABLE requests');

        // Удаление таблицы programs
        $this->addSql('DROP TABLE programs');

        // Удаление таблицы cars
        $this->addSql('DROP TABLE cars');

        // Удаление таблицы models
        $this->addSql('DROP TABLE models');

        // Удаление таблицы brands
        $this->addSql('DROP TABLE brands');
    }
}
