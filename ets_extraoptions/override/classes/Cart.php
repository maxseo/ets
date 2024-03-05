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

class Cart extends CartCore
{
    public $_products;
    public $shouldSplitGiftProductsQuantity ;
    public function getOrderTotal(
        $withTaxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = false,
        bool $keepOrderPrices = false
    ){
        $total = parent::getOrderTotal($withTaxes,$type,$products,$id_carrier,$use_cache,$keepOrderPrices);
        if($type!=Cart::BOTH && $type!=Cart::ONLY_PRODUCTS)
            return $total;
        if(!$keepOrderPrices && ($type== Cart::BOTH  || $type==Cart::ONLY_PRODUCTS) && Module::isEnabled('ets_extraoptions'))
        {
            $priceCustom  = Module::getInstanceByName('ets_extraoptions')->getPriceAttributeCustom($this,$withTaxes);
            return $total + $priceCustom;
        }
        else
            return $total;
    }
    public function getProducts($refresh = true, $id_product = false, $id_country = null, $fullInfos = true,bool $keepOrderPrices = false,$default = false)
    {
        if($keepOrderPrices || $default || !Module::isEnabled('ets_extraoptions'))
            return parent::getProducts($refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
        else
        {
            $this->_products = Module::getInstanceByName('ets_extraoptions')->getProducts($this,$refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
            return $this->_products;
        }
    }
    public function applyProductCalculations($row, $shopContext, $productQuantity = null, bool $keepOrderPrices = false)
    {
        return parent::applyProductCalculations($row,$shopContext,$productQuantity,$keepOrderPrices);
    }
}