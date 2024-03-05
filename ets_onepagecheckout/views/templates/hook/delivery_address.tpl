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
<div class="title yes_invoice_address" style="display:none">{l s='Delivery address' mod='ets_onepagecheckout'}</div>
{if $list_address}
    {if $use_address}
        <div class="form-group row p_0 ">
            <label class="col-md-4 form-control-label required"> {l s='Use address' mod='ets_onepagecheckout'} </label>
            <div class="col-md-8 opc_field_right">
                <div class="shipping_address_form">
                        <div class="ets_opc_select">
                            <span class="ets_opc_select_arrow">
                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
                            </span>
                            <select id="use_shipping_address" name="shipping_address[id_address]" class="form-control" data-type="shipping_address">
                                <option value="" disabled="" selected="">-- {l s='please choose' mod='ets_onepagecheckout'} --</option>
                                {foreach from=$list_address item='address'}
                                    <option value="{$address.id_address|intval}"{if $address.id_address==$id_address} selected="selected"{/if}>{$address.alias|escape:'html':'UTF-8'}</option>
                                {/foreach}
                                <option value="new">{l s='Enter new address' mod='ets_onepagecheckout'}</option>
                            </select>
                        </div>
                </div>
            </div>
        </div>
    {else}
        <input id="use_shipping_address" type="hidden" name="shipping_address[id_address]" value="{$id_address|intval}" data-type="shipping_address" />
    {/if}
{/if}
{$address_form nofilter}
{if $use_address_invoice}
    <p class="no_invoice_address">{l s='The selected address will be used both as your personal address (for invoice) and as your delivery address.' mod='ets_onepagecheckout'}</p>
    <div class="form-group row ">
        <label for="use_another_address_for_invoice" class="ets_checkinput">
        <input type="checkbox" name="use_another_address_for_invoice" id="use_another_address_for_invoice" value="1" />&nbsp;<i class="ets_checkbox"></i>{l s='Use another address for invoice' mod='ets_onepagecheckout'}</label>
        
    </div>
{/if}