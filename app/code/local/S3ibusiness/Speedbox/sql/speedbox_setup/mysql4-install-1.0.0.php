<?php

/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS speedbox_zones;
CREATE TABLE speedbox_zones(
`id_zone` int(11) unsigned NOT NULL auto_increment,
`nom` varchar(255) NOT NULL default'',
`villes` varchar(255) NOT NULL default'',
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY(`id_zone`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS speedbox_frais_port;
CREATE TABLE speedbox_frais_port (
`id` int(11) unsigned NOT NULL auto_increment,
`id_zone`  int(11) NOT NULL default '0' ,
`condition` varchar(255) NOT NULL default '',
`min` varchar(255) NOT NULL default'',
`max` varchar(255) NOT NULL  default'',
`cout` varchar(255) NOT NULL default'',
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY(`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->addAttribute("order", "speedbox_numero_colis", array("type" => "text"));
$installer->addAttribute("order", "speedbox_statut_colis", array("type" => "text"));
$installer->addAttribute("order", "speedbox_code_barre_colis", array("type" => "text"));
$installer->addAttribute("order", "speedbox_selected_relais_id", array("type" => "text"));
$installer->addAttribute("order", "speedbox_selected_relais_infos", array("type" => "text"));
$installer->endSetup();
