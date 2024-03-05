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
{if $list_socials }
    <div class="opc_social_form col-xs-12 col-sm-12">
        <div class="opc_solo_or"><span>{l s='OR log in with' mod='ets_onepagecheckout'}</span></div>
        <ul class="opc_social">
            {if $list_socials}
                {foreach from=$list_socials item='social'}
                    <li class="opc_social_item {Tools::strtolower($social)|escape:'html':'UTF-8'} active" data-auth="{$social|escape:'html':'UTF-8'}" title="{if Tools::strtolower($social) == 'paypal'}{l s='Sign in with Paypal' mod='ets_onepagecheckout'}{elseif Tools::strtolower($social) == 'facebook'}{l s='Sign in with Facebook' mod='ets_onepagecheckout'}{elseif Tools::strtolower($social) == 'google'}{l s='Sign in with Google' mod='ets_onepagecheckout'}{/if}">
                        <span class="opc_social_btn medium rounded custom">
                            
                            {if Tools::strtolower($social) == 'paypal'}
                                <i class="ets_svg_icon">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1647 646q18 84-4 204-87 444-565 444h-44q-25 0-44 16.5t-24 42.5l-4 19-55 346-2 15q-5 26-24.5 42.5t-44.5 16.5h-251q-21 0-33-15t-9-36q9-56 26.5-168t26.5-168 27-167.5 27-167.5q5-37 43-37h131q133 2 236-21 175-39 287-144 102-95 155-246 24-70 35-133 1-6 2.5-7.5t3.5-1 6 3.5q79 59 98 162zm-172-282q0 107-46 236-80 233-302 315-113 40-252 42 0 1-90 1l-90-1q-100 0-118 96-2 8-85 530-1 10-12 10h-295q-22 0-36.5-16.5t-11.5-38.5l232-1471q5-29 27.5-48t51.5-19h598q34 0 97.5 13t111.5 32q107 41 163.5 123t56.5 196z"/></svg>
                                </i>
                            {else if Tools::strtolower($social) == 'facebook'}
                                <i class="ets_svg_icon">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1343 12v264h-157q-86 0-116 36t-30 108v189h293l-39 296h-254v759h-306v-759h-255v-296h255v-218q0-186 104-288.5t277-102.5q147 0 228 12z"/></svg>
                                </i>
                            {else if Tools::strtolower($social) == 'google'}
                                <i class="ets_svg_icon">
                                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 786h725q12 67 12 128 0 217-91 387.5t-259.5 266.5-386.5 96q-157 0-299-60.5t-245-163.5-163.5-245-60.5-299 60.5-299 163.5-245 245-163.5 299-60.5q300 0 515 201l-209 201q-123-119-306-119-129 0-238.5 65t-173.5 176.5-64 243.5 64 243.5 173.5 176.5 238.5 65q87 0 160-24t120-60 82-82 51.5-87 22.5-78h-436v-264z"/></svg>
                                </i>
                            {else}
                                <i class="icon icon-{Tools::strtolower($social)|escape:'html':'UTF-8'} fa fa-{Tools::strtolower($social)|escape:'html':'UTF-8'}"></i>
                            {/if} {$social|escape:'html':'UTF-8'}
                        </span>
                    </li>
                {/foreach}
            {/if}
        </ul>
    </div>
{/if}