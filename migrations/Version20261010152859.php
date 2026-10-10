<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261010152859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, balance NUMERIC(10, 2) NOT NULL, owner_id INTEGER NOT NULL, CONSTRAINT FK_7D3656A47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7D3656A47E3C61F9 ON account (owner_id)');
        $this->addSql('CREATE TABLE "transaction" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, amount NUMERIC(10, 2) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, source_account_id INTEGER NOT NULL, target_account_id INTEGER DEFAULT NULL, CONSTRAINT FK_723705D1E7DF2E9E FOREIGN KEY (source_account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_723705D1A987872B FOREIGN KEY (target_account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_723705D1E7DF2E9E ON "transaction" (source_account_id)');
        $this->addSql('CREATE INDEX IDX_723705D1A987872B ON "transaction" (target_account_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE "transaction"');
        $this->addSql('DROP TABLE user');
    }
}
