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

if (!defined('_PS_VERSION_'))
	exit;
class Ets_opc_additionalinfo_field_value extends ObjectModel
{
    public $id_ets_opc_additionalinfo_field;
    public $id_cart;
    public $value;
    public $file_name;
    public static $definition = array(
		'table' => 'ets_opc_additionalinfo_field_value',
		'primary' => 'id_ets_opc_additionalinfo_field_value',
		'multilang' => false,
		'fields' => array(
            'id_ets_opc_additionalinfo_field' => array('type'=> self::TYPE_INT),
            'id_cart' => array('type'=> self::TYPE_INT),
            'value' => array('type' => self::TYPE_STRING),
            'file_name' => array('type'=>self::TYPE_STRING)
        )
	);
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
        $this->context= Context::getContext();
	}
    public function delete()
    {
        if(parent::delete())
        {
            if($this->value && file_exists(_PS_ETS_OPC_UPLOAD_DIR_.$this->value))
            {
                $field = new Ets_opc_additionalinfo_field($this->id_ets_opc_additionalinfo_field);
                if($field->type=='file')
                    @unlink(_PS_ETS_OPC_UPLOAD_DIR_.$this->value);
                
            }
        }
    }
    public static function getIdByField($id_field,$id_cart)
    {
        return Db::getInstance()->getValue('SELECT id_ets_opc_additionalinfo_field_value FROM `'._DB_PREFIX_.'ets_opc_additionalinfo_field_value` WHERE id_cart="'.(int)$id_cart.'"'.($id_field ? ' AND id_ets_opc_additionalinfo_field="'.(int)$id_field.'"':''));
    }
    public static function getFieldValuesByIDCart($id_cart,$id_lang=0)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_opc_additionalinfo_field_value` fv
            INNER JOIN `'._DB_PREFIX_.'ets_opc_additionalinfo_field` f ON (fv.id_ets_opc_additionalinfo_field = f.id_ets_opc_additionalinfo_field)
            LEFT JOIN `'._DB_PREFIX_.'ets_opc_additionalinfo_field_lang` fl ON (f.id_ets_opc_additionalinfo_field = fl.id_ets_opc_additionalinfo_field AND fl.id_lang="'.((int)$id_lang ? : (int)Context::getContext()->language->id).'")
            WHERE fv.id_cart="'.(int)$id_cart.'"';
        return Db::getInstance()->executeS($sql);
    }
    public static function customerDownloadFile($file_name)
    {
        $sql = 'SELECT fv.value,fv.file_name FROM `'._DB_PREFIX_.'ets_opc_additionalinfo_field_value` fv
            INNER JOIN `'._DB_PREFIX_.'orders` o ON(fv.id_cart = o.id_cart)
            WHERE o.id_customer = "'.(int)Context::getContext()->customer->id.'" AND fv.value ="'.pSQL($file_name).'"';
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
    }
 }