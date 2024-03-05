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
<div class="list-table-attributes">
    <div class="form-group row">
        <button class="add_new_link btn btn-default">
            <i class="icon icon-plus"></i> {l s='Add new option' mod='ets_extraoptions'}
        </button>
    </div>
    <div class="panel-wrapper">
        {if $groups}
            {foreach from=$groups item='group'}
                <div class="attribute-group-item-{$group.id_ets_eto_attr_group|intval}">
                    {if $group.id_ets_eto_attr_group || Count($groups)>1}<h2>{if $group.id_ets_eto_attr_group}{$group.name|escape:'html':'UTF-8'}{else}{l s='Other options' mod='ets_extraoptions'}{/if}</h2>{/if}
                    {$group.list_attributes nofilter}
                </div>
            {/foreach}
        {else}
            <div class="alert alert-warning">{l s='No items found' mod='ets_extraoptions'}</div>
        {/if}
    </div>
</div>
{if isset($form_new) && $form_new}
    <div class="ets_eto_popup">
        <div class="popup_content_table">
            <div class="popup_content_tablecell">
                <div class="popup_content_wrap" style="position: relative">
                    <span class="close_popup" title="{l s='Close' mod='ets_extraoptions'}">+</span>
                    <div id="block-form-add-new-attributes" class="defaultForm form-horizontal">
                        {$form_new nofilter}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}