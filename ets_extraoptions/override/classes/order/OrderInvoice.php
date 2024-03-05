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
 
class OrderInvoice extends OrderInvoiceCore
{
    public function getProducts($products = false, $selected_products = false, $selected_qty = false)
    {
        $order_details = parent::getProducts($products,$selected_products,$selected_qty);
        if($order_details)
        {
            foreach($order_details as &$order_detail)
            {
                if($custom_price = Db::getInstance()->getValue('SELECT price_without_reduction FROM  `'._DB_PREFIX_.'ets_eto_order` WHERE id_order="'.(int)$order_detail['id_order'].'" AND id_product="'.(int)$order_detail['product_id'].'" AND id_product_attribute="'.(int)$order_detail['product_attribute_id'].'"'))
                {
                    if($order_detail['reduction_amount_tax_excl']<=0 && $order_detail['reduction_percent'] <100)
                    {
                        $unit_price_tax_excl_including_ecotax = $order_detail['unit_price_tax_excl_including_ecotax'] - $custom_price;
                        $unit_price_tax_excl_before_specific_price = (100 * $unit_price_tax_excl_including_ecotax) / (100 - $order_detail['reduction_percent']);
                        $order_detail['reduction_amount_tax_excl'] = $unit_price_tax_excl_before_specific_price - $unit_price_tax_excl_including_ecotax;
                    }
                }
            }
        }
        return $order_details;
    }
}