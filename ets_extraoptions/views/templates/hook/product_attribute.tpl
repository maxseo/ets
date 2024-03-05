{*
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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if $ETS_ETO_CHECKBOX_BACKGROUD_COLOR}
<style>
{literal}
    input[type="checkbox"]:checked + .ets_input_check {
      background: {/literal}{$ETS_ETO_CHECKBOX_BACKGROUD_COLOR|escape:'html':'UTF-8'}{literal} none repeat scroll 0 0;
      border-color: {/literal}{$ETS_ETO_CHECKBOX_BACKGROUD_COLOR|escape:'html':'UTF-8'}{literal};
    }
{/literal}
</style>
{/if}
{if $attribute_groups}
    <div style="display:none">
        <div class="block-product-attribute-custom">
            <div class="col-md-12">
                <div class="form-group">
                    <h4 class="">{$attribute_title|escape:'html':'UTF-8'}</h4>
                </div>
                <div class="list-attribute-custom">
                    {foreach from=$attribute_groups item='group'}
                        {if $group.attributes}
                            {if $group.id_ets_eto_attr_group || Count($attribute_groups)>1}<h5>{$group.name|escape:'html':'UTF-8'}</h5>{/if}
                            {foreach from=$group.attributes item='attribute'}
                                <div class="checkbox{if $attribute.required} required{/if}">
                                    <label id="custom-attribute-{$attribute.id_ets_eto_attr|intval}" class="control-label">
                                        <div class="ets_input_group">
                                            <input id="custom-attribute-{$attribute.id_ets_eto_attr|intval}" name="ets-ca-custom-attribute[]" data-price="{$attribute.price|escape:'html':'UTF-8'}" value="{$attribute.id_ets_eto_attr|intval}" type="checkbox"{if $attribute.checked} checked="checked"{/if} />
                                            <div class="ets_input_check" data-before="✔">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg>
                                            </div>
                                        </div>
                                        {if $attribute.required}<span class="required"></span> {/if}
                                        {$attribute.name|escape:'html':'UTF-8'}
                                        <span class="product-discount">
                                            (
        
                                            {if isset($attribute.price_without_reduction) && $attribute.price_without_reduction}
                                                <span class="regular-price">{$attribute.price_without_reduction|escape:'html':'UTF-8'}</span>
                                            {/if}
                                            <div class="product-price has-discount">
                                                <div class="current-price">
                                                    <span>{$attribute.price_text|escape:'html':'UTF-8'}</span>
                                                </div>
                                                {if isset($attribute.reduction) && $attribute.reduction}<span class="discount discount-percentage">{l s='Save' mod='ets_extraoptions'} {$attribute.reduction|escape:'html':'UTF-8'}%</span> {/if}
                                            </div>
                                            )
                                        </span>
                                    </label>
                                    {if $attribute.description}
                                        <span class="desc">
                                            <svg width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                            <span class="content">{$attribute.description|nl2br nofilter}</span>
                                        </span>
                                    {/if}
                                </div>
                            {/foreach}
                        {/if}
                    {/foreach}
                </div>
                <div class="attribute-custom-price">{$attribute_total_title|escape:'html':'UTF-8'} <span class="price"></span></div>
            </div>
        </div>
    </div>
{/if}