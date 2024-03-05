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
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class Ets_opc_db
{
    public static $instance;
    public $is17 = true;

    public function __construct()
    {
        $this->is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
    }
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_opc_db();
        }
        return self::$instance;
    }
    public static function installDb()
    {
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_opc_additionalinfo_field` ( 
        `id_ets_opc_additionalinfo_field` INT(11) NOT NULL AUTO_INCREMENT , 
        `type` VARCHAR(222) NOT NULL , 
        `sort` INT(11) NOT NULL , 
        `required` TINYINT(1) NOT NULL , 
        `enable` TINYINT(1) NOT NULL , 
        `deleted` INT(1) NOT NULL , PRIMARY KEY (`id_ets_opc_additionalinfo_field`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_opc_additionalinfo_field_lang` ( 
        `id_ets_opc_additionalinfo_field` INT(11) NOT NULL , 
        `id_lang` INT(11) NOT NULL ,
        `title` VARCHAR(222) NOT NULL , 
        `description` TEXT NOT NULL , 
        `options` TEXT NOT NULL ,
        PRIMARY KEY (`id_ets_opc_additionalinfo_field`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_opc_additionalinfo_field_value` ( 
        `id_ets_opc_additionalinfo_field_value` INT(11) NOT NULL AUTO_INCREMENT , 
        `id_ets_opc_additionalinfo_field` INT(11) NOT NULL , 
        `id_cart` INT(11) NOT NULL , 
        `value` text, 
        `file_name` VARCHAR(222) NOT NULL ,INDEX(id_ets_opc_additionalinfo_field),INDEX(id_cart), PRIMARY KEY (`id_ets_opc_additionalinfo_field_value`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_opc_login_social` ( 
        `id_customer` INT(11) NOT NULL , 
        `registered_network` TINYINT(1) NOT NULL , 
        `last_login_network` TINYINT(1) NOT NULL , 
        PRIMARY KEY (`id_customer`)) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8');
        return true;
    }
    public static function unInstallDb()
    {
        if($id_meta = (int)Db::getInstance()->getValue('SELECT id_meta FROM `'._DB_PREFIX_.'meta` WHERE page ="module-'.pSQL('ets_onepagecheckout').'-onepagecheckout"'))
        {
            $meta_class = new Meta($id_meta);
            $meta_class->delete();
        }
        if($id_meta = (int)Db::getInstance()->getValue('SELECT id_meta FROM `'._DB_PREFIX_.'meta` WHERE page ="module-'.pSQL('ets_onepagecheckout').'-order"'))
        {
            $meta_class = new Meta($id_meta);
            $meta_class->delete();
        }
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ .'ets_opc_additionalinfo_field`');
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ .'ets_opc_additionalinfo_field_lang`');
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ .'ets_opc_additionalinfo_field_value`');
        Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ .'ets_opc_login_social`');
        return true;
    }
    public static function getModulePayments()
    {
        $sql = 'SELECT m.id_module,m.name, IF(ms.id_module, 1, 0) as active FROM `'._DB_PREFIX_.'module` m
        INNER JOIN `'._DB_PREFIX_.'hook_module` hm ON (m.id_module=hm.id_module)
        INNER JOIN `'._DB_PREFIX_.'hook` h ON (h.id_hook=hm.id_hook)
        LEFT JOIN `'._DB_PREFIX_.'module_shop` ms ON (ms.id_module = m.id_module AND ms.id_shop="'.(int)Context::getContext()->shop->id.'")
        WHERE m.active=1 AND  (h.name="displayPaymentEU" OR h.name="advancedPaymentOptions" OR h.name="paymentOptions")
        GROUP BY m.id_module';
        return Db::getInstance()->executeS($sql);
    }
    public static function updateCustomerSocial($id_customer,$social=false,$create=false)
    {
        if($social)
        {
            if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_opc_login_social` WHERE id_customer='.(int)$id_customer))
            {
                return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_opc_login_social` (id_customer,registered_network,last_login_network) VALUES("'.(int)$id_customer.'","'.($create ? (int)$social:0).'","'.(int)$social.'")');
            }
            else
            {
                return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_opc_login_social` SET last_login_network="'.(int)$social.'" WHERE id_customer='.(int)$id_customer);
            }
        }
        else
        {
            if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_opc_login_social` WHERE id_customer='.(int)$id_customer))
            {
                return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_opc_login_social` SET last_login_network="0" WHERE id_customer='.(int)$id_customer);
            }
        }
        return true;
    }
    public static function checkExistMeta()
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'meta_lang` WHERE url_rewrite ="checkout"') || Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'meta` WHERE page ="module-ets_onepagecheckout-order"');
    }
    public static function getLastCustomerAddressId($id_customer)
    {
        $id_order = Db::getInstance()->getValue('SELECT MAX(id_order) FROM `'._DB_PREFIX_.'orders` WHERE id_customer="'.(int)$id_customer.'"');
        if($id_order && ($order = new Order($id_order)) && $order->id_address_delivery)
            $id_address = $order->id_address_delivery;
        else
            $id_address = Address::getFirstCustomerAddressId($id_customer);
        return $id_address;
    }
    public static function getPaymentMethods($id_country,$type_checkout_options)
    {
        if(Configuration::get('PS_GROUP_FEATURE_ACTIVE'))
        {
            $reflectionClass = new ReflectionClass('Group');
            $group = new Group();
            $reflectionProperty = $reflectionClass->getProperty('ps_group_feature_active');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($group, false);
        }

        $finder = new PaymentOptionsFinder();
        $payment_methods = $finder->present();
        if(Configuration::get('PS_GROUP_FEATURE_ACTIVE'))
        {
            $reflectionProperty->setValue($group, null);
        }

        if($payment_methods)
        {
            foreach(array_keys($payment_methods) as $module_name)
            {
                $payment_method = Module::getInstanceByName($module_name);
                if($id_country && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_country` WHERE id_module="'.(int)$payment_method->id.'" AND id_country="'.(int)$id_country.'" AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
                {
                    unset($payment_methods[$module_name]);
                }
                elseif(Context::getContext()->currency->id && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_currency` WHERE id_module="'.(int)$payment_method->id.'" AND (id_currency="'.(int)Context::getContext()->currency->id.'" OR id_currency="-1")'))
                {
                    if(Context::getContext()->currency->id ==Configuration::get('PS_CURRENCY_DEFAULT'))
                    {
                        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_currency` WHERE id_module="'.(int)$payment_method->id.'" AND (id_currency="'.(int)Context::getContext()->currency->id.'" OR id_currency="-2")'))
                        {
                            unset($payment_methods[$module_name]);
                        }
                    }
                    else
                    {
                        unset($payment_methods[$module_name]);
                    }
                }
                else
                {
                    $delivery_option_selecteds = Context::getContext()->cart->delivery_option ? json_decode(Context::getContext()->cart->delivery_option,true) :Context::getContext()->cart->getDeliveryOption();
                    if($delivery_option_selecteds)
                    {
                        foreach($delivery_option_selecteds as $ids_carrier)
                        {
                            $id_carriers = explode(',',trim($ids_carrier,','));
                            foreach($id_carriers as $idcarrier)
                            {
                                $carrier = new Carrier($idcarrier);
                                if($carrier->id && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_carrier` WHERE id_reference="'.(int)$carrier->id_reference.'" AND id_module="'.(int)$payment_method->id.'" AND id_shop="'.(int)Context::getContext()->shop->id.'"'))
                                {
                                    unset($payment_methods[$module_name]);
                                }
                            }
                        }

                    }

                }
                if(isset($payment_methods[$module_name]))
                {
                    if($type_checkout_options=='guest' || $type_checkout_options=='create')
                    {
                        if($type_checkout_options=='guest')
                        {
                            if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_group` WHERE id_module="'.(int)$payment_method->id.'" AND id_group="'.(int)Configuration::get('PS_GUEST_GROUP').'"'))
                            {
                                unset($payment_methods[$module_name]);
                            }
                        }
                        elseif($type_checkout_options=='create')
                        {
                            if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_group` WHERE id_module="'.(int)$payment_method->id.'" AND id_group="'.(int)Configuration::get('PS_CUSTOMER_GROUP').'"'))
                            {
                                unset($payment_methods[$module_name]);
                            }
                        }
                    }
                    elseif(Context::getContext()->customer->isLogged())
                    {
                        $groups = Context::getContext()->customer->getGroups();
                        $ok= false;
                        if($groups)
                        {
                            foreach($groups as $id_group)
                            {
                                if(Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_group` WHERE id_module="'.(int)$payment_method->id.'" AND id_group="'.(int)$id_group.'"'))
                                {
                                    $ok=true;
                                    break;
                                }
                            }
                        }
                        if(!$ok)
                            unset($payment_methods[$module_name]);
                    }elseif(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'module_group` WHERE id_module="'.(int)$payment_method->id.'" AND id_group="'.(int)Configuration::get('PS_GUEST_GROUP').'"'))
                    {
                        unset($payment_methods[$module_name]);
                    }
                }
                unset($payment_method);
            }
        }
        return $payment_methods;
    }
    public static function getDeliveryPriceByPrice($id_carrier,$totalCart,$id_zone)
    {
        $sql = 'SELECT r.`delimiter1`
				FROM `' . _DB_PREFIX_ . 'delivery` d
				LEFT JOIN `' . _DB_PREFIX_ . 'range_price` r ON d.`id_range_price` = r.`id_range_price`
				WHERE d.`id_zone` = ' . (int) $id_zone . '
					AND ' . (float) $totalCart . ' < r.`delimiter1`
                    AND d.`price`=0
					AND d.`id_carrier` = ' . (int)$id_carrier . '
					' . Carrier::sqlDeliveryRangeShop('range_price') . '
				ORDER BY r.`delimiter1` ASC';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    public static function updateDeliveryAddress($id_address)
    {
        return  Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'cart_product` set id_address_delivery="'.(int)$id_address.'" WHERE id_cart='.(int)Context::getContext()->cart->id);
    }
}
