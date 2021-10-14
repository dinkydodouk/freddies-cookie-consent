<?php
/**
 * Cookie Consent plugin for Craft CMS 3.x
 *
 * A cookie consent banner that blocks cookies before they are set.
 *
 * @link      https://www.dinkydodo.com
 * @copyright Copyright (c) 2021 Freddie Dodo
 */

namespace dinkydodouk\freddiescookieconsent\migrations;

use dinkydodouk\freddiescookieconsent\FreddiesCookieConsent;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Freddie Dodo
 * @package   FreddiesCookieConsent
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    protected function createTables()
    {
        $tablesCreated = false;

        $tableSettings = Craft::$app->db->schema->getTableSchema('{{%freddiescookieconsent_settings}}');
        $tableCookies = Craft::$app->db->schema->getTableSchema('{{%freddiescookieconsent_cookies}}');
        $tableCookiesSections = Craft::$app->db->schema->getTableSchema('{{%freddiescookieconsent_cookies_sections}}');
        $tableCookiesAccept = Craft::$app->db->schema->getTableSchema('{{%freddiescookieconsent_cookies_accept}}');

        if ($tableSettings === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%freddiescookieconsent_settings}}',
                [
                    'id' => $this->primaryKey(),
                    'settings_json' => $this->json()->null(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                ]
            );
        }

        if ($tableCookies === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%freddiescookieconsent_cookies}}',
                [
                    'id' => $this->primaryKey(),
                    'cookie_name' => $this->string()->notNull(),
                    'cookie_expiry' => $this->string()->null(),
                    'cookie_description' => $this->string()->null(),
                    'cookie_section' => $this->integer()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                ]
            );
        }

        if ($tableCookiesSections === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%freddiescookieconsent_cookies_sections}}',
                [
                    'id' => $this->primaryKey(),
                    'section_handle' => $this->string()->null(),
                    'section_name' => $this->string()->null(),
                    'section_on' => $this->boolean()->null(),
                    'section_required' => $this->boolean()->null(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                ]
            );
        }

        if ($tableCookiesAccept === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%freddiescookieconsent_cookies_accept}}',
                [
                    'id' => $this->primaryKey(),
                    'session_id' => $this->string()->notNull(),
                    'allowed' => $this->boolean()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    public function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName('{{%freddiescookieconsent_cookies}}', 'cookie_name', true),
            '{{%freddiescookieconsent_cookies}}',
            'cookie_name',
            true
        );

        $this->createIndex(
            $this->db->getIndexName('{{%freddiescookieconsent_cookies_sections}}', 'section_handle', true),
            '{{%freddiescookieconsent_cookies_sections}}',
            'section_handle',
            true
        );

        $this->createIndex(
            $this->db->getIndexName('{{%freddiescookieconsent_cookies_sections}}', 'section_name', true),
            '{{%freddiescookieconsent_cookies_sections}}',
            'section_name',
            true
        );

        $this->createIndex(
            $this->db->getIndexName('{{%freddiescookieconsent_cookies_accept}}', 'section_id', true),
            '{{%freddiescookieconsent_cookies_accept}}',
            'session_id',
            true
        );

        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%freddiescookieconsent_settings}}', 'siteId'),
            '{{%freddiescookieconsent_settings}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%freddiescookieconsent_cookies}}', 'siteId'),
            '{{%freddiescookieconsent_cookies}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%freddiescookieconsent_cookies}}', 'siteId'),
            '{{%freddiescookieconsent_cookies}}',
            'cookie_section',
            '{{%freddiescookieconsent_cookies_sections}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%freddiescookieconsent_cookies_sections}}', 'siteId'),
            '{{%freddiescookieconsent_cookies_sections}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%freddiescookieconsent_cookies_accept}}', 'siteId'),
            '{{%freddiescookieconsent_cookies_accept}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData()
    {
        $this->insert(
            '{{%freddiescookieconsent_cookies_sections}}',
            [
                'section_handle' => 'necessary',
                'section_name' => 'Necessary',
                'section_on' => true,
                'section_required' => true,
                'siteId' => 1
            ],
            true
        );

        $this->insert(
            '{{%freddiescookieconsent_cookies_sections}}',
            [
                'section_handle' => 'preferences',
                'section_name' => 'Preferences',
                'section_on' => false,
                'section_required' => false,
                'siteId' => 1
            ],
            true
        );

        $this->insert(
            '{{%freddiescookieconsent_cookies_sections}}',
            [
                'section_handle' => 'statistics',
                'section_name' => 'Statistics',
                'section_on' => false,
                'section_required' => false,
                'siteId' => 1
            ]
        );

        $this->insert(
            '{{%freddiescookieconsent_cookies_sections}}',
            [
                'section_handle' => 'marketing',
                'section_name' => 'Marketing',
                'section_on' => false,
                'section_required' => false,
                'siteId' => 1
            ],
            true
        );
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%freddiescookieconsent_settings}}');
        $this->dropTableIfExists('{{%freddiescookieconsent_cookies}}');
        $this->dropTableIfExists('{{%freddiescookieconsent_cookies_sections}}');
        $this->dropTableIfExists('{{%freddiescookieconsent_cookies_accept}}');
    }
}
