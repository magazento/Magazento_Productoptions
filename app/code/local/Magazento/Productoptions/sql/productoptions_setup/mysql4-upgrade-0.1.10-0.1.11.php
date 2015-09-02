<?php

$installer = $this;

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer->startSetup();

$installer->run("CREATE TABLE IF NOT EXISTS {$this->getTable('productoptions/productoptions_stores')} (
  `productoptions_id` smallint(6) NOT NULL,
  `store_id` smallint(11) NOT NULL,
  PRIMARY KEY (`productoptions_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
