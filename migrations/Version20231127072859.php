<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20231127072859 extends AbstractMigration
{


    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE photo DROP pre_tax_price');
        $this->addSql('ALTER TABLE print_format ADD pre_tax_price NUMERIC(4, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE photo ADD pre_tax_price NUMERIC(4, 2) NOT NULL');
        $this->addSql('ALTER TABLE print_format DROP pre_tax_price');
    }
}
