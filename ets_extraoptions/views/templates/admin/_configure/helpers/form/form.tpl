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
{extends file="helpers/form/form.tpl"}
{block name="input"}
{if $input.type == 'textarea'}
	{if isset($input.lang) AND $input.lang}
		{foreach $languages as $language}
			{if $languages|count > 1}
			<div class="form-group translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
				<div class="col-lg-9">
			{/if}
					{if isset($input.maxchar) && $input.maxchar}
					<div class="input-group">
						<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
							<span class="text-count-down">{$input.maxchar|intval}</span>
						</span>
					{/if}
					<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_{$language.id_lang|intval}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}</textarea>
					{if isset($input.maxchar) && $input.maxchar}
					</div>
					{/if}
			{if $languages|count > 1}
				</div>
				<div class="col-lg-2">
					<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
						{$language.iso_code|escape:'html':'UTF-8'}
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						{foreach from=$languages item=language}
						<li>
							<a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a>
						</li>
						{/foreach}
					</ul>
				</div>
			</div>
			{/if}
		{/foreach}
		{if isset($input.maxchar) && $input.maxchar}
			<script type="text/javascript">
			$(document).ready(function(){
			{foreach from=$languages item=language}
				countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter"));
			{/foreach}
			});
			</script>
		{/if}
	{else}
		{if isset($input.maxchar) && $input.maxchar}
			<span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|intval}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|intval}{/if}_counter" class="input-group-addon">
				<span class="text-count-down">{$input.maxchar|intval}</span>
			</span>
		{/if}
		<textarea{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if} name="{$input.name|escape:'html':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'html':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'html':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{else}textarea-autosize{/if}{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}>{$fields_value[$input.name]|escape:'html':'UTF-8'}</textarea>
		{if isset($input.maxchar) && $input.maxchar}
			<script type="text/javascript">
    			$(document).ready(function(){
    				countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
    			});
			</script>
		{/if}
	{/if}
{elseif $input.type=='select' && $input.name=='id_ets_eto_attr_group' && !$input.options.query}
    <div class="alert alert-info">{l s='No option groups available.' mod='ets_extraoptions'} <a href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=ets_extraoptions&tab_active=group">{l s='Configure now' mod='ets_extraoptions'}</a></div>
{else}
    {$smarty.block.parent}
{/if}
{/block}