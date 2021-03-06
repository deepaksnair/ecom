<?php

namespace Dotdigitalgroup\Email\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create Table
         */
        $this->createContactTable($installer);
        $this->createOrderTable($installer);
        $this->createCampaignTable($installer);
        $this->createReviewTable($installer);
        $this->createWishlistTable($installer);
        $this->createCatalogTable($installer);
        $this->createRuleTable($installer);
        $this->createImporterTable($installer);
        $this->createAutomationTable($installer);

        /**
         * Modify table
         */
        $this->addColumnToAdminUserTable($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createContactTable($installer)
    {
        $this->dropContactTableIfExists($installer);

        $contactTable = $installer->getConnection()->newTable(
            $installer->getTable('email_contact')
        );

        $contactTable = $this->addColumnsToContactTable($contactTable);
        $contactTable = $this->addIndexesToContactTable($installer, $contactTable);

        $contactTable->addForeignKey(
            $installer->getFkName(
                'email_contact',
                'website_id',
                'store_website',
                'website_id'
            ),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $contactTable->setComment('Connector Contacts');
        $installer->getConnection()->createTable($contactTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropContactTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_contact')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_contact')
            );
        }
    }

    /**
     * @param Table $contactTable
     * @return \Magento\Framework\DB\Ddl\Table
     */
    private function addColumnsToContactTable($contactTable)
    {
        return $contactTable->addColumn(
            'email_contact_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
            ->addColumn(
                'is_guest',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Is Guest'
            )
            ->addColumn(
                'contact_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                15,
                ['unsigned' => true, 'nullable' => true],
                'Connector Contact ID'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Website ID'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Customer Email'
            )
            ->addColumn(
                'is_subscriber',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Is Subscriber'
            )
            ->addColumn(
                'subscriber_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Subscriber status'
            )
            ->addColumn(
                'email_imported',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Is Imported'
            )
            ->addColumn(
                'subscriber_imported',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Subscriber Imported'
            )
            ->addColumn(
                'suppressed',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Is Suppressed'
            );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $contactTable
     * @return \Magento\Framework\DB\Ddl\Table
     */
    private function addIndexesToContactTable($installer, $contactTable)
    {
        return $contactTable->addIndex(
            $installer->getIdxName('email_contact', ['email_contact_id']),
            ['email_contact_id']
        )
            ->addIndex(
                $installer->getIdxName('email_contact', ['is_guest']),
                ['is_guest']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['customer_id']),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['website_id']),
                ['website_id']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['is_subscriber']),
                ['is_subscriber']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['subscriber_status']),
                ['subscriber_status']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['email_imported']),
                ['email_imported']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['subscriber_imported']),
                ['subscriber_imported']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['suppressed']),
                ['suppressed']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['email']),
                ['email']
            )
            ->addIndex(
                $installer->getIdxName('email_contact', ['contact_id']),
                ['contact_id']
            );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createOrderTable($installer)
    {
        $this->dropOrderTableIfExists($installer);

        $orderTable = $installer->getConnection()->newTable(
            $installer->getTable('email_order')
        );

        $orderTable = $this->addColumnsToOrderTable($orderTable);
        $orderTable = $this->addIndexesToOrderTable($installer, $orderTable);

        $orderTable->addForeignKey(
            $installer->getFkName(
                $installer->getTable('email_order'),
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $orderTable->addForeignKey(
            $installer->getFkName(
                $installer->getTable('email_order'),
                'order_id',
                'sales_order',
                'entity_id'
            ),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $orderTable->setComment('Transactional Order Data');
        $installer->getConnection()->createTable($orderTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropOrderTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_order')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_order')
            );
        }
    }

    /**
     * @param Table $orderTable
     * @return Table
     */
    private function addColumnsToOrderTable($orderTable)
    {
        return $orderTable->addColumn(
            'email_order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order ID'
        )
        ->addColumn(
            'order_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Order Status'
        )
        ->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Sales Quote ID'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store ID'
        )
        ->addColumn(
            'email_imported',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Is Order Imported'
        )
        ->addColumn(
            'modified',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Is Order Modified'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $orderTable
     * @return Table
     */
    private function addIndexesToOrderTable($installer, $orderTable)
    {
        return $orderTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['store_id']
            ),
            ['store_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['quote_id']
            ),
            ['quote_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['email_imported']
            ),
            ['email_imported']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['order_status']
            ),
            ['order_status']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['modified']
            ),
            ['modified']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['updated_at']
            ),
            ['updated_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_order'),
                ['created_at']
            ),
            ['created_at']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createCampaignTable($installer)
    {
        $this->dropCampaignTableIfExists($installer);

        $campaignTable = $installer->getConnection()->newTable(
            $installer->getTable('email_campaign')
        );

        $campaignTable = $this->addColumnsToCampaignTable($campaignTable);
        $campaignTable = $this->addIndexesToCampaignTable($installer, $campaignTable);

        $campaignTable->addForeignKey(
            $installer->getFkName(
                $installer->getTable('email_campaign'),
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $campaignTable->setComment('Connector Campaigns');
        $installer->getConnection()->createTable($campaignTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropCampaignTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_campaign')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_campaign')
            );
        }
    }

    /**
     * @param Table $campaignTable
     * @return Table
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addColumnsToCampaignTable($campaignTable)
    {
        return $campaignTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'campaign_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Campaign ID'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Contact Email'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Customer ID'
        )
        ->addColumn(
            'sent_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Send Date'
        )
        ->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['unsigned' => true, 'nullable' => false],
            'Order Increment ID'
        )
        ->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Sales Quote ID'
        )
        ->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Error Message'
        )
        ->addColumn(
            'checkout_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Checkout Method Used'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store ID'
        )
        ->addColumn(
            'event_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Event Name'
        )
        ->addColumn(
            'send_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Send Id'
        )
        ->addColumn(
            'send_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Campaign send status'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $campaignTable
     * @return Table
     */
    private function addIndexesToCampaignTable($installer, $campaignTable)
    {
        return $campaignTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['store_id']
            ),
            ['store_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['campaign_id']
            ),
            ['campaign_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['email']
            ),
            ['email']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['send_id']
            ),
            ['send_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['send_status']
            ),
            ['send_status']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['created_at']
            ),
            ['created_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['updated_at']
            ),
            ['updated_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['sent_at']
            ),
            ['sent_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['event_name']
            ),
            ['event_name']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['message']
            ),
            ['message']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['quote_id']
            ),
            ['quote_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_campaign'),
                ['customer_id']
            ),
            ['customer_id']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createReviewTable($installer)
    {
        $this->dropReviewTableIfExists($installer);

        $reviewTable = $installer->getConnection()->newTable(
            $installer->getTable('email_review')
        );

        $reviewTable = $this->addColumnsToReviewTable($reviewTable);
        $reviewTable = $this->addIndexesToReviewTable($installer, $reviewTable);

        $reviewTable->setComment('Connector Reviews');
        $installer->getConnection()->createTable($reviewTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropReviewTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_review')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_review')
            );
        }
    }

    /**
     * @param Table $reviewTable
     * @return Table
     */
    private function addColumnsToReviewTable($reviewTable)
    {
        return $reviewTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'review_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Review Id'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Customer ID'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
        ->addColumn(
            'review_imported',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Review Imported'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $reviewTable
     * @return Table
     */
    private function addIndexesToReviewTable($installer, $reviewTable)
    {
        return $reviewTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_review'),
                ['review_id']
            ),
            ['review_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_review'),
                ['customer_id']
            ),
            ['customer_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_review'),
                ['store_id']
            ),
            ['store_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_review'),
                ['review_imported']
            ),
            ['review_imported']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_review'),
                ['created_at']
            ),
            ['created_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_review'),
                ['updated_at']
            ),
            ['updated_at']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createWishlistTable($installer)
    {
        $this->dropWishlistTableIfExists($installer);

        $wishlistTable = $installer->getConnection()->newTable(
            $installer->getTable('email_wishlist')
        );

        $wishlistTable = $this->addColumnsToWishlistTable($wishlistTable);
        $wishlistTable = $this->addIndexesToWishlistTable($installer, $wishlistTable);

        $wishlistTable->setComment('Connector Wishlist');
        $installer->getConnection()->createTable($wishlistTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropWishlistTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_wishlist')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_wishlist')
            );
        }
    }

    /**
     * @param Table $wishlistTable
     * @return Table
     */
    private function addColumnsToWishlistTable($wishlistTable)
    {
        return $wishlistTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'wishlist_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Wishlist Id'
        )
        ->addColumn(
            'item_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Item Count'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            11,
            ['unsigned' => true, 'nullable' => false],
            'Customer ID'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
        ->addColumn(
            'wishlist_imported',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Wishlist Imported'
        )
        ->addColumn(
            'wishlist_modified',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Wishlist Modified'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $wishlistTable
     * @return Table
     */
    private function addIndexesToWishlistTable($installer, $wishlistTable)
    {
        return $wishlistTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['wishlist_id']
            ),
            ['wishlist_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['item_count']
            ),
            ['item_count']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['customer_id']
            ),
            ['customer_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['wishlist_modified']
            ),
            ['wishlist_modified']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['wishlist_imported']
            ),
            ['wishlist_imported']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['created_at']
            ),
            ['created_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['updated_at']
            ),
            ['updated_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_wishlist'),
                ['store_id']
            ),
            ['store_id']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createCatalogTable($installer)
    {
        $this->dropCatalogTableIfExists($installer);

        $catalogTable = $installer->getConnection()->newTable(
            $installer->getTable('email_catalog')
        );

        $catalogTable = $this->addColumnsToCatalogTable($catalogTable);
        $catalogTable = $this->addIndexesToCatalogTable($installer, $catalogTable);
        $catalogTable->addForeignKey(
            $installer->getFkName(
                'email_catalog',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $catalogTable->setComment('Connector Catalog');
        $installer->getConnection()->createTable($catalogTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return null
     */
    private function dropCatalogTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_catalog')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_catalog')
            );
        }
    }

    /**
     * @param Table $catalogTable
     * @return Table
     */
    private function addColumnsToCatalogTable($catalogTable)
    {
        return $catalogTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )
        ->addColumn(
            'imported',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Product Imported'
        )
        ->addColumn(
            'modified',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Product Modified'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $catalogTable
     * @return Table
     */
    private function addIndexesToCatalogTable($installer, $catalogTable)
    {
        return $catalogTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_catalog'),
                ['product_id']
            ),
            ['product_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_catalog'),
                ['imported']
            ),
            ['imported']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_catalog'),
                ['modified']
            ),
            ['modified']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_catalog'),
                ['created_at']
            ),
            ['created_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_catalog'),
                ['updated_at']
            ),
            ['updated_at']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createRuleTable($installer)
    {
        $this->dropRuleTableIfExists($installer);

        $ruleTable = $installer->getConnection()->newTable(
            $installer->getTable('email_rules')
        );

        $ruleTable = $this->addColumnsToRulesTable($ruleTable);

        $ruleTable->setComment('Connector Rules');
        $installer->getConnection()->createTable($ruleTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropRuleTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_rules')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_rules')
            );
        }
    }

    /**
     * @param Table $ruleTable
     * @return Table
     */
    private function addColumnsToRulesTable($ruleTable)
    {
        return $ruleTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Rule Name'
        )
        ->addColumn(
            'website_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '0'],
            'Website Id'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Rule Type'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Status'
        )
        ->addColumn(
            'combination',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Rule Condition'
        )
        ->addColumn(
            'condition',
            \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
            null,
            ['nullable' => false, 'default' => ''],
            'Rule Condition'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createImporterTable($installer)
    {
        $this->dropImporterTableIfExists($installer);

        $importerTable = $installer->getConnection()->newTable(
            $installer->getTable('email_importer')
        );

        $importerTable = $this->addColumnsToImporterTable($importerTable);
        $importerTable = $this->addIndexesToImporterTable($installer, $importerTable);

        $importerTable ->setComment('Email Importer');
        $installer->getConnection()->createTable($importerTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropImporterTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_importer')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_importer')
            );
        }
    }

    /**
     * @param Table $importerTable
     * @return Table
     */
    private function addColumnsToImporterTable($importerTable)
    {
        return $importerTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'import_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Import Type'
        )
        ->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['nullable' => false, 'default' => '0'],
            'Website Id'
        )
        ->addColumn(
            'import_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Import Status'
        )
        ->addColumn(
            'import_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Import Id'
        )
        ->addColumn(
            'import_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
            '2M',
            ['nullable' => false, 'default' => ''],
            'Import Data'
        )
        ->addColumn(
            'import_mode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Import Mode'
        )
        ->addColumn(
            'import_file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Import File'
        )
        ->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => ''],
            'Error Message'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        )
        ->addColumn(
            'import_started',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Import Started'
        )
        ->addColumn(
            'import_finished',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Import Finished'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $importerTable
     * @return Table
     */
    private function addIndexesToImporterTable($installer, $importerTable)
    {
        return $importerTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['import_type']
            ),
            ['import_type']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['website_id']
            ),
            ['website_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['import_status']
            ),
            ['import_status']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['import_mode']
            ),
            ['import_mode']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['created_at']
            ),
            ['created_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['updated_at']
            ),
            ['updated_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['import_id']
            ),
            ['import_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['import_started']
            ),
            ['import_started']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_importer'),
                ['import_finished']
            ),
            ['import_finished']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function createAutomationTable($installer)
    {
        $this->dropAutomationTableIfExists($installer);

        $automationTable = $installer->getConnection()->newTable(
            $installer->getTable('email_automation')
        );

        $automationTable = $this->addColumnsToAutomationTable($automationTable);
        $automationTable = $this->addIndexesToAutomationTable($installer, $automationTable);

        $automationTable->setComment('Automation Status');
        $installer->getConnection()->createTable($automationTable);
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function dropAutomationTableIfExists($installer)
    {
        if ($installer->getConnection()->isTableExists(
            $installer->getTable('email_automation')
        )
        ) {
            $installer->getConnection()->dropTable(
                $installer->getTable('email_automation')
            );
        }
    }

    /**
     * @param Table $automationTable
     * @return Table
     */
    private function addColumnsToAutomationTable($automationTable)
    {
        return $automationTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'primary' => true,
                'identity' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Primary Key'
        )
        ->addColumn(
            'automation_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Automation Type'
        )
        ->addColumn(
            'store_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Automation Type'
        )
        ->addColumn(
            'enrolment_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Entrolment Status'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Email'
        )
        ->addColumn(
            'type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Type ID'
        )
        ->addColumn(
            'program_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Program ID'
        )
        ->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Website Id'
        )
        ->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Message'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update Time'
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @param Table $automationTable
     * @return Table
     */
    private function addIndexesToAutomationTable($installer, $automationTable)
    {
        return $automationTable->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['automation_type']
            ),
            ['automation_type']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['enrolment_status']
            ),
            ['enrolment_status']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['type_id']
            ),
            ['type_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['email']
            ),
            ['email']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['program_id']
            ),
            ['program_id']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['created_at']
            ),
            ['created_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['updated_at']
            ),
            ['updated_at']
        )
        ->addIndex(
            $installer->getIdxName(
                $installer->getTable('email_automation'),
                ['website_id']
            ),
            ['website_id']
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     *
     * @return null
     */
    private function addColumnToAdminUserTable($installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable('admin_user'),
            'refresh_token',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 256,
            'nullable' => true,
            'default' => null,
            'comment' => 'Email connector refresh token',
            ]
        );
    }
}
