<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503221825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, subtotal NUMERIC(6, 2) NOT NULL, taxes NUMERIC(5, 2) NOT NULL, shipping NUMERIC(4, 2) NOT NULL, total NUMERIC(6, 2) NOT NULL, INDEX IDX_BA388B7126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, print_format_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(5, 2) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_1F1B251EA44FDF1C (print_format_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE print_format (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, width INT NOT NULL, height INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EA44FDF1C FOREIGN KEY (print_format_id) REFERENCES print_format (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7126F525E');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EA44FDF1C');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE print_format');
    }
}
