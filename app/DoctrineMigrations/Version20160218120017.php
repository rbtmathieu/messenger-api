<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160218120017 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversation DROP INDEX UNIQ_8A8E26E956AE248B, ADD INDEX IDX_8A8E26E956AE248B (user1_id)');
        $this->addSql('ALTER TABLE conversation DROP INDEX UNIQ_8A8E26E9441B8B65, ADD INDEX IDX_8A8E26E9441B8B65 (user2_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conversation DROP INDEX IDX_8A8E26E956AE248B, ADD UNIQUE INDEX UNIQ_8A8E26E956AE248B (user1_id)');
        $this->addSql('ALTER TABLE conversation DROP INDEX IDX_8A8E26E9441B8B65, ADD UNIQUE INDEX UNIQ_8A8E26E9441B8B65 (user2_id)');
    }
}
