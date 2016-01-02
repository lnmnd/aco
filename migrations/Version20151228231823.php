<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151228231823 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('aco_article');
        $table->addColumn('uuid', 'guid');
        $table->addColumn('title', 'text');
        $table->addColumn('created_at', 'datetimetz');
        $table->addColumn('article_source_url', 'text');
        $table->addColumn('article_source_content', 'text');
        $table->addColumn('content', 'text');
        $table->addColumn('removed', 'boolean');

        $table->setPrimaryKey(['uuid']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('aco_article');
    }
}
