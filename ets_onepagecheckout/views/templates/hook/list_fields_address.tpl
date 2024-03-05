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
{if $field_address}
    {foreach from=$field_address item='field'}
        <tr id="field-address-{$field.id|escape:'html':'UTF-8'}" style="">  
            <td class="ets_checkinput_hasdrag">
                <label class="ets_checkinput" for="ETS_OPC_ADDRESS_DISPLAY_FIELD_{$field.id|escape:'html':'UTF-8'}">
                    <input id="ETS_OPC_ADDRESS_DISPLAY_FIELD_{$field.id|escape:'html':'UTF-8'}" class="ets_opc_field address_field" name="ETS_OPC_ADDRESS_DISPLAY_FIELD[]" value="{$field.id|escape:'html':'UTF-8'}" type="checkbox"{if in_array($field.id,$selected_address)} checked="checked"{/if} />
                    <i class="ets_checkbox"></i>
                    {$field.label|escape:'html':'UTF-8'}
                    <span class="icon_dragdrop"></span>
                </label>
            </td>
            <td class="text-center">
                <label class="ets_checkinput" for="ETS_OPC_ADDRESS_DISPLAY_FIELD_{$field.id|escape:'html':'UTF-8'}">
                    <input id="ETS_OPC_ADDRESS_DISPLAY_FIELD_{$field.id|escape:'html':'UTF-8'}" class="ets_opc_field_validate address_field_validate" name="ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED[]" value="{$field.id|escape:'html':'UTF-8'}" type="checkbox"{if in_array($field.id,$selected_required_address)} checked="checked"{/if} />
                    <i class="ets_checkbox"></i>
                </label>
            </td>
        </tr>
    {/foreach}
{/if}