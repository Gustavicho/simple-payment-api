<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017015316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE `transactions` (
                id INT AUTO_INCREMENT NOT NULL, 
                sender_id INT NOT NULL, 
                receiver_id INT NOT NULL, 
                value NUMERIC(10, 2) NOT NULL, 
                INDEX IDX_EAA81A4CF624B39D (sender_id), 
                INDEX IDX_EAA81A4CCD53EDB6 (receiver_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` 
            ENGINE = InnoDB'
        );

        $this->addSql(
            'CREATE TABLE `users` (
                id INT AUTO_INCREMENT NOT NULL, 
                email VARCHAR(180) NOT NULL, 
                roles JSON NOT NULL, 
                password VARCHAR(255) NOT NULL, 
                full_name VARCHAR(255) NOT NULL, 
                document VARCHAR(18) NOT NULL, 
                balance NUMERIC(10, 2) NOT NULL, 
                UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), 
                UNIQUE INDEX UNIQ_IDENTIFIER_DOCUMENT (document), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 
            COLLATE `utf8mb4_unicode_ci` 
            ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE `transactions` 
             ADD CONSTRAINT FK_EAA81A4CF624B39D 
             FOREIGN KEY (sender_id) REFERENCES `users` (id)'
        );

        $this->addSql(
            'ALTER TABLE `transactions` 
             ADD CONSTRAINT FK_EAA81A4CCD53EDB6 
             FOREIGN KEY (receiver_id) REFERENCES `users` (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `transactions` DROP FOREIGN KEY FK_EAA81A4CF624B39D');
        $this->addSql('ALTER TABLE `transactions` DROP FOREIGN KEY FK_EAA81A4CCD53EDB6');
        $this->addSql('DROP TABLE `transactions`');
        $this->addSql('DROP TABLE `users`');
    }
}