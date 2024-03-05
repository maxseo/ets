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
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class Ets_eto_combination_class extends ObjectModel
{
    public $date_add;
    public static $definition = array(
        'table' => 'ets_eto_combination',
        'primary' => 'id_combination',
        'multilang' => false,
        'fields' => array(
            'date_add' => array('type' => self::TYPE_DATE),
        ),
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    public static function processChangeCustomAttribute($id_product,$id_product_attribute,$quantity,$id_combination)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'ets_eto_cart` WHERE id_cart="'.(int)Context::getContext()->cart->id.'" AND id_product="'.(int)$id_product.'" AND id_product_attribute="'.(int)$id_product_attribute.'" AND id_combination='.(int)$id_combination))
        {
            return Db::getInstance()->insert('ets_eto_cart',array(
                'id_cart' => Context::getContext()->cart->id,
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'quantity' => $quantity,
                'id_combination'=> $id_combination,
                'date_add' => date('Y-m-d H:i:s')
            )
            );
        }
        else
        {
            $op = Tools::getValue('op');
            return Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_eto_cart` SET quantity=quantity '.($op =='down' ? '-':'+').' '.(int)$quantity.', date_add ="'.pSQL(date('Y-m-d H:i:s')).'" WHERE id_cart="'.(int)Context::getContext()->cart->id.'" AND id_product="'.(int)$id_product.'" AND id_product_attribute="'.(int)$id_product_attribute.'" AND id_combination='.(int)$id_combination);
        }	
    }
    public static function getIdCombinationByAttributes($attributes)
    {
        if($attributes)
        {
            $sql =' SELECT id_combination
            FROM `'._DB_PREFIX_.'ets_eto_attr_combination`
            GROUP BY id_combination
            HAVING GROUP_CONCAT(id_ets_eto_attr ORDER BY id_ets_eto_attr ASC) = "'.implode(',',array_map('intval',$attributes)).'"';
            if($id_combination = Db::getInstance()->getValue($sql))
            {
                return $id_combination;
            }
            else
            {
                $combinationObj = new Ets_eto_combination_class();
                if($combinationObj->add())
                {
                    foreach($attributes as $id_attribute)
                    {
                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_eto_attr_combination`(id_combination,id_ets_eto_attr) VALUES("'.(int)$combinationObj->id.'","'.(int)$id_attribute.'")');
                    }
                    return $combinationObj->id;
                }
            }
        }
        else
            return false;
    }
    public static function getPriceProductCustom($id_product,$id_combination,$withTax = true,$withDiscount= true,$onlyNoDiscount=false)
    {
        $price =0;
        $attributes = Db::getInstance()->executeS('SELECT id_ets_eto_attr FROM  `'._DB_PREFIX_.'ets_eto_attr_combination` WHERE id_combination='.(int)$id_combination);
        if($attributes)
        {
            foreach($attributes as $attribute)
            {
                $price += Ets_eto_attribute_class::getPriceProductAttributeCustom($id_product,$attribute['id_ets_eto_attr'],$withTax,$withDiscount,$onlyNoDiscount);
            
            }
            return $price;
        }
        return false;
    }
    public static function getAttributeName($id_combination)
    {
        $sql = 'SELECT GROUP_CONCAT(" ",al.name) FROM 
         `'._DB_PREFIX_.'ets_eto_attr` a 
        INNER JOIN  `'._DB_PREFIX_.'ets_eto_attr_combination` ac ON (ac.id_ets_eto_attr= a.id_ets_eto_attr AND ac.id_combination="'.(int)$id_combination.'")
        LEFT JOIN  `'._DB_PREFIX_.'ets_eto_attr_lang` al ON (a.id_ets_eto_attr=al.id_ets_eto_attr AND al.id_lang="'.(int)Context::getContext()->language->id.'")
        ';
        return Db::getInstance()->getValue($sql);
    }
}