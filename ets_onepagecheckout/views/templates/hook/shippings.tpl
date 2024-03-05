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
<div class="delivery-options-list">
    {if $delivery_option_list}
        {foreach $delivery_option_list as $id_address => $option_list}
            <div class="delivery-options">
                {foreach $option_list as $key => $option}
                    <div class="row delivery-option">
                        <div class="col-auto">
                            <span class="custom-radio float-xs-left">
                                <input id="delivery_option_{$option.id_carrier|intval}" name="delivery_option[{$id_address|intval}]"  value="{$key|escape:'html':'UTF-8'}" {if in_array($key,$delivery_option_selected)} checked="checked"{/if} type="radio" />
                                <span></span>
                            </span>
                        </div>
                        <label class="col delivery-option-2" for="delivery_option_{$option.id_carrier|intval}">
                            <div class="row">
                                <div class="col-sm-5 col-8">
                                    <div class="d-flex align-items-center">
                                        {if $ETS_OPC_SHIPPING_LOGO_ENABLED && $option.logo}
                                            <img src="{$option.logo|escape:'html':'UTF-8'}" alt="{$option.name|escape:'html':'UTF-8'}" style="width:50px"  />
                                        {/if}
                                        <span class="h6 carrier-name">{$option.name|escape:'html':'UTF-8'}</span>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-7 col text-right">
                                    <div class="col-12">
                                        <span class="carrier-price">
                                        {if $option.total_price_with_tax && (isset($option.is_free) && $option.is_free == 0)}
                							{if $use_taxes == 1}
                							    {if $priceDisplay == 1}
                								    {displayPrice price=$option.total_price_without_tax} {l s='(Tax excl.)' mod='ets_onepagecheckout'}
                							    {else}
                								    {displayPrice price=$option.total_price_with_tax} {l s='(Tax incl.)' mod='ets_onepagecheckout'}
                							    {/if}
                							{else}
                							    {displayPrice price=$option.total_price_without_tax}
                							{/if}
            						    {else}
            							     {l s='Free' mod='ets_onepagecheckout'}
            						    {/if}
                                        
                                        </span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <span class="carrier-delay">{$option.delay|escape:'html':'UTF-8'}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    {if isset($option.extraContent) && $option.extraContent}
                        <div class="row carrier-extra-content extends_{$option.id_carrier|intval}" {if !in_array($key,$delivery_option_selected)} style="display:none;"{/if}>
                            {$option.extraContent nofilter}
                        </div>
                    {/if}
                {/foreach}
            </div>
        {/foreach}
    {else}
        <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' mod='ets_onepagecheckout'}</p>
    {/if}
    <div id="hook-display-after-carrier">
        {$hookDisplayAfterCarrier nofilter}
    </div>
</div>