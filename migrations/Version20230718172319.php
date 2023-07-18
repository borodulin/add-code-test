<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230718172319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE catalog (id SERIAL NOT NULL, parent_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B2C3247727ACA70 ON catalog (parent_id)');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247727ACA70 FOREIGN KEY (parent_id) REFERENCES catalog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE catalog DROP CONSTRAINT FK_1B2C3247727ACA70');
        $this->addSql('DROP TABLE catalog');
    }
}
