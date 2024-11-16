<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20241116211949 extends AbstractMigration
{


    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, type VARCHAR(255) NOT NULL, link VARCHAR(1084) NOT NULL, status VARCHAR(255) NOT NULL, amount NUMERIC(6, 2) NOT NULL, INDEX IDX_6D28840D1AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D1AD5CDBF');
        $this->addSql('DROP TABLE payment');
    }
}
