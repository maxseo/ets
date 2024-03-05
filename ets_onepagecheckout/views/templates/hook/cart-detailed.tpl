{*
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
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<div class="cart-overview js-cart" data-refresh-url="{$link->getModuleLink('ets_onepagecheckout','order',['ajax' => true, 'action' => 'refresh'])|escape:'html':'UTF-8'}">
    {if isset($cart.products) &&  $cart.products}
    <ul class="cart-items">
      {foreach from=$cart.products item=product}
        <li class="cart-item">
            {if Module::isEnabled('ph_extend_support')}
                {include file='module:ph_extend_support/views/templates/hook/checkout/cart-detailed-product-line.tpl' product=$product}
            {else}
                {include file='module:ets_onepagecheckout/views/templates/hook/cart-detailed-product-line.tpl' product=$product}
            {/if}
        </li>
        {if is_array($product.customizations) && Ets_onepagecheckout::validateArray($product.customizations) && $product.customizations|count >1}<hr>{/if}
      {/foreach}
    </ul>
    {else}
      <span class="no-items">{l s='There are no more items in your cart' mod='ets_onepagecheckout'}</span>
    {/if}
</div>