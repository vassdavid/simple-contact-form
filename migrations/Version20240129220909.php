<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129220909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add default administrator';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO "user" ("id", "username", "roles", "password") VALUES (1, \'admin\', \'["ROLE_ADMIN"]\', \'$2y$13$VoVq7GvoYfAmjeRva7ka5uTLnlybybaVupbq0BzCwTj.xZ6QH086W\');');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM "user" WHERE "id" = 1;');
    }
}
