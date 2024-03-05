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
{if $fields}
    {foreach from=$fields item='field'}
        <div class="form-group row ">
            <label class="col-md-3 form-control-label{if $field.required} required{/if}"> {$field.title|escape:'html':'UTF-8'} </label>
            <div class="col-md-8 opc_field_right">
                {if $field.type=='text'}
                    <div>
                        <input id="additional_info_{$field.id|intval}" class="form-control validate{if $field.required} is_required{/if}" data-validate="isCleanHtml" name="additional_info[{$field.id|intval}]" value="" maxlength="32" type="text" data-validate-errors="{$field.title|escape:'html':'UTF-8'} {l s='is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{$field.title|escape:'html':'UTF-8'} {l s='is required' mod='ets_onepagecheckout' js=1}"/>
                        {if $field.description}
                            <div class="desc">{$field.description|nl2br nofilter}</div>
                        {/if}
                    </div>
                {elseif $field.type=='number'}
                    <div class="ps-number edit-qty hover-buttons" id="">
                        <input id="additional_info_{$field.id|intval}" class="form-control validate{if $field.required} is_required{/if}" data-validate="isInt" name="additional_info[{$field.id|intval}]" value="" maxlength="32" type="number" data-validate-errors="{$field.title|escape:'html':'UTF-8'} {l s='is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{$field.title|escape:'html':'UTF-8'} {l s='is required' mod='ets_onepagecheckout' js=1}" />
                        <div class="ps-number-spinner d-flex">
                            <span class="ps-number-up">up</span>
                            <span class="ps-number-down">down</span>
                        </div>
                    </div>
                    {if $field.description}
                        <div class="desc">{$field.description|nl2br nofilter}</div>
                    {/if}
                {elseif $field.type=='date' || $field.type=='date_time'}
                    <div class="row" id="">
                        <div class="input-group col-lg-4">
                            <input id="additional_info_{$field.id|intval}" autocomplete="off" placeholder="{if $field.type=='date'}yyyy/mm/dd{else}yyyy/mm/dd hh::ii:ss{/if}" id="additional_info_{$field.id|intval}" class="form-control{if $field.type=='date'} datepicker{/if}{if $field.type=='date_time'} datetimepicker {/if} validate{if $field.required} is_required{/if}" data-validate="isDateFormat" name="additional_info[{$field.id|intval}]" value="" maxlength="32" type="text" data-validate-errors="{$field.title|escape:'html':'UTF-8'} {l s='is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{$field.title|escape:'html':'UTF-8'} {l s='is required' mod='ets_onepagecheckout' js=1}"/>
                            <label class="input-group-addon" for="additional_info_{$field.id|intval}">
                                <i class="ets_svg_icon no_be">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>
                                </i>
                            </label>
                        </div>
                        {if $field.description}
                            <div class="desc col-xs-12 col-sm-12">{$field.description|nl2br nofilter}</div>
                        {/if}
                    </div>
                    
                {elseif $field.type=='textarea'}
                    <div>
                        <textarea id="additional_info_{$field.id|intval}" class="form-control validate{if $field.required} is_required{/if}" data-validate="isCleanHtml" name="additional_info[{$field.id|intval}]" data-validate-errors="{$field.title|escape:'html':'UTF-8'} {l s='is not valid' mod='ets_onepagecheckout' js=1}" data-required-errors="{$field.title|escape:'html':'UTF-8'} {l s='is required' mod='ets_onepagecheckout' js=1}"></textarea>
                        {if $field.description}
                            <div class="desc">{$field.description|nl2br nofilter}</div>
                        {/if}
                    </div>
                {elseif $field.type=='radio' && $field.options}
                    <div class="input-radios" id="additional_info_{$field.id|intval}">
                        {foreach from=$field.options key='key' item='option'}
                            <label for="additional_info_{$field.id|intval}_{$option.value|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}">
                                <span class="custom-radio">
                                    <input value="{$option.value|escape:'html':'UTF-8'}" id="additional_info_{$field.id|intval}_{$option.value|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" type="radio" name="additional_info[{$field.id|intval}]"{if $option.default} checked="checked"{/if} />
                                    <span></span>
                                </span>
                                {$option.text|escape:'html':'UTF-8'}
                            </label>
                        {/foreach}
                    </div>
                    {if $field.description}
                        <div class="desc">{$field.description|nl2br nofilter}</div>
                    {/if}
                {elseif $field.type=='checkbox' && $field.options}
                    <div class="input-checkboxs" id="additional_info_{$field.id|intval}">
                        {foreach from=$field.options key='key' item='option'}
                            <label for="additional_info_{$field.id|intval}_{$option.value|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" class="ets_checkinput">
                            <input value="{$option.value|escape:'html':'UTF-8'}" id="additional_info_{$field.id|intval}_{$option.value|escape:'html':'UTF-8'}_{$key|escape:'html':'UTF-8'}" type="checkbox" name="additional_info[{$field.id|intval}][]" {if $option.default} checked="checked"{/if} />
                            <i class="ets_checkbox"></i>
                            {$option.text|escape:'html':'UTF-8'}
                            </label>
                        {/foreach}
                    </div>
                    {if $field.description}
                        <div class="desc">{$field.description nofilter}</div>
                    {/if}
                {elseif $field.type=='select' && $field.options}
                    <select name="additional_info[{$field.id|intval}]" id="additional_info_{$field.id|intval}">
                        {foreach from=$field.options item='option'}
                            <option value="{$option.value|escape:'html':'UTF-8'}" {if $option.default} selected="selected"{/if}> {$option.text|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    {if $field.description}
                        <div class="desc">{$field.description|nl2br nofilter}</div>
                    {/if}
                {elseif $field.type=='file'}
                    <div class="custom-file">
                        <input id="additional_info_{$field.id|intval}" class="form-control custom-file-input" name="additional_info[{$field.id|intval}]" type="file" />
                        <label class="custom-file-label" for="additional_info_{$field.id|intval}" data-browser="{l s='Browse' mod='ets_onepagecheckout'}">
                           {l s='Choose file(s)' mod='ets_onepagecheckout'}
                        </label>
                    </div>
                    {if $field.description}
                        <div class="desc">{$field.description|nl2br nofilter}</div>
                    {/if}
                    <div class="desc">{l s='Accepted formats: pdf, jpg, gif, png, jpeg, docs, docx, txt, zip. Limit:' mod='ets_onepagecheckout'} {Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|escape:'html':'UTF-8'}{l s='MB' mod='ets_onepagecheckout'}</div>
                {/if}
            </div>
        </div>
    {/foreach}
{/if}