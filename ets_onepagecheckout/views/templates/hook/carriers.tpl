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
{if $carriers}
    <table class="carrier">
        <thead>
            <tr>
                <th>{l s='Carrier logo' mod='ets_onepagecheckout'}</th>
                <th>{l s='Carrier name' mod='ets_onepagecheckout'}</th>
                <th>{l s='Delay' mod='ets_onepagecheckout'}</th>
                <th>{l s='Status' mod='ets_onepagecheckout'}</th>
                <th>{l s='Position' mod='ets_onepagecheckout'}</th>
                <th>{l s='Setting' mod='ets_onepagecheckout'}</th>
            </tr>
        </thead>
        {foreach from=$carriers item='carrier'}
            <tr>
                <td>
                    {if isset($carrier.logo)}
                        <img src="{$carrier.logo|escape:'html':'UTF-8'}" style="width:50px" />
                    {/if}
                </td>
                <td>{$carrier.name|escape:'html':'UTF-8'}</td>
                <td>{$carrier.delay|escape:'html':'UTF-8'}</td>
                <td>
                    {if $carrier.active}
                        <a class="list-action field-active list-action-enable action-disabled list-item-{$carrier.id_carrier|intval}" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=ets_onepagecheckout&id_carrier={$carrier.id_carrier|intval}&table=carrier&change_enabled=0&field=active" data-id="{$carrier.id_carrier|intval}" title="{l s='Click to disable' mod='ets_onepagecheckout'}">
                            <i class="icon-check"></i>
                        </a>
                    {else}
                        <a class="list-action field-active list-action-enable action-enabled list-item-{$carrier.id_carrier|intval}" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=ets_onepagecheckout&id_carrier={$carrier.id_carrier|intval}&table=carrier&change_enabled=1&field=active" data-id="{$carrier.id_carrier|intval}" title="{l s='Click to enable' mod='ets_onepagecheckout'}">
                            <i class="icon-remove"></i>
                        </a>
                    {/if}    
                </td>
                <td>
                    {($carrier.position+1)|intval}
                </td>
                <td><a target="_blank" href="{$carrier.url|escape:'html':'UTF-8'}" title="{l s='Settings' mod='ets_onepagecheckout'}" >{l s='Settings' mod='ets_onepagecheckout'}</a></td>
            </tr>
        {/foreach}
    </table>
    
{/if}