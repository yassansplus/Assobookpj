<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210727083020 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_adherent DROP CONSTRAINT FK_D63F19B025F06C53');
        $this->addSql('ALTER TABLE event_adherent ADD CONSTRAINT FK_D63F19B025F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE event_adherent DROP CONSTRAINT fk_d63f19b025f06c53');
        $this->addSql('ALTER TABLE event_adherent ADD CONSTRAINT fk_d63f19b025f06c53 FOREIGN KEY (adherent_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
