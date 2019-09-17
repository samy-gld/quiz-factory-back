<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190909173751 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, execution_id INT NOT NULL, question_position INT NOT NULL, answer_index_tab LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', success TINYINT(1) NOT NULL, INDEX IDX_DADD4A2557125544 (execution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE execution (id INT AUTO_INCREMENT NOT NULL, invitation_token VARCHAR(255) NOT NULL, started TINYINT(1) NOT NULL, finished TINYINT(1) NOT NULL, current_position INT NOT NULL, UNIQUE INDEX UNIQ_2A0D73A33FC351A (invitation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invitation (token VARCHAR(255) NOT NULL, quiz_id INT NOT NULL, execution_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_F11D61A2853CD175 (quiz_id), UNIQUE INDEX UNIQ_F11D61A257125544 (execution_id), PRIMARY KEY(token)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A2557125544 FOREIGN KEY (execution_id) REFERENCES execution (id)');
        $this->addSql('ALTER TABLE execution ADD CONSTRAINT FK_2A0D73A33FC351A FOREIGN KEY (invitation_token) REFERENCES invitation (token)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A257125544 FOREIGN KEY (execution_id) REFERENCES execution (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A2557125544');
        $this->addSql('ALTER TABLE invitation DROP FOREIGN KEY FK_F11D61A257125544');
        $this->addSql('ALTER TABLE execution DROP FOREIGN KEY FK_2A0D73A33FC351A');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE execution');
        $this->addSql('DROP TABLE invitation');
    }
}
