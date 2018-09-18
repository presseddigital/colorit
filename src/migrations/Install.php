<?php
namespace fruitstudios\colorit\migrations;

use fruitstudios\colorit\Colorit;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

class Install extends Migration
{
    // Public Properties
    // =========================================================================

    public $driver;

    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables())
        {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
        }
        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();
        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables()
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%fieldtemplates_colorit}}');
        if($tableSchema === null)
        {
            $tablesCreated = true;
            $this->createTable(
                '{{%fieldtemplates_colorit}}',
                [
                    'id' => $this->primaryKey(),
                    'name' => $this->string()->notNull(),
                    'type' => $this->string()->notNull(),
                    'settings' => $this->text(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        return $tablesCreated;
    }

    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName('{{%fieldtemplates_colorit}}', 'name', true),
            '{{%fieldtemplates_colorit}}',
            ['name'],
            false
        );

        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    protected function addForeignKeys()
    {

    }

    protected function removeTables()
    {
        $this->dropTableIfExists('{{%fieldtemplates_colorit}}');
    }
}
