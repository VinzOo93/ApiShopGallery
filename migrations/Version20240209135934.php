<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20240209135934 extends AbstractMigration
{


    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7126F525E');
        $this->addSql('DROP INDEX IDX_BA388B7126F525E ON cart');
        $this->addSql('ALTER TABLE cart DROP item_id');
        $this->addSql('ALTER TABLE item ADD cart_id INT NOT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('CREATE INDEX IDX_1F1B251E1AD5CDBF ON item (cart_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart ADD item_id INT NOT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_BA388B7126F525E ON cart (item_id)');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E1AD5CDBF');
        $this->addSql('DROP INDEX IDX_1F1B251E1AD5CDBF ON item');
        $this->addSql('ALTER TABLE item DROP cart_id');
    }
}
