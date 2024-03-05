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
{if $payments}
    <table class="payment">
        <tr>
            <td class="text-center">{l s='Payment logo' mod='ets_onepagecheckout'}</td>
            <td class="text-left">{l s='Payment name' mod='ets_onepagecheckout'}</td>
            <td class="text-left">{l s='Description' mod='ets_onepagecheckout'}</td>
            <td class="text-center">{l s='Status' mod='ets_onepagecheckout'}</td>
            <td class="text-center">{l s='Setting' mod='ets_onepagecheckout'}</td>
        </tr>
        {foreach from=$payments item='payment'}
            <tr>
                <td class="text-center">
                    {if isset($payment.logo)}
                        <img src="{$payment.logo|escape:'html':'UTF-8'}" style="width:57px" />
                    {/if}
                </td>
                <td class="text-left">{$payment.module_name|escape:'html':'UTF-8'}</td>
                <td class="text-left">{$payment.description|escape:'html':'UTF-8'}</td>
                <td class="text-center">
                    {if $payment.active}
                        <a href="#" class="list-action-payment field-active list-action-enable action-disabled list-item-{$payment.id_module|intval}" data-module="{$payment.name|escape:'html':'UTF-8'}" data-active="1" data-link-active="{$payment.link_active|escape:'html':'UTF-8'}" data-link-disable="{$payment.link_disable|escape:'html':'UTF-8'}" data-id="{$payment.id_module|intval}" title="{l s='Click to disable' mod='ets_onepagecheckout'}">
                            <i class="icon-check"></i>
                        </a>
                    {else}
                        <a href="#" class="list-action-payment field-active list-action-enable action-enabled list-item-{$payment.id_module|intval}" data-module="{$payment.name|escape:'html':'UTF-8'}" data-active="0" data-link-active="{$payment.link_active|escape:'html':'UTF-8'}" data-link-disable="{$payment.link_disable|escape:'html':'UTF-8'}" data-id="{$payment.id_module|intval}" title="{l s='Click to enable' mod='ets_onepagecheckout'}">
                            <i class="icon-remove"></i>
                        </a>
                    {/if}    
                </td>
                <td class="text-center">
                    {if $payment.setting}
                        <a target="_blank" href="{$payment.url|escape:'html':'UTF-8'}" title="{l s='Settings' mod='ets_onepagecheckout'}">{l s='Settings' mod='ets_onepagecheckout'}</a>
                    {/if}
                </td>
            </tr>
        {/foreach}
    </table>
    
{/if}