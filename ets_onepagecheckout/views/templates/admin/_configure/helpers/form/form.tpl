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
{extends file="helpers/form/form.tpl"}
{block name="input_row"}
    {if isset($input.start_social) && $input.start_social}
        <div class="form-group-content-social" data-item="{$input.start_social|escape:'html':'UTF-8'}">
    {/if}
    {if $input.name=='ETS_OPC_LOGIN_DISPLAY_FIELD'}
        <div class="tab-content {$input.tab|escape:'html':'UTF-8'}" style="display:none;">
            <ul id="sub-tabs">
                <li class="sub-tab active" data-tab="login">{l s='Log in' mod='ets_onepagecheckout'}</li>
                <li class="sub-tab" data-tab="guest">{l s='Guest order' mod='ets_onepagecheckout'}</li>
                <li class="sub-tab" data-tab="create">{l s='Create account' mod='ets_onepagecheckout'}</li>
            </ul>
        </div>
    {/if}
    <div class="tab-content {$input.tab|escape:'html':'UTF-8'}" style="display:none;">
        {if isset($input.sub_tab)}
            <div class="tab-sub-content {$input.sub_tab|escape:'html':'UTF-8'}" style="display:none;">
        {/if}
        {if $input.type!='custom_html'}
            {$smarty.block.parent}
        {else}
            {$input.html_custom nofilter}
        {/if}
        {if $input.name=='ETS_OPC_FACEBOOK_SECRET' || $input.name=='ETS_OPC_GOOGLE_SECRET' || $input.name=='ETS_OPC_PAYPAL_SANBOX_ENABLED'}
            <div class="form-group-wrapper ets_solo_social_networks row_ets_solo_google_callback" data-group="gl">
                <div class="form-group form-group social_login">
                    <label class="control-label col-lg-3">{if $input.name=='ETS_OPC_PAYPAL_SANBOX_ENABLED'}{l s='Return URL' mod='ets_onepagecheckout'}{else} {l s='Redirect URI' mod='ets_onepagecheckout'}{/if} </label>
                    <div class="col-lg-9">
                        {if $link_calbacks}
                            {foreach from=$link_calbacks item='link_calback'}
                                <span class="ets_solo_callback_url" data-msg="{l s='Copied' mod='ets_onepagecheckout'}" title="{l s='Click to copy' mod='ets_onepagecheckout'}">{$link_calback|escape:'html':'UTF-8'}</span><br />
                            {/foreach}
                        {/if}
                        <p class="help-block">{if $input.name=='ETS_OPC_PAYPAL_SANBOX_ENABLED'} {l s='Copy and paste this Return URL to get your social network API key pair' mod='ets_onepagecheckout'}{else} {l s='Copy and paste this Redirect URI to get your social network API key pair' mod='ets_onepagecheckout'}{/if} </p>
                    </div>
                </div>
            </div>
        {/if}
        {if $input.name=='ETS_OPC_PAGE_ENABLED_SOCIAL'}
            <div class="form-group social_login">
                <div class="col-lg-3"></div>
                <div class="col-lg-9">
                    <div id="group-social-login">
                        {foreach from=$list_socials item='social'}
                            {assign var=field_name value= "ETS_OPC_"|cat:Tools::strtoupper($social)|escape:'html':'UTF-8'|cat:"_ENABLED"}
                            <div id="social-{$social|escape:'html':'UTF-8'}" class="social-item" data-item="{Tools::strtolower($social)|escape:'html':'UTF-8'}">
                                <div class="social-login-header">
                                    <i class="icon icon-{Tools::strtolower($social)|escape:'html':'UTF-8'}"></i> {l s='Log in with' mod='ets_onepagecheckout'} {$social|escape:'html':'UTF-8'}
                                    <div class="setting">
                                        <span class="ets_svg_icon">
                                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>
                                        </span>
                                        {l s='Configure' mod='ets_onepagecheckout'}
                                    </div>
                                    <label class="ets_opc_switch active">
                                        <input class="ets_opc_slider"{if isset($fields_value[$field_name]) && $fields_value[$field_name]} checked="checked"{/if} value="1" data-field="ETS_OPC_{Tools::strtoupper($social)|escape:'html':'UTF-8'}_ENABLED" type="checkbox" />
                                        <span class="ets_opc_slider_label on">{l s='On' mod='ets_onepagecheckout'}</span>
                                        <span class="ets_opc_slider_label off">{l s='Off' mod='ets_onepagecheckout'}</span>
                                    </label>
                                    <span class="position">
                                        <span class="ets_svg_icon">
                                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 26-19 45l-256 256q-19 19-45 19t-45-19-19-45v-128h-384v384h128q26 0 45 19t19 45-19 45l-256 256q-19 19-45 19t-45-19l-256-256q-19-19-19-45t19-45 45-19h128v-384h-384v128q0 26-19 45t-45 19-45-19l-256-256q-19-19-19-45t19-45l256-256q19-19 45-19t45 19 19 45v128h384v-384h-128q-26 0-45-19t-19-45 19-45l256-256q19-19 45-19t45 19l256 256q19 19 19 45t-19 45-45 19h-128v384h384v-128q0-26 19-45t45-19 45 19l256 256q19 19 19 45z"/></svg>
                                        </span>
                                    </span>
                                </div>
                                <div class="social-login-content" style="display:none;" data-item="{Tools::strtolower($social)|escape:'html':'UTF-8'}">
    
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        {if isset($input.sub_tab)}
            </div>
        {/if}
         
    </div>
    {if isset($input.end_social) && $input.end_social}
        </div>
    {/if}
{/block}
{block name="input"}
{if $input.type == 'checkbox'}
    {if isset($input.values.query) && $input.values.query}
        {if isset($input.extra_field) && $input.extra_field}
					<table>
                    <thead>
                        <tr>
                            <th>
                                {l s='Display field' mod='ets_onepagecheckout'}
                            </th>
                            <th>
                                {l s='Required' mod='ets_onepagecheckout'}
                                 
                            </th>
                        </tr>
                        {if $input.name=='ETS_OPC_ADDRESS_DISPLAY_FIELD'}
                            <tr>
                                <th>
                                    <label for="ETS_OPC_ADDRESS_DISPLAY_FIELD_all" class="ets_checkinput">
                                        <input type="checkbox" id="ETS_OPC_ADDRESS_DISPLAY_FIELD_all" value="1" />
                                        <i class="ets_checkbox"></i>
                                         {l s='All' mod='ets_onepagecheckout'}                                   
                                    </label>
                                    
                                </th>
                                <th class="text-center">
                                    <label for="ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all" class="ets_checkinput">
                                        <input type="checkbox" id="ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all" value="1" />
                                        <i class="ets_checkbox"></i>
                                    </label>
                                </th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label for="ETS_OPC_ADDRESS_DISPLAY_FIELD_use_address" class="ets_checkinput">
                                        <input class="ets_opc_field address_field" type="checkbox" id="ETS_OPC_ADDRESS_DISPLAY_FIELD_use_address" name="ETS_OPC_ADDRESS_DISPLAY_FIELD[]" value="use_address" {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array('use_address',$fields_value[$input.name])} checked="checked"{/if} />
                                        <i class="ets_checkbox"></i>
                                         {l s='Use address' mod='ets_onepagecheckout'}                                   
                                    </label>
                                </td>
                            </tr>
                        {/if}
                    </thead>
                    <tbody id="list-fields-{$input.tab|escape:'html':'UTF-8'}">
                    
        {/if}
        {foreach $input.values.query as $value}
            {if isset($input.extra_field) && $input.extra_field}
					<tr id="field-{$input.tab|escape:'html':'UTF-8'}-{$value[$input.values.id]|replace :'_':''|escape:'html':'UTF-8'}">
            {else}
                <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
            {/if}
			{assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]|escape:'html':'UTF-8'}
				{strip}
                    {if isset($input.extra_field) && $input.extra_field}
                            <td class="ets_checkinput_hasdrag">
                                <label for="{$id_checkbox|escape:'html':'UTF-8'}" class="ets_checkinput">
            						<input class="ets_opc_field {$input.tab|escape:'html':'UTF-8'}_field" {if isset($value.required) && $value.required }disabled="disabled" checked="checked"{/if} {if $value[$input.values.id]=='message'}disabled="disabled"{/if} type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value[$input.values.id])} value="{$value[$input.values.id]|escape:'html':'UTF-8'}"{/if} {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array($value[$input.values.id],$fields_value[$input.name])} checked="checked"{/if} />
                                    <i class="ets_checkbox"></i>
            						&nbsp;{$value[$input.values.name]|escape:'html':'UTF-8'}
                                    <span class="icon_dragdrop"></span>
            					</label>
                            </td>
                            <td class="text-center">
                                <label for="{$id_checkbox|escape:'html':'UTF-8'}" class="ets_checkinput">
            						<input class="ets_opc_field_validate {$input.tab|escape:'html':'UTF-8'}_field_validate" {if isset($value.required) && $value.required }disabled="disabled" checked="checked"{/if} type="checkbox" name="{$input.name2|escape:'html':'UTF-8'}[]" id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value[$input.values.id])} value="{$value[$input.values.id]|escape:'html':'UTF-8'}"{/if} {if isset($fields_value[$input.name2]) && is_array($fields_value[$input.name2]) && $fields_value[$input.name2] && in_array($value[$input.values.id],$fields_value[$input.name2]) && in_array($value[$input.values.id],$fields_value[$input.name])} checked="checked"{/if} />
                                    <i class="ets_checkbox"></i>
            					</label>
                            </td>
                    {else}
                        <label for="{$id_checkbox|escape:'html':'UTF-8'}" class="ets_checkinput">
    						<input {if isset($value.required) && $value.required }disabled="disabled" checked="checked"{/if} {if $value[$input.values.id]=='message'}disabled="disabled"{/if} type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" id="{$id_checkbox|escape:'html':'UTF-8'}" {if isset($value[$input.values.id])} value="{$value[$input.values.id]|escape:'html':'UTF-8'}"{/if} {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array($value[$input.values.id],$fields_value[$input.name])} checked="checked"{/if} />
                            <i class="ets_checkbox"></i>
                            {$value[$input.values.name]|escape:'html':'UTF-8'}
    					</label>
                    {/if}
				{/strip}
			{if isset($input.extra_field) && $input.extra_field}
					</tr>
            {else}
                </div>
            {/if}
		{/foreach} 
        {if isset($input.extra_field) && $input.extra_field}
                    </tbody>
                    {if $input.name=='ETS_OPC_ADDRESS_DISPLAY_FIELD'}
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <label for="ETS_OPC_ADDRESS_DISPLAY_FIELD_use_address_invoice" class="ets_checkinput">
                                        <input class="ets_opc_field address_field" type="checkbox" id="ETS_OPC_ADDRESS_DISPLAY_FIELD_use_address_invoice" name="ETS_OPC_ADDRESS_DISPLAY_FIELD[]" value="use_address_invoice" {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && $fields_value[$input.name] && in_array('use_address_invoice',$fields_value[$input.name])} checked="checked"{/if} />
                                        <i class="ets_checkbox"></i>
                                         {l s='Use another address for invoice' mod='ets_onepagecheckout'}                                   
                                    </label>
                                </td>
                            </tr>
                        </tfoot>
                    {/if}
					</table>
        {/if}
    {/if}
{else}
    {$smarty.block.parent}
{/if}
{/block}
{block name="label"}
{if isset($input.label)}
	<label class="control-label col-lg-3{if ((isset($input.required) && $input.required) || (isset($input.required2) && $input.required2)) && $input.type != 'radio'} required{/if}">
		{if isset($input.hint)}
		<span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{if is_array($input.hint)}
					{foreach $input.hint as $hint}
						{if is_array($hint)}
							{$hint.text|escape:'html':'UTF-8'}
						{else}
							{$hint|escape:'html':'UTF-8'}
						{/if}
					{/foreach}
				{else}
					{$input.hint|escape:'html':'UTF-8'}
				{/if}">
		{/if}
		{$input.label|escape:'html':'UTF-8'}
		{if isset($input.hint)}
		</span>
		{/if}
	</label>
{/if}
{/block}
{block name="input"}
    {$smarty.block.parent}
    {if $input.name=='ETS_OPC_TEST_API'}
        <button class="btn btn-outline-primary add_ip_button" type="button" data-ip="{Tools::getRemoteAddr()|escape:'html':'UTF-8'}">
            <span class="ets_svg_icon">
                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
            </span>
            {l s='Add my IP' mod='ets_onepagecheckout'}
        </button>
    {/if}
{/block}
{block name="description"}
	{if isset($input.desc) && !empty($input.desc)}
		<p class="help-block">
			{if is_array($input.desc)}
				{foreach $input.desc as $p}
					{if is_array($p)}
						<span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'html':'UTF-8'}</span><br />
					{else}
						{$p|escape:'html':'UTF-8'}<br />
					{/if}
				{/foreach}
			{else}
				{$input.desc nofilter}
			{/if}
		</p>
	{/if}
{/block}