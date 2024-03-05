<?php
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
 
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../../config/config.inc.php');
$context = Context::getContext();
$ets_onepagecheckout = Module::getInstanceByName('ets_onepagecheckout');
if($context->employee->id && $context->employee->isLoggedBack())
{
    if (($file_name = Tools::getValue('file_name', false)) && Validate::isFileName($file_name) )
    {
        $id_order = (int)Tools::getValue('id_order');
        $sql = 'SELECT fv.value,fv.file_name FROM `'._DB_PREFIX_.'ets_opc_additionalinfo_field_value` fv
        INNER JOIN `'._DB_PREFIX_.'orders` o ON(fv.id_cart = o.id_cart)
        WHERE o.id_order = "'.(int)$id_order.'" AND fv.value ="'.pSQL($file_name).'"';
        if(($file = Db::getInstance()->getRow($sql)) && $file['value'] &&  file_exists(_PS_ETS_OPC_UPLOAD_DIR_.$file['value']))
        {
            $file_url = _PS_ETS_OPC_UPLOAD_DIR_.$file['value'];
            $ext = Tools::strtolower(Tools::substr(strrchr($file['file_name']? : $file['value'], '.'), 1));
            switch ($ext) {
    			case "pdf": $ctype="application/pdf"; break;
    			case "exe": $ctype="application/octet-stream"; break;
    			case "zip": $ctype="application/zip"; break;
    			case "doc": $ctype="application/msword"; break;
    			case "docx": $ctype="application/msword"; break;
    			case "xls": $ctype="application/vnd.ms-excel"; break;
    			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
    			case "gif": $ctype="image/gif"; break;
    			case "png": $ctype="image/png"; break;
    			case "jpeg":
    			case "jpg": $ctype="image/jpg"; break;
    			default: $ctype="application/force-download";
    		}
            header("Pragma: public"); // required
    		header("Expires: 0");
    		header("X-Robots-Tag: noindex, nofollow", true);
    		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    		header("Cache-Control: private",false); // required for certain browsers
    		header("Content-Type: $ctype");
    		header("Content-Disposition: attachment; filename=\"".($file['file_name'] ? : $file['value'])."\";" );
    		header("Content-Transfer-Encoding: Binary");
            if ($fsize = @filesize($file_url)) {
                header( "Content-Length: ".$fsize);
            }
            ob_clean();
  		    flush();
	        readfile($file_url);
            die();
        }
        die($ets_onepagecheckout->l('File not found','download'));
        
    }
    else
        die($ets_onepagecheckout->l('File not found!', 'download'));
}
else
    die($ets_onepagecheckout->l('You have been logged out','download'));