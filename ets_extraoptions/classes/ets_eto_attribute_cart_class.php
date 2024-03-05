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

class Ets_eto_attribute_cart_class
{
    public static function getPriceAttributeCustom($cart,$widthTax)
    {
        $price = 0;
        $customattribute = false;
        if($cart->id)
        {
            if($products = $cart->getProducts(true,false,null,true,false,true))
            {
                foreach($products as $product)
                {
                    if($combinations = Db::getInstance()->executeS('SELECT id_combination,quantity FROM  `'._DB_PREFIX_.'ets_eto_cart` WHERE id_cart="'.(int)$cart->id.'" AND id_product='.(int)$product['id_product'].' AND id_product_attribute='.(int)$product['id_product_attribute']))
                    {
                        foreach($combinations as $combination)
                        {
                            $id_combination = (int)$combination['id_combination'];
                            $quantity = (int)$combination['quantity'];
                            if($id_combination && ($customPrice = Ets_eto_combination_class::getPriceProductCustom($product['id_product'],$id_combination,$widthTax))!==false)
                            {
                                $customattribute = true;
                                $price += $customPrice*$quantity;
                            }
                        }
                        
                    }
                }
                
            }
        }
        if($customattribute)
            return $price;
        else
            return false;
    }
    public static function deleteAttributeCustom($id_cart,$id_product,$id_product_attribute)
    {
        $id_combination = (int)Tools::getValue('id_combination');
        if($quantity = (int)Db::getInstance()->getValue('SELECT quantity FROM  `'._DB_PREFIX_.'ets_eto_cart` WHERE id_cart = "'.(int)$id_cart.'" AND id_product="'.(int)$id_product.'" AND id_product_attribute='.(int)$id_product_attribute.' AND id_combination='.(int)$id_combination))
        {
            Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'ets_eto_cart` WHERE id_cart = "'.(int)$id_cart.'" AND id_product="'.(int)$id_product.'" AND id_product_attribute='.(int)$id_product_attribute.' AND id_combination='.(int)$id_combination);
            $cart_quantity = (int)Db::getInstance()->getValue('SELECT quantity FROM  `'._DB_PREFIX_.'cart_product` WHERE id_cart = "'.(int)$id_cart.'" AND id_product="'.(int)$id_product.'" AND id_product_attribute='.(int)$id_product_attribute);
            if($cart_quantity > $quantity)
            {
                Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'cart_product` SET quantity = quantity-'.(int)$quantity.' WHERE id_cart = "'.(int)$id_cart.'" AND id_product="'.(int)$id_product.'" AND id_product_attribute='.(int)$id_product_attribute);
                return true;
            }
        }
        return false;
    }
}