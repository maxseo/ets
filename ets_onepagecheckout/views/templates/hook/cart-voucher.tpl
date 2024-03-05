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
{if isset($cart.vouchers.allowed) &&  $cart.vouchers.allowed}
    <div class="block-promo">
      <div class="cart-voucher ets-cart-voucher">
        {if $cart.vouchers.added}
            <ul class="promo-name card-block">
              {foreach from=$cart.vouchers.added item=voucher}
                <li class="cart-summary-line">
                  <span class="label">{$voucher.name|escape:'html':'UTF-8'}</span>
                  <div class="float-xs-right">
                    <span>{$voucher.reduction_formatted|escape:'html':'UTF-8'}</span>
                    <a href="{$voucher.delete_url|escape:'html':'UTF-8'}" data-link-action="ets-remove-voucher"><i class="material-icons">&#xE872;</i></a>
                  </div>
                </li>
              {/foreach}
              {hook h='displayCustomDiscountRule' added=true}
            </ul>
        {else}
            {hook h='displayCustomDiscountRule' added=false} 
        {/if}
        <div id="promo-code" >
            <div class="promo-code">
                  <form action="{$urls.pages.cart|escape:'html':'UTF-8'}" data-link-action="add-voucher" method="post">
                    <span class="ets_icon_scices ets_icon_svg">
                        <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M960 896q26 0 45 19t19 45-19 45-45 19-45-19-19-45 19-45 45-19zm300 64l507 398q28 20 25 56-5 35-35 51l-128 64q-13 7-29 7-17 0-31-8l-690-387-110 66q-8 4-12 5 14 49 10 97-7 77-56 147.5t-132 123.5q-132 84-277 84-136 0-222-78-90-84-79-207 7-76 56-147t131-124q132-84 278-84 83 0 151 31 9-13 22-22l122-73-122-73q-13-9-22-22-68 31-151 31-146 0-278-84-82-53-131-124t-56-147q-5-59 15.5-113t63.5-93q85-79 222-79 145 0 277 84 83 52 132 123t56 148q4 48-10 97 4 1 12 5l110 66 690-387q14-8 31-8 16 0 29 7l128 64q30 16 35 51 3 36-25 56zm-681-260q46-42 21-108t-106-117q-92-59-192-59-74 0-113 36-46 42-21 108t106 117q92 59 192 59 74 0 113-36zm-85 745q81-51 106-117t-21-108q-39-36-113-36-100 0-192 59-81 51-106 117t21 108q39 36 113 36 100 0 192-59zm178-613l96 58v-11q0-36 33-56l14-8-79-47-26 26q-3 3-10 11t-12 12q-2 2-4 3.5t-3 2.5zm224 224l96 32 736-576-128-64-768 431v113l-160 96 9 8q2 2 7 6 4 4 11 12t11 12l26 26zm704 416l128-64-520-408-177 138q-2 3-13 7z"/></svg>
                    </span>
                      <input type="hidden" name="token" value="{$static_token|escape:'html':'UTF-8'}" />
                    <input type="hidden" name="addDiscount" value="1" />
                    <input class="promo-input" type="text" name="discount_name" placeholder="{l s='Discount code' mod='ets_onepagecheckout'}" />
                    <button type="submit" class="btn btn-primary"><span>{l s='Add' mod='ets_onepagecheckout'}</span></button>
                  </form>
                  <div class="alert alert-danger js-error" role="alert">
                    <i class="opc_info_icon"></i><span class="ets-ml-1 js-error-text"></span>
                  </div>
            </div>
        </div>
        {if $cart.discounts|count > 0}
          <p class="block-promo promo-highlighted">
            {l s='Take advantage of our exclusive offers:' mod='ets_onepagecheckout'}
          </p>
          <ul class="js-discount card-block promo-discounts">
            {foreach from=$cart.discounts item=discount}
              <li class="cart-summary-line">
                <span class="label">
                  <span class="code">{$discount.code|escape:'html':'UTF-8'}</span> - {$discount.name|escape:'html':'UTF-8'}
                </span>
              </li>
            {/foreach}
          </ul>
        {/if}
      </div>
    </div>
{/if}