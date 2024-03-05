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
 
class CartController extends CartControllerCore
{
    public function processChangeProductInCart()
    {
        if(Module::isEnabled('ets_extraoptions'))
        {
            Module::getInstanceByName('ets_extraoptions')->validateAddCustomAttributeToCart($this->errors,$this->id_product);
        }
        parent::processChangeProductInCart();
        if(Module::isEnabled('ets_extraoptions') && !$this->errors)
        {
            Module::getInstanceByName('ets_extraoptions')->postAddCustomAttributeToCart($this->id_product,$this->id_product_attribute,$this->qty);
        }
    }
    public function displayAjaxRefresh()
    {
        if (Configuration::isCatalogMode()) {
            return;
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(Tools::jsonEncode([
            'cart_detailed' =>$this->context->smarty->fetch(_PS_MODULE_DIR_.'ets_extraoptions/views/templates/hook/checkout/cart-detailed.tpl'),//  $this->render('checkout/_partials/cart-detailed'),
            'cart_detailed_totals' => $this->render('checkout/_partials/cart-detailed-totals'),
            'cart_summary_items_subtotal' => $this->render('checkout/_partials/cart-summary-items-subtotal'),
            'cart_summary_subtotals_container' => $this->render('checkout/_partials/cart-summary-subtotals'),
            'cart_summary_totals' => $this->render('checkout/_partials/cart-summary-totals'),
            'cart_detailed_actions' => $this->render('checkout/_partials/cart-detailed-actions'),
            'cart_voucher' => $this->render('checkout/_partials/cart-voucher'),
        ]));
    }
    public function productInCartMatchesCriteria($productInCart)
    {
        return (
            !isset($this->id_product_attribute) ||
            (
                $productInCart['id_product_attribute'] == $this->id_product_attribute &&
                $productInCart['id_customization'] == $this->customization_id
            )
        ) && isset($this->id_product) && $productInCart['id_product'] == $this->id_product && $productInCart['id_combination']==(int)Tools::getValue('id_combination');
    }
}