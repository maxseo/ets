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
<div class="panel card">
    <div class="panel-heading card-header">
        <svg style="vertical-align: -2px;" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
            <path d="M1216 1344v128q0 26-19 45t-45 19h-512q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h64v-384h-64q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h384q26 0 45 19t19 45v576h64q26 0 45 19t19 45zm-128-1152v192q0 26-19 45t-45 19h-256q-26 0-45-19t-19-45v-192q0-26 19-45t45-19h256q26 0 45 19t19 45z"/>
        </svg>
        {l s='Additional info' mod='ets_onepagecheckout'}
    </div>
    <div class="panel_card-body">
        <table class="table">
            <tbody>
                {foreach from=$additional_fields item='field'}
                    <tr>
                        <td><strong>{$field.title|escape:'html':'UTF-8'}:</strong></td>
                        <td>
                            {if $field.value}
                                {if $field.file_name}<a href="../../../../../modules/ets_onepagecheckout/download.php?file_name={$field.value|escape:'html':'UTF-8'}&id_order={$id_order|intval}" title="{l s='Download' mod='ets_onepagecheckout'}">{$field.file_name|escape:'html':'UTF-8'}</a>{else}{$field.value|nl2br nofilter}{/if}
                            {else}
                                --
                            {/if} 
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>