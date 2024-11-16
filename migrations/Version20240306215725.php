<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20240306215725 extends AbstractMigration
{


    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart CHANGE taxes taxes NUMERIC(6, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart CHANGE taxes taxes NUMERIC(5, 2) NOT NULL');
    }
}
