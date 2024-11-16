<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20240206134839 extends AbstractMigration
{


    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item ADD unit_price NUMERIC(6, 2) NOT NULL, ADD unit_pre_tax_price NUMERIC(6, 2) NOT NULL, ADD pre_tax_price NUMERIC(6, 2) NOT NULL, ADD tax_price NUMERIC(6, 2) NOT NULL, DROP price');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE item ADD price NUMERIC(5, 2) NOT NULL, DROP unit_price, DROP unit_pre_tax_price, DROP pre_tax_price, DROP tax_price');
    }
}
