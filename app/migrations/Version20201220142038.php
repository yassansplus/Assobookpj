<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201220142038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE iw.user_account ADD adherent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE iw.user_account ADD association_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE iw.user_account ADD CONSTRAINT FK_7B4B816A25F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE iw.user_account ADD CONSTRAINT FK_7B4B816AEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B4B816A25F06C53 ON iw.user_account (adherent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B4B816AEFB9C8A5 ON iw.user_account (association_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE iw.user_account DROP CONSTRAINT FK_7B4B816A25F06C53');
        $this->addSql('ALTER TABLE iw.user_account DROP CONSTRAINT FK_7B4B816AEFB9C8A5');
        $this->addSql('DROP INDEX UNIQ_7B4B816A25F06C53');
        $this->addSql('DROP INDEX UNIQ_7B4B816AEFB9C8A5');
        $this->addSql('ALTER TABLE iw.user_account DROP adherent_id');
        $this->addSql('ALTER TABLE iw.user_account DROP association_id');
    }
}
