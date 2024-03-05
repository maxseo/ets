<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2022 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_1_0_4($module)
{
    $sqls= array();
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_eto_attr_group` ( 
    `id_ets_eto_attr_group` INT(11) NOT NULL AUTO_INCREMENT ,
    `id_shop` INT(11), 
    `position` INT(11), 
    PRIMARY KEY (`id_ets_eto_attr_group`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_eto_attr_group_lang` ( 
    `id_ets_eto_attr_group` INT(11) NOT NULL ,
    `id_lang` INT(11) NOT NULL, 
    `name` VARCHAR(300), 
    PRIMARY KEY (`id_ets_eto_attr_group`,`id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
    if(!$module->checkCreatedColumn('ets_eto_attr','display_by_option_group'))
    {
       $sqls[]= 'ALTER TABLE `'._DB_PREFIX_.'ets_eto_attr` ADD `display_by_option_group` INT(1) NOT NULL';
       $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_eto_attr` ADD INDEX (`display_by_option_group`)';
    }
    if(!$module->checkCreatedColumn('ets_eto_attr','id_ets_eto_attr_group'))
    {
       $sqls[]= 'ALTER TABLE `'._DB_PREFIX_.'ets_eto_attr` ADD `id_ets_eto_attr_group` INT(11) NOT NULL';
       $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_eto_attr` ADD INDEX (`id_ets_eto_attr_group`)';
    }
    foreach($sqls as $sql)
        Db::getInstance()->execute($sql);
    return true;
} 