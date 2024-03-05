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
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<script type="text/javascript">
var text_update_position='{l s='Successfully updated.' mod='ets_extraoptions'}';
var confirm_delete_text = '{l s='Do you want to delete this item?' mod='ets_extraoptions' js=1}';
</script>
<div class="panel ets_extraoptions-panel{if isset($class_list)} {$class_list|escape:'html':'UTF-8'}{/if}">
    {if isset($show_add_new) && $show_add_new}            
        <a class="btn btn-default add_new_link" href="{if isset($link_new)}{$link_new|escape:'html':'UTF-8'}{else}{$currentIndex|escape:'html':'UTF-8'}{/if}">
            <i class="icon icon-plus"></i> {l s='Add new option group' mod='ets_extraoptions'}
        </a>            
    {/if}
    {if $fields_list}
        <div class="table-responsive clearfix">
            {if $show_toolbar}
                <form method="post" action="{$currentIndex|escape:'html':'UTF-8'}">
            {else}
                <div>
            {/if}
                {if isset($bulk_action_html)}
                    {$bulk_action_html nofilter}
                {/if}
                <table class="table configuration alltab_ss ">
                    <thead>
                        <tr class="nodrag nodrop">
                            {if isset($bulk_actions) && $bulk_actions}
                                 <th class="fixed-width-xs">
                                    <span class="title_box">
                                        {if count($field_values)}
                                            <input value="" class="{$name|escape:'html':'UTF-8'}_readed_all" type="checkbox" />
                                        {/if}
                                    </span>
                                </th>
                            {/if}
                            {assign var ='i' value=1}
                            {foreach from=$fields_list item='field' key='index'}
                                <th class="{if isset($index) && $index == 'active'}ets_eto_{/if}{$index|escape:'html':'UTF-8'}{if isset($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}" {if $show_action && !$actions && count($fields_list)==$i}colspan="2"{/if}>
                                    <span class="title_box">
                                        {$field.title|escape:'html':'UTF-8'}
                                        {if isset($field.sort) && $field.sort}
                                            <span class="soft">
                                            <a href="{$currentIndex|escape:'html':'UTF-8'}&sort={$index|escape:'html':'UTF-8'}&sort_type=desc{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='desc'} class="active"{/if}><i class="icon-caret-down"></i></a>
                                            <a href="{$currentIndex|escape:'html':'UTF-8'}&sort={$index|escape:'html':'UTF-8'}&sort_type=asc{$filter_params nofilter}" {if isset($sort)&& $sort==$index && isset($sort_type) && $sort_type=='asc'} class="active"{/if}><i class="icon-caret-up"></i></a>
                                            </span>
                                         {/if}
                                    </span>
                                </th>  
                                {assign var ='i' value=$i+1}                          
                            {/foreach}
                            {if $show_action && $actions}
                                <th class="table_action" style="text-align: right;">{l s='Action' mod='ets_extraoptions'}</th>
                            {/if}
                        </tr>
                        {if $show_toolbar}
                            <tr class="nodrag nodrop filter row_hover">
                                {if isset($bulk_actions) && $bulk_actions}
                                    <th>&nbsp;</th>
                                {/if}
                                {foreach from=$fields_list item='field' key='index'}
                                    <th class="{if isset($index) && $index == 'active'}ets_eto_{/if}{$index|escape:'html':'UTF-8'}{if isset($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}">
                                        {if isset($field.filter) && $field.filter}
                                            {if $field.type=='text'}
                                                <input class="filter" name="{$index|escape:'html':'UTF-8'}" type="text" {if isset($field.width)}style="width: {$field.width|intval}px;"{/if} {if isset($field.active)}value="{$field.active|escape:'html':'UTF-8'}"{/if}/>
                                            {/if}
                                            {if $field.type=='select' || $field.type=='active'}
                                                <select  {if isset($field.width)}style="width: {$field.width|intval}px;"{/if}  name="{$index|escape:'html':'UTF-8'}">
                                                    <option value=""> -- </option>
                                                    {if isset($field.filter_list.list) && $field.filter_list.list}
                                                        {assign var='id_option' value=$field.filter_list.id_option}
                                                        {assign var='value' value=$field.filter_list.value}
                                                        {foreach from=$field.filter_list.list item='option'}
                                                            <option {if ($field.active!=='' && $field.active==$option.$id_option) || ($field.active=='' && $index=='has_post' && $option.$id_option==1 )} selected="selected"{/if} value="{$option.$id_option|escape:'html':'UTF-8'}">{$option.$value|escape:'html':'UTF-8'}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>                                            
                                            {/if}
                                            {if $field.type=='int'}
                                                <label for="{$index|escape:'html':'UTF-8'}_min"><input type="text" placeholder="{l s='Min' mod='ets_extraoptions'}" name="{$index|escape:'html':'UTF-8'}_min" value="{$field.active.min|escape:'html':'UTF-8'}" /></label>
                                                <label for="{$index|escape:'html':'UTF-8'}_max"><input type="text" placeholder="{l s='Max' mod='ets_extraoptions'}" name="{$index|escape:'html':'UTF-8'}_max" value="{$field.active.max|escape:'html':'UTF-8'}" /></label>
                                            {/if}
                                            {if $field.type=='date'}
                                                <fieldset class="form-group"> 
                                                    <div class="input-group ets_eto_datepicker">
                                                        <input id="{$index|escape:'html':'UTF-8'}_min" autocomplete="off" class="form-control" name="{$index|escape:'html':'UTF-8'}_min" placeholder="{l s='From' mod='ets_extraoptions'}" value="{$field.active.min|escape:'html':'UTF-8'}" type="text" autocomplete="off" />
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">
                                                                <i class="icon icon-date"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <fieldset class="form-group"> 
                                                    <div class="input-group ets_eto_datepicker">
                                                        <input id="{$index|escape:'html':'UTF-8'}_max" autocomplete="off" class="form-control" name="{$index|escape:'html':'UTF-8'}_max" placeholder="{l s='To' mod='ets_extraoptions'}" value="{$field.active.max|escape:'html':'UTF-8'}" type="text" autocomplete="off" />
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">
                                                                <i class="icon icon-date"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            {/if}
                                        {else}
                                           {l s=' -- ' mod='ets_extraoptions'}
                                        {/if}
                                    </th>
                                {/foreach}
                                {if $show_action}
                                    <th class="actions">
                                        <span class="pull-right flex">
                                            <input type="hidden" name="post_filter" value="yes" />
                                            {if $show_reset}<a  class="btn btn-warning"  href="{$currentIndex|escape:'html':'UTF-8'}"><i class="icon-eraser"></i> {l s='Reset' mod='ets_extraoptions'}</a> &nbsp;{/if}
                                            <button class="btn btn-default" name="ets_eto_submit_{$name|escape:'html':'UTF-8'}" id="ets_eto_submit_{$name|escape:'html':'UTF-8'}" type="submit">
            									<i class="icon-search"></i> {l s='Filter' mod='ets_extraoptions'}
            								</button>
                                        </span>
                                    </th>
                                {/if}
                            </tr>
                        {/if}
                    </thead>
                    <tbody id="list-{$name|escape:'html':'UTF-8'}" class="list-{$name|escape:'html':'UTF-8'}">
                        {if $field_values}
                        {foreach from=$field_values item='row'}
                            <tr id="{$name|escape:'html':'UTF-8'}-{$row.$identifier|intval}" {if isset($row.read) && !$row.read}class="no-read"{/if} data-id="{$row.$identifier|intval}">
                                {if isset($bulk_actions) && $bulk_actions}
                                     <td class="{$name|escape:'html':'UTF-8'}-more-action">
                                        <input type="checkbox" name="{$name|escape:'html':'UTF-8'}_readed[{$row.$identifier|escape:'html':'UTF-8'}]" class="{$name|escape:'html':'UTF-8'}_readed" value="1" />
                                    </td>
                                {/if}
                                {assign var='i' value=1}
                                {foreach from=$fields_list item='field' key='key'}                             
                                    <td class="{if isset($key) && $key == 'active'}ets_eto_{/if}{$key|escape:'html':'UTF-8'} {if isset($sort)&& $sort==$key && isset($sort_type) && $sort_type=='asc' && isset($field.update_position) && $field.update_position}pointer dragHandle center{/if}{if isset($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}" {if $show_action && !$actions && count($fields_list)==$i}colspan="2"{/if} >
                                        {if isset($field.rating_field) && $field.rating_field}
                                            {if isset($row.$key) && $row.$key > 0}
                                                {for $i=1 to (int)$row.$key}
                                                    <div class="star star_on"></div>
                                                {/for}
                                                {if (int)$row.$key < 5}
                                                    {for $i=(int)$row.$key+1 to 5}
                                                        <div class="star"></div>
                                                    {/for}
                                                {/if}
                                            {else}
                                            
                                                {l s=' -- ' mod='ets_extraoptions'}
                                            {/if}
                                        {elseif $field.type != 'active'}
                                            {if $field.type=='date'}
                                                {if !$row.$key}
                                                --
                                                {else}
                                                    {if $key!='date_from' && $key!='date_to'}
                                                        {dateFormat date=$row.$key full=1}
                                                    {else}
                                                        {dateFormat date=$row.$key full=0}
                                                    {/if}
                                                {/if}
                                            {elseif $field.type=='checkbox'}
                                                <input type="checkbox" name="{$name|escape:'html':'UTF-8'}_boxs[]" value="{$row.$identifier|escape:'html':'UTF-8'}" class="{$name|escape:'html':'UTF-8'}_boxs" />
                                            {elseif $field.type=='input_number'}
                                                {assign var='field_input' value=$field.field}
                                                <div class="qty edit_quantity" data-v-599c0dc5="">
                                                    <div class="ps-number edit-qty hover-buttons" data-{$identifier|escape:'html':'UTF-8'}="{$row.$identifier|escape:'html':'UTF-8'}">
                                                        <input class="form-control {$name|escape:'html':'UTF-8'}_{$field_input|escape:'html':'UTF-8'}" type="number" name="{$name|escape:'html':'UTF-8'}_{$field_input|escape:'html':'UTF-8'}[{$row.$identifier|escape:'html':'UTF-8'}]" value="" placeholder="0" />
                                                        <div class="ps-number-spinner d-flex">
                                                            <span class="ps-number-up"></span>
                                                            <span class="ps-number-down"></span>
                                                        </div>
                                                    </div>
                                                    <button class="check-button" disabled="disabled"><i class="fa fa-check icon icon-check"></i></button>
                                                </div>
                                            {else}
                                                {if isset($field.update_position) && $field.update_position}
                                                    <div class="dragGroup">
                                                    <span class="positions">
                                                {/if}
                                                {if isset($row.$key) && $row.$key!=='' && !is_array($row.$key)}{if isset($field.strip_tag) && !$field.strip_tag}{$row.$key nofilter}{else}{$row.$key|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}{/if}{else}--{/if}
                                                {if isset($row.$key) && is_array($row.$key) && isset($row.$key.image_field) && $row.$key.image_field}
                                                    <a class="ets_mp_fancy" href="{$row.$key.img_url|escape:'html':'UTF-8'}"><img style="{if isset($row.$key.height) && $row.$key.height}max-height: {$row.$key.height|intval}px;{/if}{if isset($row.$key.width) && $row.$key.width}max-width: {$row.$key.width|intval}px;{/if}" src="{$row.$key.img_url|escape:'html':'UTF-8'}" /></a>
                                                {/if} 
                                                {if isset($field.update_position) && $field.update_position}
                                                    </div>
                                                    </span>
                                                {/if}  
                                            {/if}                                     
                                        {else}
                                            {if isset($row.$key) && $row.$key}
                                                <a name="{$name|escape:'html':'UTF-8'}"  href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&change_enabled=0&field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-enabled list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}" title="{l s='Click to disable' mod='ets_extraoptions'}">
                                                    <span class="action_field_content">
                                                        <i class="fa fa-check icon icon-check"></i>
                                                    </span>
                                                    <span class="action_yes">{l s='Yes' mod='ets_extraoptions'}</span>
                                                    <span class="action_no">{l s='No' mod='ets_extraoptions'}</span>
                                                </a>
                                            {else}
                                                <a name="{$name|escape:'html':'UTF-8'}" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&change_enabled=1&field={$key|escape:'html':'UTF-8'}" class="list-action field-{$key|escape:'html':'UTF-8'} list-action-enable action-disabled  list-item-{$row.$identifier|escape:'html':'UTF-8'}" data-id="{$row.$identifier|escape:'html':'UTF-8'}" title="{l s='Click to enable' mod='ets_extraoptions'}">
                                                    <span class="action_field_content">
                                                        <i class="fa fa-remove icon icon-remove"></i>
                                                    </span>
                                                    <span class="action_yes">{l s='Yes' mod='ets_extraoptions'}</span>
                                                    <span class="action_no">{l s='No' mod='ets_extraoptions'}</span>
                                                </a>
                                            {/if} 
                                        {/if}
                                    </td>
                                    {assign var='i' value=$i+1}
                                {/foreach}
                                {if $show_action}
                                    {if $actions}  
                                        <td class="text-right">                            
                                            <div class="btn-group-action">
                                                <div class="btn-group pull-right">
                                                    {if $actions[0]=='view'}
                                                        {if isset($row.child_view_url) && $row.child_view_url}
                                                            <a class="btn btn-default link_view" href="{$row.child_view_url|escape:'html':'UTF-8'}" {if isset($view_new_tab) && $view_new_tab} target="_blank" {/if}><i class="icon icon-search-plus fa fa-search-plus"></i> {l s='View' mod='ets_extraoptions'}</a>
                                                        {elseif !isset($row.action_edit) || $row.action_edit}
                                                            <a class="btn btn-default link_edit" href="{$currentIndex|escape:'html':'UTF-8'}&edit{$name|escape:'html':'UTF-8'}=1&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}" ><i class="icon icon-pencil fa fa-pencil"></i> {l s='Edit' mod='ets_extraoptions'}</a>
                                                        {/if}
                                                    {/if}
                                                    {if $actions[0]=='delete'}
                                                        <a class="btn btn-default" onclick="return confirm('{l s='Do you want to delete this item?' mod='ets_extraoptions' js=1}');" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&del=yes"><i class="icon icon-trash fa fa-trash"></i> {l s='Delete' mod='ets_extraoptions'}</a>
                                                    {/if}
                                                    {if $actions|count >=2 && (!isset($row.action_edit) || $row.action_edit || in_array('action',$actions) || (isset($row.action_delete) &&$row.action_delete) )}
                                                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
                                    						<i class="icon-caret-down"></i>&nbsp;
                                    					</button>
                                                        <ul class="dropdown-menu">
                                                            {foreach from=$actions item='action' key='key'}
                                                                {if $key!=0}
                                                                    {if $action=='delete' && (!isset($row.view_order_url) || (isset($row.view_order_url) && !$row.view_order_url) )}
                                                                        <li><a class="btn btn-default link_delete" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}&del=yes{if isset($row.type)}&type={$row.type|escape:'html':'UTF-8'}{/if}"><i class="fa fa-trash icon icon-trash"></i> {l s='Delete' mod='ets_extraoptions'}</a></li>
                                                                    {/if}
                                                                    {if $action=='view'}
                                                                        {if isset($row.child_view_url) && $row.child_view_url}
                                                                            <li><a class="btn btn-default" href="{$row.child_view_url|escape:'html':'UTF-8'}"><i class="fa fa-search-plus icon icon-search-plus"></i> {l s='View' mod='ets_extraoptions'}</a></li>
                                                                        {else}
                                                                            <li><a class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="fa fa-pencil icon icon-pencil"></i> {l s='Edit' mod='ets_extraoptions'}</a></li>
                                                                        {/if}
                                                                    {/if}
                                                                    {if $action =='edit'}
                                                                        <li><a class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&edit{$name|escape:'html':'UTF-8'}=1&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="fa fa-pencil icon icon-pencil"></i> {l s='Edit' mod='ets_extraoptions'}</a></li>
                                                                    {/if}
                                                                    {if $action =='duplicate'}
                                                                        <li><a class="btn btn-default" href="{$currentIndex|escape:'html':'UTF-8'}&duplicate{$name|escape:'html':'UTF-8'}=1&{$identifier|escape:'html':'UTF-8'}={$row.$identifier|escape:'html':'UTF-8'}"><i class="fa fa-copy icon icon-copy"></i> {l s='Duplicate' mod='ets_extraoptions'}</a></li>
                                                                    {/if}
                                                                {/if}
                                                            {/foreach}
                                                        </ul>
                                                    {/if}
                                                </div>
                                            </div>
                                        </td>
                                    {/if}
                                {/if}
                            </tr>
                        {/foreach}  
                        {/if}  
                        {if !$field_values}
                           <tr class="no-record not_items_found"> <td colspan="100%"><p>{l s='No items found' mod='ets_extraoptions'}</p></td></tr> 
                        {/if}                
                    </tbody>
                </table>
                {if isset($bulk_actions) && $bulk_actions}
                    <select id="bulk_action_{$name|escape:'html':'UTF-8'}" name="bulk_action_{$name|escape:'html':'UTF-8'}" style="display:none;">
                        <option value="">{l s='Bulk actions' mod='ets_extraoptions'}</option>
                        <option value="delete_all">{l s='Delete all selected items' mod='ets_extraoptions'}</option>
                        <option value="duplicate_all">{l s='Duplicate all selected items' mod='ets_extraoptions'}</option>
                    </select>
                {/if}
                {if $paggination}
                    <div class="ets_eto_paggination" style="margin-top: 10px;">
                        {$paggination nofilter}
                    </div>
                {/if}
            {if $show_toolbar}
            </form>
            {else}
            </div>
            {/if}
        </div>
    {/if}
    {if isset($link_back_to_list)}
        <div class="panel-footer">
            <a id="desc-attribute-back" class="btn btn-default btn-primary" href="{$link_back_to_list|escape:'html':'UTF-8'}">
        		<i class="process-icon-back "></i> <span>{l s='Back to list' mod='ets_extraoptions'}</span>
        	</a>
        </div>
    {/if}
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
</div>