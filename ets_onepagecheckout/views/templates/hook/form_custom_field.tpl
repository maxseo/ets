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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}

<div class="additional-info-setting no-border-padding">
	<div class="">
		<div class="flat" style="">
			<div class="flat-body">
				<div class="alert alert-info">{l s='You can add more information fields to gain additional information from customers via checkout page' mod='ets_onepagecheckout'}</div>
				<div class="custom_fields_append" id="opc_custom_fields_append">
					{if isset($custom_fields) && $custom_fields}
                        {foreach $custom_fields as $key=>$field}
							<div id="additionalfield_{$field.id|escape:'html':'UTF-8'}" class="form-group custom-field" data-id="{$field.id|escape:'html':'UTF-8'}">
								<span data-toggle="collapse" href="#custom_field_{$key|escape:'html':'UTF-8'}" class="btn-pmf-collapse collapsed">
									<span class="ets_icon_helper">
                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 1344v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45z"/></svg>
                                    </span>
									{if isset($field['title'][$default_lang])}{$field['title'][$default_lang]|escape:'html':'UTF-8'}{/if}
                                    <span class="ets_icon_svg">
                                        <svg class="svg_plus" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/></svg>
                                        <svg class="svg_minus" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-1216q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h1216q40 0 68 28t28 68z"/></svg>
                                    </span>
                                </span>
							    <div class="group-fields collapse" id="custom_field_{$key|escape:'html':'UTF-8'}">
							    	<div class="form-group row">
								        <label class="control-label required col-lg-3">{l s='Field title' mod='ets_onepagecheckout'}</label>
								        <div class="col-lg-6">
								        	{foreach $languages as $k=>$lang}
								            <div class="form-group row trans_field trans_field_{$lang.id_lang|escape:'html':'UTF-8'} {if $k > 0}hidden{/if}">
								                <div class="col-lg-9">
								                    <input type="text" name="custom_field[{$key|escape:'html':'UTF-8'}][title][{$lang.id_lang|escape:'html':'UTF-8'}]" value="{if isset($field['title'][$lang.id_lang])}{$field['title'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}" class="form-control {if $currency->id == $lang.id_lang}required{/if}" data-error="{l s='Title of payment method field is required' mod='ets_onepagecheckout'}" />
								                </div>
                                                {if count($languages) >1}
    								                <div class="col-lg-2">
    								                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{$lang.iso_code|escape:'html':'UTF-8'} <span class="caret"></span></button>
    								                    <ul class="dropdown-menu">
    								                    	{foreach $languages as $lg}
    								                        <li><a href="javascript:ets_opcHideOtherLang({$lg.id_lang|escape:'html':'UTF-8'})" title="">{$lg.name|escape:'html':'UTF-8'}</a></li>
    								                        {/foreach}
    								                    </ul>
    								                </div>
                                                {/if}
								            </div>
								            {/foreach}
								        </div>
								    </div>
								    <div class="form-group row">
								        <label class="control-label col-lg-3">{l s='Field type' mod='ets_onepagecheckout'}</label>
								        <div class="col-lg-5">
								            <select name="custom_field[{$key|escape:'html':'UTF-8'}][type]" class="form-control custom_field_type">
								                <option value="text" {if $field.type == 'text'}selected="selected"{/if}>{l s='Text' mod='ets_onepagecheckout'}</option>
								                <option value="textarea" {if $field.type == 'textarea'}selected="selected"{/if}>{l s='Textarea' mod='ets_onepagecheckout'}</option>
                                                <option value="radio" {if $field.type == 'radio'}selected="selected"{/if}>{l s='Radio' mod='ets_onepagecheckout'}</option>
                                                <option value="checkbox" {if $field.type == 'checkbox'}selected="selected"{/if}>{l s='Checkbox' mod='ets_onepagecheckout'}</option>
                                                <option value="select" {if $field.type == 'select'}selected="selected"{/if}>{l s='Select' mod='ets_onepagecheckout'}</option>
                                                <option value="file" {if $field.type == 'file'}selected="selected"{/if}>{l s='File' mod='ets_onepagecheckout'}</option>
                                                <option value="date_time" {if $field.type == 'date_time'}selected="selected"{/if}>{l s='Date time' mod='ets_onepagecheckout'}</option>
                                                <option value="date" {if $field.type == 'date'}selected="selected"{/if}>{l s='Date' mod='ets_onepagecheckout'}</option>
                                                <option value="number" {if $field.type == 'number'}selected="selected"{/if}>{l s='Number' mod='ets_onepagecheckout'}</option>
								            </select>
								        </div>
								    </div>
                                    <div class="form-group row options"{if $field.type == 'radio' || $field.type == 'checkbox' || $field.type == 'select'} style="display:block"{else} style="display:none"{/if} >
								        <label class="control-label required col-lg-3">{l s='Options' mod='ets_onepagecheckout'}</label>
								        <div class="col-lg-6">
								        	{foreach $languages as $k=>$lang}
    								            <div class="form-group row trans_field trans_field_{$lang.id_lang|escape:'html':'UTF-8'} {if $k > 0}hidden{/if}">
    								                <div class="col-lg-9">
    								                    <textarea name="custom_field[{$key|escape:'html':'UTF-8'}][options][{$lang.id_lang|escape:'html':'UTF-8'}]" class="form-control">{if isset($field['options'][$lang.id_lang])}{$field['options'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                                                        <div class="help-block">{l s='Each value on 1 line. It also allows to set custom label, custom value and default value following this structure: %s' sprintf=['label|value:default'] mod='ets_onepagecheckout'}</div>
    								                </div>
                                                    {if count($languages) >1}
        								                <div class="col-lg-2">
        								                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{$lang.iso_code|escape:'html':'UTF-8'} <span class="caret"></span></button>
        								                    <ul class="dropdown-menu">
        								                    	{foreach $languages as $lg}
        								                        <li><a href="javascript:ets_opcHideOtherLang({$lg.id_lang|escape:'html':'UTF-8'})" title="">{$lg.name|escape:'html':'UTF-8'}</a></li>
        								                        {/foreach}
        								                    </ul>
        								                </div>
                                                    {/if}
    								            </div>
								            {/foreach}
								        </div>
								    </div>
								    <div class="form-group row">
								        <label class="control-label col-lg-3">{l s='Description' mod='ets_onepagecheckout'}</label>
								        <div class="col-lg-6">
								        	{foreach $languages as $k=>$lang}
    								            <div class="form-group row trans_field trans_field_{$lang.id_lang|escape:'html':'UTF-8'} {if $k > 0}hidden{/if}">
    								                <div class="col-lg-9">
    								                    <textarea name="custom_field[{$key|escape:'html':'UTF-8'}][description][{$lang.id_lang|escape:'html':'UTF-8'}]" class="form-control">{if isset($field['title'][$lang.id_lang])}{$field['description'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
    								                </div>
                                                    {if count($languages) >1}
        								                <div class="col-lg-2">
        								                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">{$lang.iso_code|escape:'html':'UTF-8'} <span class="caret"></span></button>
        								                    <ul class="dropdown-menu">
        								                    	{foreach $languages as $lg}
        								                        <li><a href="javascript:ets_opcHideOtherLang({$lg.id_lang|escape:'html':'UTF-8'})" title="">{$lg.name|escape:'html':'UTF-8'}</a></li>
        								                        {/foreach}
        								                    </ul>
        								                </div>
                                                    {/if}
    								            </div>
								            {/foreach}
								        </div>
                                        <div class="col-lg-1">
                                        </div>
								    </div>
								    <div class="form-group row ">
								        <label class="control-label col-lg-3">{l s='Require' mod='ets_onepagecheckout'}</label>
								        <div class="col-lg-3">
								        	<select name="custom_field[{$key|escape:'html':'UTF-8'}][required]" class="form-control">
								        		<option value="1" {if $field.required == 1}selected="selected"{/if}>{l s='Yes' mod='ets_onepagecheckout'}</option>
								        		<option value="0" {if $field.required == 0}selected="selected"{/if}>{l s='No' mod='ets_onepagecheckout'}</option>
								        		
								        	</select>
								        </div>
								    </div>
								    <div class="form-group row ">
								        <label class="control-label col-lg-3">{l s='Enable' mod='ets_onepagecheckout'}</label>
								        <div class="col-lg-9">
								            <span class="switch prestashop-switch fixed-width-lg">
								                <input type="radio" name="custom_field[{$key|escape:'html':'UTF-8'}][enable]" id="custom_field_{$key|escape:'html':'UTF-8'}_enable_on" value="1" class="custom_field_enable" {if $field.enable == 1}checked="checked"{/if}>
								                <label for="custom_field_{$key|escape:'html':'UTF-8'}_enable_on">{l s='Yes' mod='ets_onepagecheckout'}</label>
								                <input type="radio" name="custom_field[{$key|escape:'html':'UTF-8'}][enable]" id="custom_field_{$key|escape:'html':'UTF-8'}_enable_off" class="custom_field_enable" value="0" {if $field.enable == 0}checked="checked"{/if}>
								                <label for="custom_field_{$key|escape:'html':'UTF-8'}_enable_off">{l s='No' mod='ets_onepagecheckout'}</label>
								                <a class="slide-button btn"></a>
								            </span>
								        </div>
								    </div>
								    <input type="hidden" name="custom_field[{$key|escape:'html':'UTF-8'}][id]" value="{$field.id|escape:'html':'UTF-8'}" />
							    </div>
                                <a class="btn btn-default btn-sm btn-delete-field js-btn-delete-field" href="javascript:void(0)">
                                    <span class="ets_svg_icon">
                                        <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                    </span>
                                </a>
							</div>
						{/foreach}
                    {/if}
					<div class="form-group row">
			            <div class="col-lg-10">
			                <button type="button" class="btn btn-default js-add-custom-field">
                                <span class="ets_svg_icon">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/></svg>
                                </span>
                                {l s='Add new field' mod='ets_onepagecheckout'}</button>
			            </div>
			        </div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var ets_opc_languages = {json_encode($languages) nofilter};
	var ets_opc_currency = {json_encode($currency) nofilter};
    var custom_field_title = '{l s='Field title' mod='ets_onepagecheckout' js=1}';
    var pmf_title_required = '{l s='Field title is required' mod='ets_onepagecheckout' js=1}';
    var custom_field_type = '{l s='Field type' mod='ets_onepagecheckout' js=1}';
    var custom_field_description_text = '{l s='Description' mod='ets_onepagecheckout' js=1}';
    var custom_field_options_text = '{l s='Options' mod='ets_onepagecheckout' js=1}';
    var required_text = '{l s='Required' mod='ets_onepagecheckout' js=1}';
    var Enabled_text = '{l s='Enabled' mod='ets_onepagecheckout' js=1}';
    var delete_text ='{l s='Delete' mod='ets_onepagecheckout' js=1}';
    var yes_text ='{l s='Yes' mod='ets_onepagecheckout' js=1}';
    var no_text ='{l s='No' mod='ets_onepagecheckout' js=1}';
    var confirm_delete_field_text='{l s='Do you want to delete this item?' mod='ets_onepagecheckout' js=1}';
    var Text_type ='{l s='Text' mod='ets_onepagecheckout' js=1}';
    var Textarea_type = '{l s='Textarea' mod='ets_onepagecheckout' js=1}';
    var Rich_text_editor_type ='{l s='Rich text editor' mod='ets_onepagecheckout' js=1}';
    var Radio_type = '{l s='Radio' mod='ets_onepagecheckout' js=1}';
    var Checkbox_type = '{l s='Checkbox' mod='ets_onepagecheckout' js=1}';
    var Select_type = '{l s='Select' mod='ets_onepagecheckout' js=1}';
    var File_type = '{l s='File' mod='ets_onepagecheckout' js=1}';
    var Date_time_type = '{l s='Date time' mod='ets_onepagecheckout' js=1}';
    var Date_type ='{l s='Date' mod='ets_onepagecheckout' js=1}';
    var Color_type ='{l s='Color' mod='ets_onepagecheckout' js=1}';
    var Video_type = '{l s='Video' mod='ets_onepagecheckout' js=1}';
    var Image_type = '{l s='Image' mod='ets_onepagecheckout' js=1}';
    var Number_type = '{l s='Number' mod='ets_onepagecheckout' js=1}';
    var Each_value_on_1_line_text = '{l s='Each value on 1 line. It also allows to set custom label, custom value and default value following this structure: %s' sprintf=['label|value:default'] mod='ets_onepagecheckout' js=1}';
</script>