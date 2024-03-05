{*
* 2007-2023 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesbite only.
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
<div class="js-address-form {$address_type|escape:'html':'UTF-8'}">
    <input name="{$address_type|escape:'html':'UTF-8'}[id_address]" value="{$class_address->id|intval}" type="hidden" />
    {if $field_address}
        {if !in_array('country',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
            <input id="{$address_type|escape:'html':'UTF-8'}_id_country" class="form-control form-control-select ets-onepage-js-country" name="{$address_type|escape:'html':'UTF-8'}[id_country]" type="hidden" value="{$id_country|intval}" />
        {/if}
        {foreach from=$field_address key='key' item='field'}
            {if $key=='alias' && in_array('alias',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('alias',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Alias' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_alias" class="form-control validate{if in_array('alias',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isGenericName" name="{$address_type|escape:'html':'UTF-8'}[alias]" value="{$class_address->alias|trim|escape:'html':'UTF-8'}" maxlength="32" type="text" data-validate-errors="{l s='Alias is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Alias is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='firstname' && in_array('firstname',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row firstname">
                    <label class="col-md-4 form-control-label {if in_array('firstname',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='First name' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_firstname" class="form-control validate {if in_array('firstname',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isCustomerName" name="{$address_type|escape:'html':'UTF-8'}[firstname]" value="{$class_address->firstname|trim|escape:'html':'UTF-8'}" maxlength="255"  type="text" data-validate-errors="{l s='First name is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='First name is required' mod='ets_onepagecheckout' js=1}"/> 
                    </div>
                </div>
            {/if}
            {if $key=='lastname' && in_array('lastname',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row lastname">
                    <label class="col-md-4 form-control-label {if in_array('lastname',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Last name' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_lastname" class="form-control validate {if in_array('lastname',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isCustomerName" name="{$address_type|escape:'html':'UTF-8'}[lastname]" value="{$class_address->lastname|trim|escape:'html':'UTF-8'}" maxlength="255" type="text" data-validate-errors="{l s='Last name is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Last name is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='company' && in_array('company',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label {if in_array('company',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Company' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right ">
                        <input id="{$address_type|escape:'html':'UTF-8'}_company" class="form-control validate{if in_array('company',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isGenericName" name="{$address_type|escape:'html':'UTF-8'}[company]" value="{$class_address->company|trim|escape:'html':'UTF-8'}" maxlength="255" type="text" data-validate-errors="{l s='Company is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Company is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='address' && in_array('address',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label {if in_array('address',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Address' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_address1" class="form-control validate {if in_array('address',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isAddress" name="{$address_type|escape:'html':'UTF-8'}[address1]" value="{$class_address->address1|trim|escape:'html':'UTF-8'}" maxlength="128" type="text" id="{$address_type|escape:'html':'UTF-8'}_address1" data-validate-errors="{l s='Address is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Address is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='address2' && in_array('address2',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('address2',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Address Complement' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_address2" class="form-control validate{if in_array('address2',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isAddress" name="{$address_type|escape:'html':'UTF-8'}[address2]" value="{$class_address->address2|trim|escape:'html':'UTF-8'}" maxlength="128" type="text" data-validate-errors="{l s='Address Complement is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Address Complement is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='other' && in_array('other',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('other',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Other' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_other" class="form-control validate{if in_array('other',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isMessage" name="{$address_type|escape:'html':'UTF-8'}[other]" value="{$class_address->other|trim|escape:'html':'UTF-8'}" maxlength="128" type="text" data-validate-errors="{l s='Other is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Other is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='city' && in_array('city',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('city',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='City' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_city" class="form-control validate{if in_array('city',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isCityName" name="{$address_type|escape:'html':'UTF-8'}[city]" value="{$class_address->city|trim|escape:'html':'UTF-8'}" maxlength="64" type="text" data-validate-errors="{l s='City is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='City is required' mod='ets_onepagecheckout' js=1}"/>
                    </div>
                </div>
            {/if}
            {if $key=='state' && in_array('state',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row address_state" {if !$states} style="display:none"{/if}>
                    <label class="col-md-4 form-control-label {if in_array('state',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='State' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        {if $states}
                            <div class="ets_opc_select">
                                <span class="ets_opc_select_arrow">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
                                </span>
                                <select id="{$address_type|escape:'html':'UTF-8'}_id_state" class="form-control form-control-select" name="{$address_type|escape:'html':'UTF-8'}[id_state]" id="{$address_type|escape:'html':'UTF-8'}_id_state">
                                    <option value="0">-- {l s='please choose' mod='ets_onepagecheckout'} --</option>
                                    {foreach from = $states item='state'}
                                        <option data-iso-code="{$state.iso_code|escape:'html':'UTF-8'}" value="{$state.id_state|intval}" {if $id_state_selected}{if $id_state_selected==$state.id_state} selected="selected"{/if}{else} {if $class_address->id_state== $state.id_state} selected="selected"{/if}{/if}>{$state.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {/if}
                    </div>
                </div>
            {/if}
            {if $key=='postcode' && in_array('post_code',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('post_code',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Zip Code' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_postal_code" class="form-control validate{if in_array('post_code',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isPostCode" name="{$address_type|escape:'html':'UTF-8'}[postcode]" value="{$class_address->postcode|escape:'html':'UTF-8'}" maxlength="12" type="text" data-validate-errors="{l s='Zip code is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Zip code is required' mod='ets_onepagecheckout' js=1}"/>
                    </div>
                </div>
            {/if}
            {if $key=='country' && in_array('country',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('country',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Country' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <div class="ets_opc_select">
                            <span class="ets_opc_select_arrow">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
                                </span>
                            <select id="{$address_type|escape:'html':'UTF-8'}_id_country" class="form-control form-control-select ets-onepage-js-country" name="{$address_type|escape:'html':'UTF-8'}[id_country]" data-type="{$address_type|escape:'html':'UTF-8'}" id="{$address_type|escape:'html':'UTF-8'}_country">
                                <option value="">-- {l s='please choose' mod='ets_onepagecheckout'} --</option>
                                {if $countries}
                                    {foreach from=$countries item='country'}
                                        <option data-iso-code="{$country.iso_code|escape:'html':'UTF-8'}" value="{$country.id_country|intval}" {if $country.id_country==$id_country} selected="selected"{/if} >{$country.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                </div>
            {/if}
            {if $key=='phone' && in_array('phone',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row">
                    <label class="col-md-4 form-control-label{if in_array('phone',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Phone'  mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_phone" class="form-control validate{if in_array('phone',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isPhoneNumber" name="{$address_type|escape:'html':'UTF-8'}[phone]" value="{$class_address->phone|escape:'html':'UTF-8'}" maxlength="32" type="tel" data-validate-errors="{l s='Phone is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Phone is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='phonemobile' && in_array('phone_mobile',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row">
                    <label class="col-md-4 form-control-label{if in_array('phone_mobile',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Mobile phone'  mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_phone_mobile" class="form-control validate{if in_array('phone_mobile',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isPhoneNumber" name="{$address_type|escape:'html':'UTF-8'}[phone_mobile]" value="{$class_address->phone_mobile|escape:'html':'UTF-8'}" maxlength="32" type="tel" data-validate-errors="{l s='Mobile phone is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Mobile phone is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
            {if $key=='dni' && in_array('dni',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('dni',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='Identification number' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_dni" class="form-control validate{if in_array('dni',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isDniLite" name="{$address_type|escape:'html':'UTF-8'}[dni]" value="{$class_address->dni|escape:'html':'UTF-8'}" maxlength="128" type="text" data-validate-errors="{l s='Identification number is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='Identification number is required' mod='ets_onepagecheckout' js=1}"/>
                    </div>
                </div>
            {/if}
            {if $key=='vatnumber' && in_array('vat_number',$ETS_OPC_ADDRESS_DISPLAY_FIELD)}
                <div class="form-group row ">
                    <label class="col-md-4 form-control-label{if in_array('vat_number',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} required{/if}"> {l s='VAT number' mod='ets_onepagecheckout'} </label>
                    <div class="col-md-8 opc_field_right">
                        <input id="{$address_type|escape:'html':'UTF-8'}_vat_number" class="form-control validate{if in_array('vat_number',$ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED)} is_required{/if}" data-validate="isGenericName" name="{$address_type|escape:'html':'UTF-8'}[vat_number]" value="{$class_address->vat_number|escape:'html':'UTF-8'}" maxlength="128" type="text" data-validate-errors="{l s='VAT number is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{l s='VAT number is required' mod='ets_onepagecheckout' js=1}" />
                    </div>
                </div>
            {/if}
        {/foreach}
    {/if}
</div>