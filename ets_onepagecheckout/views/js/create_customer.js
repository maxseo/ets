/**
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
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
$(document).ready(function(){
    if(ETS_OPC_CHECK_BOX_NEWSLETTER && $('input[name="newsletter"]').length && $('input[name="newsletter"]:checked').length==0)
    {
        $('input[name="newsletter"]').prop('checked',true)
    }
    if(ETS_OPC_CHECK_BOX_OFFERS && $('input[name="optin"]').length && $('input[name="optin"]:checked').length==0)
    {
        $('input[name="optin"]').prop('checked',true)
    }
});