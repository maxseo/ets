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

class Ets_eto_attribute_order_class
{
    public static function actionValidateOrder($params)
    {
        $order = $params['order'];
        $cart =  $params['cart'];
        $products = $order->getProductsDetail();
        if($products)
        {
            foreach($products as $product)
            {
                if($combinations = Db::getInstance()->executeS('SELECT id_combination,quantity FROM  `'._DB_PREFIX_.'ets_eto_cart` WHERE id_cart="'.(int)$cart->id.'" AND id_product='.(int)$product['product_id'].' AND id_product_attribute='.(int)$product['product_attribute_id']))
                {
                    foreach($combinations as $combination)
                    {
                        $id_combination = (int)$combination['id_combination'];
                        $quantity = $combination['quantity'];
                        $combinationObj = new Ets_eto_combination_class($id_combination);
                        if(Validate::isLoadedObject($combinationObj))
                        {
                            $price = (float)Tools::convertPrice(Ets_eto_combination_class::getPriceProductCustom($product['product_id'],$id_combination,false));                           
                            $price_incl = (float)Tools::convertPrice(Ets_eto_combination_class::getPriceProductCustom($product['product_id'],$id_combination,true));
                            $price_without_reduction = (float)Tools::convertPrice(Ets_eto_combination_class::getPriceProductCustom($product['product_id'],$id_combination,false,true,true));
                            if(!Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'ets_eto_order` WHERE id_order="'.(int)$order->id.'" AND id_product="'.(int)$product['product_id'].'" AND id_product_attribute="'.(int)$product['product_attribute_id'].'" AND id_combination="'.(int)$id_combination.'"'))
                            {
                                $data = array(
                                        'id_order'=>(int)$order->id,
                                        'id_product' => (int)$product['product_id'],
                                        'id_product_attribute' => (int)$product['product_attribute_id'],
                                        'quantity' => (int)$product['product_quantity'],
                                        'id_combination' => (int)$id_combination,
                                        'name' => pSQL(trim(Ets_eto_combination_class::getAttributeName($id_combination),', ')),
                                        'price' => (float)$price*$quantity,
                                        'price_incl' => $price_incl*$quantity,
                                        'price_without_reduction' => $price_without_reduction*$quantity,
                                );
                                Db::getInstance()->insert('ets_eto_order',$data);
                                Hook::exec('actionAddAttributeCustomProduct', $data, null, true);
                            }
                        }
                    }
                }
            }
        }
    }
    public static function getCustomPrice($order)
    {
        if(Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'ets_eto_order` WHERE id_order='.(int)$order->id))
            return (float)Db::getInstance()->getValue('SELECT SUM(price) FROM  `'._DB_PREFIX_.'ets_eto_order` WHERE id_order='.(int)$order->id);
        else
            return false;
    }
}