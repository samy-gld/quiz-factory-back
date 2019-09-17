<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190911110849 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer_proposition (answer_id INT NOT NULL, proposition_id INT NOT NULL, INDEX IDX_6C35370AA334807 (answer_id), INDEX IDX_6C35370DB96F9E (proposition_id), PRIMARY KEY(answer_id, proposition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer_proposition ADD CONSTRAINT FK_6C35370AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer_proposition ADD CONSTRAINT FK_6C35370DB96F9E FOREIGN KEY (proposition_id) REFERENCES proposition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer DROP answer_index_tab');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE answer_proposition');
        $this->addSql('ALTER TABLE answer ADD answer_index_tab LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
