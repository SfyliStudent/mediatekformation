<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240519134002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return ' Add keycloakId field to User table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP keycloack_id, DROP keycloak_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD keycloack_id VARCHAR(255) DEFAULT NULL, ADD keycloak_id VARCHAR(255) DEFAULT NULL');
    }
}
