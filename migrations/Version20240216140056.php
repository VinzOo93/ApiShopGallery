<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20240216140056 extends AbstractMigration
{


    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart ADD token VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart DROP token');
    }
}
