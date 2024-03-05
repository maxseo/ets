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
<div id="checkout-personal-information-step" class="checkout-step -reachable -complete -clickable"></div>
<section id="checkout-payment-step" class="checkout-step -current -reachable js-current-step">
    <div class="content">
        {hook h='displayPaymentTop'}
        <div class="payment-options">
            <form></form>
            {if $payment_methods}
                {foreach from=$payment_methods key='module_name' item='payment_method'}
                    {foreach from=$payment_method item='module'}
                        <div>
                            <div id="{$module.id|escape:'html':'UTF-8'}-container" class="payment-option clearfix">
                                <span class="custom-radio float-xs-left">
                                     <input id="{$module.id|escape:'html':'UTF-8'}"
                                            class="ps-shown-by-js {if $module.module_name}{if $payment_selected==$module.module_name}checked{/if}{else}{if $payment_selected==$module_name}checked{/if}{/if}"
                                            data-module-name="{if $module.module_name}{$module.module_name|escape:'html':'UTF-8'}{else}{$module_name|escape:'html':'UTF-8'}{/if}" name="payment-option" type="radio"
                                            value="{$module_name|escape:'html':'UTF-8'}"
                                     />
                                    <span></span>
                                </span>
                                {if $ETS_OPC_PAYMENT_LOGO_ENABLED}
								{if  $module.call_to_action_text == "Zapłać BLIK-iem"}
									<img src="/img/blik.jpg" style="width:32px;margin:0 6px 0 0" />
								{elseif $module.call_to_action_text == "Zapłać kartą"}
									<img src="/img/card.jpg" style="width:32px;margin:0 6px 0 0" />
								{else}
									<img src="{$module.logo|escape:'html':'UTF-8'}" style="width:32px;margin:0 6px 0 0" />
								{/if}
                                {/if}
                                <form method="GET" class="ps-hidden-by-js" style="display:none;">
                                    <button class="ps-hidden-by-js" type="submit" name="select_payment_option" value="{$module.id|escape:'html':'UTF-8'}">
                                        {l s='Choose' mod='ets_onepagecheckout'}
                                    </button>
                                </form>
                                <label for="{$module.id|escape:'html':'UTF-8'}">
                                    <span>{$module.call_to_action_text|escape:'html':'UTF-8'}</span>
                                </label>
                            </div>
                        </div>
                        {*
                        <div id="{$module.id|escape:'html':'UTF-8'}-additional-information" class="js-additional-information definition-list additional-information ps-hidden " {if $payment_selected==$module.module_name} style="display:block"{else}style="display: none;"{/if}>
                            {if $ETS_OPC_PAYMENT_LOGO_ENABLED}<img src="{$module.logo|escape:'html':'UTF-8'}" style="width:128px" />{/if}
                            {$module.additionalInformation nofilter}
                        </div>
                        *}
                        <div id="pay-with-{$module.id|escape:'html':'UTF-8'}-form" class="js-payment-option-form ps-hidden " {if $payment_selected==$module.module_name}  style="color:red; display:block"{else}style="display: none;"{/if}>
                            {if $module.form}
                                {$module.form nofilter}
                            {else}
                                <form id="payment-form" method="POST" action="{$module.action|escape:'html':'UTF-8'}">
                                    {if isset($module.inputs) && $module.inputs}
                                        {foreach from = $module.inputs item='input'}
                                            <input{foreach from=$input key='key' item='value'} {$key|escape:'html':'UTF-8'}="{$value|escape:'html':'UTF-8'}"{/foreach} />
                                        {/foreach}
                                    {/if}
                                    <button id="pay-with-{$module.id|escape:'html':'UTF-8'}" style="display:none" type="submit"></button>
                                </form>
                            {/if}
                        </div>
                    {/foreach}
                {/foreach}
            {else}
                <p class="alert alert-danger">{l s='Unfortunately, there are no payment methods available.' mod='ets_onepagecheckout'}</p>
            {/if}
        </div>
        {hook h='displayPaymentByBinaries'}
    </div>
</section>