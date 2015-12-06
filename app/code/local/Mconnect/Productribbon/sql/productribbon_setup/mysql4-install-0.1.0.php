<?php

$installer = $this;

$installer->startSetup();

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('productribbon')};
CREATE TABLE {$this->getTable('productribbon')} (
  `productribbon_id` int(11) unsigned NOT NULL auto_increment,
  `sku` varchar(255) NOT NULL default '',
  `product_date` date NULL,
  `view_status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`productribbon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('productribbonbest')};
CREATE TABLE {$this->getTable('productribbonbest')} (
  `productribbon_id` int(11) unsigned NOT NULL auto_increment,
  `sku` varchar(255) NOT NULL default '',
  `product_date` date NULL,
  `view_status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`productribbon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("-- DROP TABLE IF EXISTS {$this->getTable('productribbonrivewed')};
CREATE TABLE {$this->getTable('productribbonrivewed')} (
  `productribbon_id` int(11) unsigned NOT NULL auto_increment,
  `sku` varchar(255) NOT NULL default '',
  `product_date` date NULL,
  `view_status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`productribbon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','productribbon/mconnectnewproductarrival/number_for_display_ribbon_new_product_hidden', 'NULL');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','productribbon/mconnectbestproduct/number_for_display_ribbon_best_product_hidden', 'NULL');");
$installer->run("INSERT INTO {$this->getTable('core_config_data')} (`scope`,`scope_id`,`path`,`value`) values ('default','0','productribbon/mconnectreviewproduct/number_for_display_ribbon_review_product_hidden', 'NULL');");
$installer->endSetup(); 