<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Token system associated with Symfony Guard
 */
class Version20160215121746 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE fos_user ADD api_key VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479C912ED9D ON fos_user (api_key)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX UNIQ_957A6479C912ED9D ON fos_user');
        $this->addSql('ALTER TABLE fos_user DROP api_key');
    }
}
