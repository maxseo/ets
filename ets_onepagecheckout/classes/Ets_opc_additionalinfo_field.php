<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
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
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_')) {
    exit();
}

class Ets_opc_additionalinfo_field extends ObjectModel
{
    public $sort;
    public $type;
    public $required;
    public $enable;
    public $deleted;
    public $title;
    public $description;
    public $options;
    public static $definition = array(
        'table' => 'ets_opc_additionalinfo_field',
        'primary' => 'id_ets_opc_additionalinfo_field',
        'multilang' => true,
        'fields' => array(
            'type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'required' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'enable' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'sort' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
            'options' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString'
            ),
        )
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        $this->context= Context::getContext();
	}
	public static function updatePositions($fields)
    {
        if($fields)
        {
            foreach($fields as $key=> $id_field)
            {
                $sort = $key+1;
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_opc_additionalinfo_field` SET sort ="'.(int)$sort.'" WHERE id_ets_opc_additionalinfo_field="'.(int)$id_field.'"');
            }
        }
        return true;
    }
    public static function deleteAllField()
    {
        return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_opc_additionalinfo_field` SET deleted=1');
    }
    public static function getListField($id_lang = null,$active=false){
        $sql = "SELECT f.*,f.id_ets_opc_additionalinfo_field as id, fl.title, fl.description, fl.id_lang,fl.options FROM `"._DB_PREFIX_."ets_opc_additionalinfo_field` f
        JOIN `"._DB_PREFIX_."ets_opc_additionalinfo_field_lang` fl ON f.id_ets_opc_additionalinfo_field = fl.id_ets_opc_additionalinfo_field
        WHERE f.deleted=0 ".($active ? ' AND f.enable=1':'').($id_lang ? " AND fl.id_lang=".(int)$id_lang:""). " ORDER BY f.sort ASC";
        $fields = Db::getInstance()->executeS($sql);
        if(!$id_lang && $fields){
            $results = array();
            foreach ($fields as $field) {
                $results[$field['id_ets_opc_additionalinfo_field']]['id'] = $field['id_ets_opc_additionalinfo_field'];
                $results[$field['id_ets_opc_additionalinfo_field']]['type'] = $field['type'];
                $results[$field['id_ets_opc_additionalinfo_field']]['enable'] = $field['enable'];
                $results[$field['id_ets_opc_additionalinfo_field']]['description'][$field['id_lang']] = $field['description'];
                $results[$field['id_ets_opc_additionalinfo_field']]['options'][$field['id_lang']] = $field['options'];
                $results[$field['id_ets_opc_additionalinfo_field']]['required'] = $field['required'];
                $results[$field['id_ets_opc_additionalinfo_field']]['title'][$field['id_lang']] = $field['title'];
            }
            return $results;
        }
        return $fields;
    }
}
