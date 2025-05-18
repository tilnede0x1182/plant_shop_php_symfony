<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518153935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item DROP CONSTRAINT fk_52ea1f09177b16e8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_52ea1f09177b16e8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item RENAME COLUMN plante_id TO plant_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F091D935652 FOREIGN KEY (plant_id) REFERENCES plants (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52EA1F091D935652 ON order_item (plant_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F091D935652
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_52EA1F091D935652
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item RENAME COLUMN plant_id TO plante_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD CONSTRAINT fk_52ea1f09177b16e8 FOREIGN KEY (plante_id) REFERENCES plants (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_52ea1f09177b16e8 ON order_item (plante_id)
        SQL);
    }
}
