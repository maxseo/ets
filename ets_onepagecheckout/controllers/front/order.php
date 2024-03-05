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
class Ets_onepagecheckoutOrderModuleFrontController extends ModuleFrontController
{
    public $errors = array();
    public function __construct()
	{
		parent::__construct();
        $smarty = $this->context->smarty;
        smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
        smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
	}

    public function displayAjaxRefresh()
    {
        if (Configuration::isCatalogMode()) {
            return;
        }
        ob_end_clean();
        header('Content-Type: application/json'); 
        if(Module::isEnabled('ps_checkout') && file_exists(_PS_MODULE_DIR_.'ps_checkout/views/js/front.js'))
        {
            $dir_js = $this->module->getBaseLink().'/modules/ps_checkout/views/js/front.js?v='.time();
        }
        $id_country = (int)Tools::getValue('id_country');
        $isAvailable = $this->areProductsAvailable();
        $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group= new Group($id_group);
        if($group->price_display_method)
            $tax=false;
        else
            $tax=true;
        $tax_text = ($tax ? $this->module->l('tax incl','order') : $this->module->l('tax excl','order'));
        $del_product = (int)Tools::getValue('del_product');
        die(json_encode([
            'isAvailable' => $isAvailable!==true ? $this->module->displayError($isAvailable):'',
            'cart_detailed' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart-detailed.tpl'),
            'cart_detailed_totals' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart-detailed-totals.tpl'),
            'cart_summary_items_subtotal' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart-summary-items-subtotal.tpl'),
            'cart_summary_totals' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart-summary-totals.tpl'),
            'cart_detailed_actions' => '',
            'dir_js' => isset($dir_js) ? $dir_js:'',
            'cart_voucher' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart-voucher.tpl'),
            'shipping_methods' => $del_product? $this->displayBlockShippingMethods(0,$id_country):'',
            'payment_methods' => $del_product ? $this->displayBlockPaymentMethods(0,$id_country):'',
            'hookDisplayShopLicenseField' => Module::isEnabled('ets_shoplicense') ? Hook::exec('displayShopLicenseField') :'',
            'is_virtual_cart' => $this->context->cart->isVirtualCart(),
            'gift_label' => sprintf($this->module->l('I would like my order to be gift wrapped (additional cost of %s %s.)','order'), Tools::displayPrice($this->context->cart->getGiftWrappingPrice($tax,null)),$tax_text),
        ]));
    }
    public function init()
    {
        parent::init();
        $this->php_self='order';
        if(Tools::isSubmit('submitChangeGift'))
        {
            $gift = (int)Tools::getValue('gift');
            $this->context->cart->gift= $gift ? 1 :0;
            $this->context->cart->update();
            die(
                json_encode(
                    array(
                        'ok' => true,
                    )
                )
            );
        }
        if(Tools::isSubmit('ets_opc_change_payment') && ($payment = Tools::getValue('payment')) && Validate::isModuleName($payment))
        {
            $this->context->cookie->ets_opc_payment = $payment;
            $this->context->cookie->write();
            die(
                json_encode(
                    array(
                        'success'=>true,
                    )
                )
            );
        }
        if(!$this->module->checkValidateOnepage())
            Tools::redirect($this->context->link->getPageLink('order'));
        if(!$this->context->cart->getProducts())
        {
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'cart_detailed' => 'empty',
                            'url_cart' => $this->context->link->getPageLink('cart'),
                            'text_info' => $this->module->l('There are no more items in your cart','order'),
                        )
                    )
                );
            }
            Tools::redirect($this->context->link->getPageLink('index'));
        }
        if(Tools::isSubmit('id_country') && ($id_country = (int)Tools::getValue('id_country')))
        {
            Context::getContext()->country = new Country($id_country,$this->context->language->id);
        }
    }
    public function initContent()
	{
		parent::initContent();
        $meta = Meta::getMetaByPage('module-'.$this->module->name.'-order',$this->context->language->id);
        if($meta && ($id_meta = $meta['id_meta']))
        {
            $meta_class = new Meta($id_meta,$this->context->language->id);
        }
        $metas =array(
            'title' => isset($meta_class) && $meta_class->title ? $meta_class->title : $this->module->l('Checkout','order'),
            'meta_title' => isset($meta_class) && $meta_class->title ? $meta_class->title : $this->module->l('Checkout','order'),
            'description' => isset($meta_class) && $meta_class->description ? $meta_class->description : $this->module->l('Complete your order','order'),
            'keywords' => isset($meta_class) && $meta_class->keywords ? $meta_class->keywords : '',
            'robots' => 'index',
        );
        $body_classes = array(
            'lang-'.$this->context->language->iso_code => true,
            'lang-rtl' => (bool) $this->context->language->is_rtl,
            'country-'.$this->context->country->iso_code => true,                                   
        );
        $page = array(
            'title' => '',
            'canonical' => '',
            'meta' => $metas,
            'page_name' => 'checkout',
            'body_classes' => $body_classes,
            'admin_notifications' => array(),
        ); 
        $this->context->smarty->assign(array('page' => $page)); 
        $id_country = (int)Tools::getValue('id_country');
        if(Tools::isSubmit('submitCustomerLogin'))
        {
            $this->_submitCustomerLogin();
        }
        if(Tools::isSubmit('ajax') && Tools::isSubmit('getAddressFrom'))
            $this->displayAjaxAddressForm();
        if(Tools::isSubmit('ajax') && Tools::isSubmit('getAddressStates'))
        {
            $this->displayAjaxAddressStates();
        }
        if(Tools::isSubmit('updateCarrier'))
        {
            $this->_updateCarrier();
        }
        if(Tools::isSubmit('submitCompleteMyOrder'))
        {
            $this->_submitCompleteMyOrder();
        }
        if(Tools::isSubmit('changeTypeCheckoutOptions'))
        {
            die(
                json_encode(
                    array(
                        'payment_methods' => $this->displayBlockPaymentMethods(0,$id_country)
                    )
                )
            );
        }
        if(Tools::isSubmit('getShippingMethodByStates'))
        {
            die(
                json_encode(
                    array(
                        'shipping_methods' => $this->displayBlockShippingMethods(0,$id_country)
                    )
                )
            );
        }
        $this->context->smarty->assign(
            array(
                'path' => $this->module->getBreadCrumb(),

                'breadcrumb' => $this->module->is17 ? $this->module->getBreadCrumb() : false,
                'html_content' => $this->_initContent(),

            )
        );
        if($this->module->is17)
            $this->setTemplate('module:'.$this->module->name.'/views/templates/front/onepagecheckout.tpl');      
        else        
            $this->setTemplate('onepagecheckout_16.tpl'); 
    }
    public function _initContent()
    {
        $id_customer = ($this->context->customer->id) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group= new Group($id_group);
        if($group->price_display_method)
            $tax=false;
        else
            $tax=true;
        $id_address = $this->context->customer->isLogged() ?  Ets_opc_db::getLastCustomerAddressId($this->context->customer->id):0;
        if($id_address!=$this->context->cart->id_address_delivery)
        {
            $this->context->cart->id_address_delivery = $id_address;
            $this->context->cart->update();
        }
        $list_socials = $this->module->getListSocialLogin();
        $address_field = Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD') ? explode(',',Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD')):array();
        $isAvailable = $this->areProductsAvailable();
        $tax_text = ($tax ? $this->module->l('tax incl','order') : $this->module->l('tax excl','order'));
        $this->context->smarty->assign(
            array(
                'isps18' => version_compare(_PS_VERSION_, '8.0.0', '>=') ? true : false,
                'use_address_invoice' => in_array('use_address_invoice',$address_field) ? true : false,
                'css_extra' => $this->module->getCssExtraDesign(),
                'gdpr_text' => Configuration::get('PSGDPR_CREATION_FORM',$this->context->language->id) ? : $this->module->l('I agree to the terms and conditions and the privacy policy','order'),
                'list_socials' => $list_socials,
                'customer_logged' => $this->context->customer->logged && !$this->context->customer->is_guest,
                'is_guest' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED') && $this->context->customer->is_guest,
                'shipping_address' => $this->displayBlockDeliveryAddress($id_address),
                'invoice_address' => $this->displayBlockInvoiceAddress($id_address),
                'checkout_customer' =>$this->context->customer,
                'customer_block' => $this->displayBlockCustomerInFo(),
                'shipping_methods' => $this->displayBlockShippingMethods($id_address),
                'payment_methods' => $this->displayBlockPaymentMethods($id_address),
                'additional_info' => $this->displayBlockAdditionalInfo(),
                'shipping_cart' => $this->displayBlockShippingCart(),
                'terms_page' => new CMS(3,$this->context->language->id),
                'list_countries' => Country::getCountries($this->context->language->id,true),
                'ETS_OPC_GOOGLE_KEY_API' => Configuration::get('ETS_OPC_GOOGLE_KEY_API'),
                'ETS_OPC_SHOW_CUSTOMER_REASSURANCE' => Configuration::get('ETS_OPC_SHOW_CUSTOMER_REASSURANCE'),
                'link' => $this->context->link,
                'date_format_lite' => $this->context->language->date_format_lite,
                'date_eg' => date($this->context->language->date_format_lite,strtotime('31-05-1970')),
                'NW_CONDITIONS' => Configuration::get('NW_CONDITIONS',$this->context->language->id),
                'CUSTPRIV_MSG_AUTH' => Configuration::get('CUSTPRIV_MSG_AUTH',$this->context->language->id),
                'recyclable' => $this->context->cart->recyclable,
                'gift' => $this->context->cart->gift,
                'is_virtual_cart' => $this->context->cart->isVirtualCart(),
                'html_gift_products' => Module::isEnabled('ets_promotion') ? Module::getInstanceByName('ets_promotion')->displayListGiftProducts():'',
                'gift_message' => $this->context->cart->gift_message,
                'hookDisplayShopLicenseField' => Module::isEnabled('ets_shoplicense') ? Hook::exec('displayShopLicenseField') :'',
                'isAvailable' => $isAvailable!==true ? $this->module->displayError($isAvailable) : '',
                'gift_label' => sprintf($this->module->l('I would like my order to be gift wrapped (additional cost of %s %s.)','order'), Tools::displayPrice($this->context->cart->getGiftWrappingPrice($tax,$id_address)),$tax_text),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/onepagecheckout.tpl');
    }
    public function displayBlockAdditionalInfo()
    {
        $fields = Ets_opc_additionalinfo_field::getListField($this->context->language->id,true);
        if($fields)
        {
            foreach($fields as &$field)
            {
                if($field['options'])
                {
                    $values = array();
                    foreach(explode("\n",$field['options']) as $options)
                    {
                        $options = explode('|',$options);
                        if(isset($options[1]) && $options[1])
                        {
                            $val = $options[1];
                        }
                        else
                            $val = $options[0];
                        $val_texts = explode(':',$val);
                        if(isset($val_texts[1]) && trim($val_texts[1])=='default')
                            $default= true;
                        else
                            $default=false;
                        $values[] = array(
                            'value' =>trim($val_texts[0]), 
                            'text' => trim(isset($options[1]) ? $options[0] :$val_texts[0] ),
                            'default' => trim($default) 
                        );
                    }
                    $field['options'] = $values;
                }
            }
            $this->context->smarty->assign(
                array(
                    'fields' => $fields,
                )
            );
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/additional_info.tpl');
        }
        return '';
    }
    public function displayBlockDeliveryAddress($id_address=0)
    {
        $address_field = Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD') ? explode(',',Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD')):array();
        $this->context->smarty->assign(
            array(
                'use_address' => in_array('use_address',$address_field) ? true : false,
                'use_address_invoice' => in_array('use_address_invoice',$address_field) ? true : false,
                'address_form' => $this->renderFormAddress('shipping_address',$id_address),
                'list_address' => ($this->context->cookie->logged && $this->context->customer->id) || $this->context->customer->logged ? $this->context->customer->getAddresses($this->context->language->id):array(),
                'id_address' => $id_address, 
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/delivery_address.tpl');
    }
    public function displayBlockInvoiceAddress($id_address=0)
    {
        $address_field = Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD') ? explode(',',Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD')):array();
        $this->context->smarty->assign(
            array(
                'use_address' => in_array('use_address',$address_field) ? true : false,
                'address_form' => $this->renderFormAddress('invoice_address',$id_address),
                'list_address' => ($this->context->cookie->logged && $this->context->customer->id) || $this->context->customer->logged ? $this->context->customer->getAddresses($this->context->language->id):array(),
                'id_address' => $id_address, 
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/invoice_address.tpl');
    }
    public function renderFormAddress($address_type,$id_address=0)
    {
        if((int)$id_address)
            $address = new Address((int)$id_address);
        else
            $address = new Address();
        $iso_state = Tools::getValue('iso_state');
        $id_country_default = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        if($iso_state && Validate::isStateIsoCode($iso_state))
            $id_state_selected = (int)State::getIdByIso($iso_state);
        else
            $id_state_selected =0;
        $this->context->smarty->assign(
            array(
                'address_type' => $address_type,
                'class_address' => $address,
                'id_state_selected' => $id_state_selected,
                'countries' => Country::getCountries($this->context->language->id,true),
                'id_country' => $address->id_country ? : ($this->context->country->id ? :$id_country_default),
                'field_address' => $this->module->getListFieldsAddress(),
                'states' => State::getStatesByIdCountry($address->id_country ? : ($this->context->country->id ? :$id_country_default),true),
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/form_address.tpl');
    }
    public function displayAjaxAddressForm()
    {
        ob_end_clean();
        header('Content-Type: application/json');
        $address_type = Tools::getValue('address_type','shipping_address');
        if(!in_array($address_type,array('shipping_address','invoice_address')))
            $address_type = 'shipping_address';
        $id_address = (int)Tools::getValue('id_address');
        die(json_encode(array(
            'address_form' => $this-> renderFormAddress($address_type,$id_address),
            'shipping_methods' => $address_type=='shipping_address' ? $this->displayBlockShippingMethods($id_address):'',
        )));
    }
    public function displayAjaxAddressStates()
    {
       $id_country = (int)Tools::getValue('id_country');
       $address_type = Tools::getValue('address_type','shipping_address'); 
       if(!in_array($address_type,array('shipping_address','invoice_address')))
            $address_type = 'shipping_address';
       if($id_country)
       {
            $states =  State::getStatesByIdCountry($id_country,true);
            if($states)
            {
                $this->context->smarty->assign(
                    array(
                        'states' => $states,
                        'address_type'=> $address_type,
                    )
               );
               die(
                    json_encode(
                        array(
                            'states' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/states.tpl'),
                            'shipping_methods' => $address_type=='shipping_address' ? $this->displayBlockShippingMethods(0,$id_country):'',
                        )
                    )
               );
            }
       }
       die(
            json_encode(
                array(
                    'states' => '',
                    'shipping_methods' =>$address_type=='shipping_address' ?  $this->displayBlockShippingMethods(0,$id_country):'',
                )
            )
       );
    }
    public function displayBlockShippingMethods($id_address=0,$id_country=0)
    {
        if($this->context->cart->isVirtualCart())
            return false;
        if(!$id_country)
        {
            if($id_address)
            {
                $address = new Address($id_address);
                $id_country = $address->id_country;
            }
            else
                $id_country = $this->context->country->id ? :(int)Configuration::get('PS_COUNTRY_DEFAULT');
        }
        $country = new Country($id_country);
        $delivery_option_list = $this->context->cart->getDeliveryOptionList($country,true);
        if($delivery_option_list)
        {
            foreach($delivery_option_list as $id_address => &$option_list)
            {
                foreach($option_list as $key => &$option)
                {
                    
                   $delivery = array();
                   if($option['carrier_list']) 
                   {
                        foreach($option['carrier_list'] as $id_carrier=> $carrier)
                        {
                            $delivery['extraContent'] = '';
                            if (isset($carrier['instance']) && Validate::isLoadedObject($carrier['instance']) &&  $carrier['instance']->is_module) {
                                if ($moduleId = Module::getModuleIdByName($carrier['instance']->external_module_name)) {
                                    $carrier['id'] = $carrier['instance']->id;
                                    $delivery['extraContent'] = Hook::exec('displayCarrierExtraContent', ['carrier' => $carrier], $moduleId);
                                }
                            }
                            $delivery['id_carrier'] = $id_carrier;
                            $delivery['name'] = $carrier['instance']->name;
                            $delivery['delay'] = $carrier['instance']->delay[$this->context->language->id];
                            $delivery['logo'] = file_exists(_PS_IMG_DIR_.'s/'.$carrier['instance']->id.'.jpg') ? $this->context->link->getMediaLink(_PS_IMG_.'s/'.$carrier['instance']->id.'.jpg'):false;
                            $delivery['total_price_with_tax'] = $option['total_price_with_tax'];
                            $delivery['total_price_without_tax'] = $option['total_price_without_tax'];
                            $delivery['is_free'] = $option['is_free'];
                            $delivery['delivery_option'] ='{"'.$id_address.'":"'.$key.'"}';
                        }
                   }
                   $option = $delivery;
                }
            }
        }
        $delivery_option_selected = $this->context->cart->delivery_option ? json_decode($this->context->cart->delivery_option,true):$this->context->cart->getDeliveryOption();
        $this->context->smarty->assign(
            array(
                'use_taxes' => 1,
                'priceDisplay' => $this->module->checkDisplayTax($this->context->customer->id),
                'delivery_option_list' => $delivery_option_list,
                'delivery_option_selected' => $delivery_option_selected,
                'hookDisplayAfterCarrier' => Hook::exec('displayAfterCarrier')
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/shippings.tpl');
    }
    public function displayBlockPaymentMethods($id_address=0,$id_country=0)
    {
        if(!$id_country)
        {
            if($id_address)
            {
                $address = new Address($id_address);
                $id_country = $address->id_country;
            }
            else
                $id_country = $this->context->country->id ? :Configuration::get('PS_COUNTRY_DEFAULT');
        }
        if ($this->context->cart->getOrderTotal(true) == 0) {
            return false;
        } else {
            $type_checkout_options = Tools::getValue('type_checkout_options','login');
            if(!in_array($type_checkout_options,array('login','guest','create')))
                $type_checkout_options ='login';
            if($payment_methods = Ets_opc_db::getPaymentMethods($id_country,$type_checkout_options))
            {
                foreach($payment_methods as $module_name=> &$payment)
                {
                    if($payment)
                    {
                        foreach($payment as &$module)
                        {
                            $module['logo'] = $this->context->link->getMediaLink(_MODULE_DIR_.$module_name.'/logo.png');
                        }
                        unset($module);
                    }
                    else
                        unset($payment_methods[$module_name]);
                }
                unset($payment);
            }
            $this->context->smarty->assign(
                array(
                    'payment_methods' => $payment_methods,
                    'payment_selected' => $this->context->cookie->ets_opc_payment ? :Configuration::get('ETS_OPC_PAYMENT_DEFAULT'),
                )
            );
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/payment_methods.tpl');
        }
    }
    public function displayBlockShippingCart()
    {
        $presenter = $this->context->controller->cart_presenter;
        $shouldSeparateGifts = true;
        $presented_cart = $presenter->present($this->context->cart, $shouldSeparateGifts,true);
        $this->context->smarty->assign([
            'cart' => $presented_cart,
            'static_token' => Tools::getToken(false),
        ]);
        if (count($presented_cart['products']) > 0) {
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart.tpl');
        } else {
            $this->context->smarty->assign([
                'allProductsLink' => $this->context->link->getCategoryLink(Configuration::get('PS_HOME_CATEGORY')),
            ]);
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/cart_empty.tpl');
        }
    }
    public function displayBlockCustomerInFo()
    {
        if(($this->context->cookie->logged && $this->context->customer->id) || $this->context->customer->logged)
        {
            $this->context->smarty->assign(
                array(
                    'checkout_customer' => $this->context->customer,
                )
            );
            return $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name.'/views/templates/hook/my-account.tpl');
        }
        
    }
    public function _submitCustomerLogin()
    {
        Hook::exec('actionBeforeAuthentication');
        $passwd = trim(Tools::getValue('password'));
        $email = trim(Tools::getValue('email'));
        $field_errors = array();
        if (empty($email)) {
            $field_errors[] = array(
                'field'=> 'customer_login_email',
                'error' => $this->module->l('Email is required.','order'),                        
            );
        } elseif (!Validate::isEmail($email)) {
            $field_errors[] = array(
                'field'=> 'customer_login_email',
                'error' => $this->module->l('Email is not valid.','order'),                        
            );
        }
        if (empty($passwd)) {
            $field_errors[] = array(
                'field'=> 'customer_login_password',
                'error' => $this->module->l('Password is required.','order'),                        
            );
        } elseif (method_exists('Validate','isPasswd') && !Validate::isPasswd($passwd)) {
            $field_errors[] = array(
                'field'=> 'customer_login_password',
                'error' => $this->module->l('Password is not valid.','order'),
            );
        }
        if((!Configuration::get('ETS_OPC_LOGIN_CAPTCHA_ENABLED') || $this->checkCaptcha($field_errors)) && !$field_errors) {
            $customer = new Customer();
            $authentication = $customer->getByEmail(trim($email), trim($passwd));
            if (isset($authentication->active) && !$authentication->active) {
                $this->errors[] = $this->module->l('Your account isn\'t available at this time, please contact us','order');
            } elseif (!$authentication || !$customer->id) {
                $this->errors[] = $this->module->l('Authentication failed.','order');
            } else {
                $this->context->cookie->id_customer = (int)($customer->id);
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->logged = 1;
                $customer->logged = 1;
                $this->context->cookie->is_guest = $customer->isGuest();
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->email = $customer->email;
                $this->context->customer = $customer;

                if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                    $this->context->cart = new Cart($id_cart);
                } else {
                    $id_carrier = (int)$this->context->cart->id_carrier;
                    $this->context->cart->id_carrier = 0;
                    $this->context->cart->setDeliveryOption(null);
                    $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                    $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                }
                $this->context->cart->id_customer = (int)$customer->id;
                $this->context->cart->secure_key = $customer->secure_key;

                if (Tools::isSubmit('ajax') && isset($id_carrier) && $id_carrier) {
                    $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                    $this->context->cart->setDeliveryOption($delivery_option);
                }

                $this->context->cart->save();
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
                $this->context->cookie->write();
                $this->context->cart->autosetProductAddress();
                if(method_exists($this->context,'updateCustomer'))
                    $this->context->updateCustomer($customer);
                else
                    $this->updateContext($customer);
                Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
                Cache::getInstance()->flush();
            }
        }
        if (Tools::isSubmit('ajax')) {
            if($field_errors)
            {
                die(
                    json_encode(
                        array(
                            'hasError' => true,
                            'field_errors' => $field_errors,
                            'errors' => $this->module->displayError($this->module->l('Please fill in all required fields with valid information','order')),
                        )
                    )
                );
            }
            elseif($this->errors)
            {
               $return = array(
                    'hasError' => true,
                    'errors' => $this->module->displayError($this->errors),
                ); 
            }
            else
            {
                $id_address = Address::getFirstCustomerAddressId($this->context->customer->id);
                $return = array(
                    'hasError' => false,
                    'errors' => false,
                    'invoice_address' => $this->displayBlockInvoiceAddress($id_address),
                    'shipping_address' => $this->displayBlockDeliveryAddress($id_address),
                    'customer_block' => $this->displayBlockCustomerInFo(),
                    'shipping_methods' => $this->displayBlockShippingMethods($id_address),
                    'payment_methods' => $this->displayBlockPaymentMethods($id_address),
                    'shipping_cart' => $this->displayBlockShippingCart(),
                ); 
            }
            $this->ajaxDie(json_encode($return));
        } 
    }
    public function _updateCarrier()
    {
        $id_country = (int)Tools::getValue('id_country');
        $delivery_option = Tools::getValue('delivery_option');
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter()
        );

        $checkout_session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );
        if (Tools::getIsset('delivery_option') && is_array($delivery_option) && Ets_onepagecheckout::validateArray($delivery_option) ) {
            $checkout_session->setDeliveryOption(
                $delivery_option 
            );
        }
        Hook::exec('actionCarrierProcess', array('cart' => $checkout_session->getCart()));
        $total_has_free = 0;
        $total_more_free = 0;
        $total_percent_free =0;
        if($delivery_option && is_array($delivery_option) && Ets_onepagecheckout::validateArray($delivery_option) && Configuration::get('ETS_OPC_SHOW_SHIPPING'))
        {
            foreach($delivery_option as $delivery)
            {
                
                $id_carriers= explode(',',$delivery);
                $id_currency_default = (int)Configuration::get('PS_CURRENCY_DEFAULT');
                $totalCart = $this->context->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
                $totalShipping = $this->context->cart->getOrderTotal(true,Cart::ONLY_SHIPPING);
                if($totalShipping)
                {
                    if($this->context->currency->id!=$id_currency_default)
                        $totalCart = Tools::convertPrice($totalCart, $this->context->currency->id, false);
                    $id_zone = $this->module->hookActionGetIDZoneByAddressID();
                    if($id_carriers && is_array($id_carriers) && Ets_onepagecheckout::validateArray($id_carriers,'isUnsignedInt'))
                    {
                        foreach($id_carriers as $id_carrier)
                        {
                            if($id_carrier)
                            {
                                $carrier = new Carrier($id_carrier);
                                if((float)Configuration::get('PS_SHIPPING_HANDLING')==0 || !$carrier->shipping_handling)
                                {
                                    
                                    if($total_cart_free = (float)Ets_opc_db::getDeliveryPriceByPrice($id_carrier,$totalCart,$id_zone))
                                    {
                                        if($total_cart_free > (float)$total_has_free)
                                            $total_has_free = $total_cart_free;
                                    }
                                    else
                                    {
                                        $total_has_free = 0;
                                        break;
                                    }
                                }
                                
                            }
                            
                        }
                    }
                }
                
            }
            if($total_has_free!=0)
            {
                $total_more_free = Tools::convertPrice($total_has_free- $totalCart);
                $total_percent_free = $totalCart*100/$total_has_free;
                if($this->context->currency->id!= $id_currency_default)
                    $total_has_free = Tools::convertPrice($total_has_free);
            }
        }
        die(
            json_encode(
                array(
                    'payment_methods' => $this->displayBlockPaymentMethods(0,$id_country),
                    'total_has_free' => $total_has_free ? Tools::displayPrice($total_has_free) :false,
                    'total_more_free' => $total_more_free ? Tools::displayPrice($total_more_free) :false,
                    'total_percent_free' => $total_percent_free,
                )
            )
        );
    }
    public function _submitCompleteMyOrder()
    {
        $type_checkout_options = Tools::getValue('type-checkout-options','login');
        $field_errors = array();
        if($type_checkout_options=='login')
        {
            if(!$this->context->customer->logged)
            {
                die(
                    json_encode(
                        array(
                            'hasError' => true,
                            'errors' => $this->module->displayError($this->module->l('You need to log in before checking out.','order')),
                        )
                    )
                );
            }
        }
        else
        {
            if($type_checkout_options=='guest')
            {
                $guest_field = Configuration::get('ETS_OPC_GUEST_DISPLAY_FIELD') ? explode(',',Configuration::get('ETS_OPC_GUEST_DISPLAY_FIELD')):array();
                $guest_field_required = Configuration::get('ETS_OPC_GUEST_DISPLAY_FIELD_REQUIRED') ? explode(',',Configuration::get('ETS_OPC_GUEST_DISPLAY_FIELD_REQUIRED')):array();
                $customer_guest = Tools::getValue('customer_guest');
                if(!is_array($customer_guest) || !Ets_onepagecheckout::validateArray($customer_guest))
                    $this->errors[] = $this->module->l('Guest data is not valid','order');
                else{
                    if(!isset($customer_guest['firstname']) || !$customer_guest['firstname'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_firstname',
                            'error' => $this->module->l('First name is required','order')
                        );
                    }
                    if(!isset($customer_guest['lastname']) || !$customer_guest['lastname'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_lastname',
                            'error' => $this->module->l('Last name is required','order')
                        );
                    }
                    if(!isset($customer_guest['email']) || !$customer_guest['email'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_email',
                            'error' => $this->module->l('Email is required','order')
                        );
                    }
                    if(Configuration::get('PSGDPR_CREATION_FORM_SWITCH') && in_array('psgdpr',$guest_field_required) && Module::isEnabled('psgdpr') &&  (!isset($customer_guest['psgdpr']) || (isset($customer_guest['psgdpr']) && !$customer_guest['psgdpr'])))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_psgdpr_check',
                            'error' => $this->module->l('I agree to the terms and conditions and the privacy policy is required','order')
                        );
                    }
                    if(Configuration::get('PS_CUSTOMER_OPTIN') && in_array('optin',$guest_field_required) && (!isset($customer_guest['optin']) || (isset($customer_guest['optin']) && !$customer_guest['optin'])) )
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_optin_check',
                            'error' => $this->module->l('Receive offers from our partners is required','order')
                        );
                    }
                    if(in_array('newsletter',$guest_field_required) && Module::isEnabled('ps_emailsubscription') &&  (!isset($customer_guest['newsletter']) || (isset($customer_guest['newsletter']) && !$customer_guest['newsletter'])))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_newsletter_check',
                            'error' => $this->module->l('Sign up for our newsletter is required','order')
                        );
                    }
                    if(in_array('customer_privacy',$guest_field_required) && Module::isEnabled('ps_dataprivacy') &&  (!isset($customer_guest['customer_privacy']) || (isset($customer_guest['customer_privacy']) && !$customer_guest['customer_privacy'])))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_customer_privacy_check',
                            'error' => $this->module->l('Customer data privacy is required','order')
                        );
                    }
                    if(in_array('social_title',$guest_field) && in_array('social_title',$guest_field_required) && (!isset($customer_guest['id_gender']) || !$customer_guest['id_gender']))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_id_gender',
                            'error' => $this->module->l('Social title is required','order')
                        );
                    }
                    if(in_array('birthday',$guest_field) && in_array('birthday',$guest_field_required) && (!isset($customer_guest['birthday']) || !$customer_guest['birthday']))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_birthday',
                            'error' => $this->module->l('Birthday is required','order')
                        );
                    }
                    if(in_array('password',$guest_field) && in_array('password',$guest_field_required) && (!isset($customer_guest['password']) || !$customer_guest['password']))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_guest_password',
                            'error' => $this->module->l('Password is required','order')
                        );
                    }
                }
            }
            if($type_checkout_options=='create')
            {
                $create_field = Configuration::get('ETS_OPC_CREATEACC_DISPLAY_FIELD') ? explode(',',Configuration::get('ETS_OPC_CREATEACC_DISPLAY_FIELD')):array();
                $create_field_required = Configuration::get('ETS_OPC_CREATEACC_DISPLAY_FIELD_REQUIRED') ? explode(',',Configuration::get('ETS_OPC_CREATEACC_DISPLAY_FIELD_REQUIRED')):array();
                $customer_create = Tools::getValue('customer_create');
                if(!is_array($customer_create) || !Ets_onepagecheckout::validateArray($customer_create))
                    $this->errors[] = $this->module->l('Customer data is not valid','order');
                else
                {
                    if(!isset($customer_create['firstname']) || !$customer_create['firstname'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_firstname',
                            'error' => $this->module->l('First name is required','order')
                        );
                    }
                    if(!isset($customer_create['lastname']) || !$customer_create['lastname'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_lastname',
                            'error' => $this->module->l('Last name is required','order')
                        );
                    }
                    if(!isset($customer_create['email']) || !$customer_create['email'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_email',
                            'error' => $this->module->l('Email is required','order')
                        );
                    }
                    if(!isset($customer_create['password']) || !$customer_create['password'])
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_password',
                            'error' => $this->module->l('Password is required','order')
                        );
                    }
                    if(Configuration::get('PSGDPR_CREATION_FORM_SWITCH') && in_array('psgdpr',$create_field_required) && Module::isEnabled('psgdpr') &&  (!isset($customer_create['psgdpr']) || (isset($customer_create['psgdpr']) && !$customer_create['psgdpr'])))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_psgdpr_check',
                            'error' => $this->module->l('I agree to the terms and conditions and the privacy policy is required','order')
                        );
                    }
                    if(Configuration::get('PS_CUSTOMER_OPTIN') && in_array('optin',$create_field_required) && (!isset($customer_create['optin']) || (isset($customer_create['optin']) && !$customer_create['optin'])) )
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_optin_check',
                            'error' => $this->module->l('Receive offers from our partners is required','order')
                        );
                    }
                    if(in_array('newsletter',$create_field_required) && Module::isEnabled('ps_emailsubscription') &&  (!isset($customer_create['newsletter']) || (isset($customer_create['newsletter']) && !$customer_create['newsletter'])))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_newsletter_check',
                            'error' => $this->module->l('Sign up for our newsletter is required','order')
                        );
                    }
                    if(in_array('customer_privacy',$create_field_required) && Module::isEnabled('ps_dataprivacy') &&  (!isset($customer_create['customer_privacy']) || (isset($customer_create['customer_privacy']) && !$customer_create['customer_privacy'])))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_customer_privacy_check',
                            'error' => $this->module->l('Customer data privacy is required','order')
                        );
                    }
                    if(in_array('social_title',$create_field) && in_array('social_title',$create_field_required) && (!isset($customer_create['id_gender']) || !$customer_create['id_gender']))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_id_gender',
                            'error' => $this->module->l('Social title is required','order')
                        );
                    }
                    if(in_array('birthday',$create_field) && in_array('birthday',$create_field_required) && (!isset($customer_create['birthday']) || !$customer_create['birthday']))
                    {
                        $field_errors[] = array(
                            'field' => 'customer_create_birthday',
                            'error' => $this->module->l('Birthday is required','order')
                        );
                    }
                }
            }
        }
        $shipping_address = Tools::getValue('shipping_address',array());
        $address_field = Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD') ? explode(',',Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD')):array();
        $address_field_required = Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED') ? explode(',',Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED')):array();
        if(!is_array($shipping_address) || !Ets_onepagecheckout::validateArray($shipping_address))
            $this->errors[] = $this->module->l('Shipping address data is not valid','order');
        else
        {
            if(in_array('address2',$address_field) && in_array('address2',$address_field_required) && (!isset($shipping_address['address2'])|| !$shipping_address['address2']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_address2',
                    'error' => $this->module->l('Address complement is required','order')
                );
            }
            if(in_array('company',$address_field) && in_array('company',$address_field_required) && (!isset($shipping_address['company'])|| !$shipping_address['company']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_company',
                    'error' => $this->module->l('Company is required','order')
                );
            }
            if(in_array('vat_number',$address_field) && in_array('vat_number',$address_field_required) && (!isset($shipping_address['vat_number'])|| !$shipping_address['vat_number']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_vat_number',
                    'error' => $this->module->l('VAT number is required','order')
                );
            }
            if(in_array('alias',$address_field) && in_array('alias',$address_field_required) && (!isset($shipping_address['alias'])|| !$shipping_address['alias']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_alias',
                    'error' => $this->module->l('Alias is required','order')
                );
            }
            if(in_array('phone',$address_field) && in_array('phone',$address_field_required) && (!isset($shipping_address['phone'])|| !$shipping_address['phone']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_phone',
                    'error' => $this->module->l('Phone is required','order')
                );
            }
            if(in_array('phone_mobile',$address_field) && in_array('phone_mobile',$address_field_required) && (!isset($shipping_address['phone_mobile'])|| !$shipping_address['phone_mobile']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_phone_mobile',
                    'error' => $this->module->l('Mobile phone is required','order')
                );
            }
            if(in_array('dni',$address_field) && in_array('dni',$address_field_required) && (!isset($shipping_address['dni'])|| !$shipping_address['dni']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_dni',
                    'error' => $this->module->l('Identification number is required','order')
                );
            }
            if(in_array('other',$address_field) && in_array('other',$address_field_required) && (!isset($shipping_address['other'])|| !$shipping_address['other']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_other',
                    'error' => $this->module->l('Other is required','order')
                );
            }
            if(in_array('firstname',$address_field))
            {
                if(in_array('firstname',$address_field_required) && (!isset($shipping_address['firstname'])|| !$shipping_address['firstname']))
                {
                    $field_errors[] = array(
                        'field' => 'shipping_address_firstname',
                        'error' => $this->module->l('First name is required','order')
                    );
                }
                elseif(isset($shipping_address['firstname']) && $shipping_address['firstname'])
                {

                    if(method_exists(Validate::class, 'isCustomerName')){
                        $ok = Validate::isCustomerName($shipping_address['firstname']);
                    }else{
                        $ok = Validate::isName($shipping_address['firstname']);
                    }
                    if(!$ok){
                        $field_errors[] = array(
                            'field' => 'shipping_address_firstname',
                            'error' => $this->module->l('First name is not valid','order')
                        );
                    }
                }    
            }
            if(in_array('lastname',$address_field))
            {
                if(in_array('lastname',$address_field_required) && (!isset($shipping_address['lastname'])|| !$shipping_address['lastname']))
                {
                    $field_errors[] = array(
                        'field' => 'shipping_address_lastname',
                        'error' => $this->module->l('Last name is required','order')
                    );
                }
                elseif(isset($shipping_address['lastname']) && $shipping_address['lastname'])
                {
                    if(method_exists(Validate::class, 'isCustomerName')){
                        $ok = Validate::isCustomerName($shipping_address['lastname']);
                    }else{
                        $ok = Validate::isName($shipping_address['lastname']);
                    }
                    if(!$ok) {
                        $field_errors[] = array(
                            'field' => 'shipping_address_lastname',
                            'error' => $this->module->l('Last name is not valid', 'order')
                        );
                    }
                }    
            }
            if(in_array('address',$address_field) && in_array('address',$address_field_required) && (!isset($shipping_address['address1'])|| !$shipping_address['address1']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_address1',
                    'error' => $this->module->l('Address is required','order')
                );
            }
            if(in_array('city',$address_field) && in_array('city',$address_field_required) && (!isset($shipping_address['city'])|| !$shipping_address['city']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_city',
                    'error' => $this->module->l('City is required','order')
                );
            }
            if(in_array('post_code',$address_field) && in_array('post_code',$address_field_required) && (!isset($shipping_address['postcode'])|| !$shipping_address['postcode']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_postal_code',
                    'error' => $this->module->l('Zip Code is required','order')
                );
            }
            if(in_array('country',$address_field) && in_array('country',$address_field_required) && (!isset($shipping_address['id_country'])|| !$shipping_address['id_country']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_id_country',
                    'error' => $this->module->l('Country is required','order')
                );
            }
            if(in_array('state',$address_field) && in_array('state',$address_field_required) && (isset($shipping_address['id_state']) && !$shipping_address['id_state']))
            {
                $field_errors[] = array(
                    'field' => 'shipping_address_id_state',
                    'error' => $this->module->l('State is required','order')
                );
            }
        }
        $use_another_address_for_invoice = (int)Tools::getValue('use_another_address_for_invoice');
        if($use_another_address_for_invoice)
        {
            
            $invoice_address = Tools::getValue('invoice_address',array());
            if(!is_array($invoice_address) || !Ets_onepagecheckout::validateArray($invoice_address))
                $this->errors[] = $this->module->l('Invoice address data is not valid','order');
            else
            {
                if(in_array('address2',$address_field) && in_array('address2',$address_field_required) && (!isset($invoice_address['address2'])|| !$invoice_address['address2']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_address2',
                        'error' => $this->module->l('Address complement is required','order')
                    );
                }
                if(in_array('company',$address_field) && in_array('company',$address_field_required) && (!isset($invoice_address['company'])|| !$invoice_address['company']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_company',
                        'error' => $this->module->l('Company is required','order')
                    );
                }
                if(in_array('vat_number',$address_field) && in_array('vat_number',$address_field_required) && (!isset($invoice_address['vat_number'])|| !$invoice_address['vat_number']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_vat_number',
                        'error' => $this->module->l('VAT number is required','order')
                    );
                }
                if(in_array('alias',$address_field) && in_array('alias',$address_field_required) && (!isset($invoice_address['alias'])|| !$invoice_address['alias']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_alias',
                        'error' => $this->module->l('Alias is required','order')
                    );
                }
                if(in_array('phone',$address_field) && in_array('phone',$address_field_required) && (!isset($invoice_address['phone'])|| !$invoice_address['phone']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_phone',
                        'error' => $this->module->l('Phone is required','order')
                    );
                }
                if(in_array('phone_mobile',$address_field) && in_array('phone_mobile',$address_field_required) && (!isset($invoice_address['phone_mobile'])|| !$invoice_address['phone_mobile']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_phone_mobile',
                        'error' => $this->module->l('Mobile phone is required','order')
                    );
                }
                if(in_array('dni',$address_field) && in_array('dni',$address_field_required) && (!isset($invoice_address['dni'])|| !$invoice_address['dni']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_dni',
                        'error' => $this->module->l('Identification number is required','order')
                    );
                }
                if(in_array('other',$address_field) && in_array('other',$address_field_required) && (!isset($invoice_address['other'])|| !$invoice_address['other']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_other',
                        'error' => $this->module->l('Other is required','order')
                    );
                }
                if(in_array('firstname',$address_field))
                {
                    if(in_array('firstname',$address_field_required) && (!isset($invoice_address['firstname'])|| !$invoice_address['firstname']))
                    {
                        $field_errors[] = array(
                            'field' => 'invoice_address_firstname',
                            'error' => $this->module->l('First name is required','order')
                        );
                    }
                    elseif(isset($invoice_address['firstname']) && $invoice_address['firstname'])
                    {
                        if(method_exists(Validate::class, 'isCustomerName')){
                            $ok = Validate::isCustomerName($invoice_address['firstname']);
                        }else{
                            $ok = Validate::isName($invoice_address['firstname']);
                        }
                        if(!$ok)
                        {
                            $field_errors[] = array(
                                'field' => 'invoice_address_firstname',
                                'error' => $this->module->l('First name is not valid','order')
                            );
                        }

                    }
                    
                }
                if(in_array('lastname',$address_field))
                {
                    if(in_array('lastname',$address_field_required) && (!isset($invoice_address['lastname'])|| !$invoice_address['lastname']))
                    {
                        $field_errors[] = array(
                            'field' => 'invoice_address_lastname',
                            'error' => $this->module->l('Last name is required','order')
                        );
                    }
                    elseif(isset($invoice_address['lastname']) && $invoice_address['lastname'])
                    {
                        if(method_exists(Validate::class, 'isCustomerName')){
                            $ok = Validate::isCustomerName($invoice_address['lastname']);
                        }else{
                            $ok = Validate::isName($invoice_address['lastname']);
                        }
                        if(!$ok) {
                            $field_errors[] = array(
                                'field' => 'invoice_address_lastname',
                                'error' => $this->module->l('Last name is not valid', 'order')
                            );
                        }
                    }
                    
                }
                if(in_array('address',$address_field) && in_array('address',$address_field_required) && (!isset($invoice_address['address1'])|| !$invoice_address['address1']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_address1',
                        'error' => $this->module->l('Address is required','order')
                    );
                }
                if(in_array('city',$address_field) && in_array('city',$address_field_required) && (!isset($invoice_address['city'])|| !$invoice_address['city']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_city',
                        'error' => $this->module->l('City is required','order')
                    );
                }
                if(in_array('post_code',$address_field) && in_array('post_code',$address_field_required) && (!isset($invoice_address['postcode'])|| !$invoice_address['postcode']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_postal_code',
                        'error' => $this->module->l('Zip code is required','order')
                    );
                }
                if(in_array('country',$address_field) && in_array('country',$address_field_required) && (!isset($invoice_address['id_country'])|| !$invoice_address['id_country']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_id_country',
                        'error' => $this->module->l('Country is required','order')
                    );
                }
                if(in_array('state',$address_field) && in_array('state',$address_field_required) && (isset($invoice_address['id_state']) && !$invoice_address['id_state']))
                {
                    $field_errors[] = array(
                        'field' => 'invoice_address_id_state',
                        'error' => $this->module->l('State is required','order')
                    );
                }
            }
        }
        $post_values = Tools::getValue('additional_info',array());
        $file_values = isset($_FILES['additional_info']) ? $_FILES['additional_info']:array();
        $additional_fields = Ets_opc_additionalinfo_field::getListField($this->context->language->id,true);
        if($additional_fields && is_array($post_values) && Ets_onepagecheckout::validateArray($post_values) && is_array($file_values) && Ets_onepagecheckout::validateArray($file_values))
        {   
            foreach($additional_fields as $field)
            {
                if($field['required'])
                {
                    if((!isset($post_values[$field['id']]) || (isset($post_values[$field['id']]) && !$post_values[$field['id']])) && $field['type']!='file')
                    {
                        $field_errors[] = array(
                            'field' => 'additional_info_'.$field['id'],
                            'error' => sprintf($this->module->l('%s is required','order'),$field['title'])
                        );
                    }
                    if($field['type']=='file' && (!isset($file_values['name'][$field['id']]) || (isset($file_values['name'][$field['id']]) &&!$file_values['name'][$field['id']])))
                    {
                        $field_errors[] = array(
                            'field' => 'additional_info_'.$field['id'],
                            'error' => sprintf($this->module->l('%s is required','order'),$field['title'])
                        );
                    }
                }
            }
        }
        if(Module::isEnabled('ets_shoplicense') && ($product_shop = Tools::getValue('product_shop')) && Ets_onepagecheckout::validateArray($product_shop))
        {
            foreach($product_shop as $id_product=> $shops)
            {
                foreach($shops as $key=> $shop)
                {
                    if(!trim($shop))
                    {
                        $field_errors[] = array(
                            'field' => 'product_shop_'.$id_product.'_'.$key,
                            'error' => $this->l('Shop to install is required'),
                        );
                    }
                    elseif(!Ets_onepagecheckout::isDomain(trim($shop)))
                    {
                        $field_errors[] = array(
                            'field' => 'product_shop_'.$id_product.'_'.$key,
                            'error' => $this->l('Shop to install is not a valid domain or URL'),
                        );
                    }
                }
            }
        }
        if($field_errors)
        {
            die(
                json_encode(
                    array(
                        'hasError' => true,
                        'field_errors' => $field_errors,
                        'errors' => $this->module->displayError($this->module->l('Please fill in all required fields with valid information','order')),
                    )
                )
            );
        }
        else
        {
            if($type_checkout_options=='guest')
            {
                $customer_guest = Tools::getValue('customer_guest',array());
                if(!is_array($customer_guest) || !Ets_onepagecheckout::validateArray($customer_guest))
                    $this->errors[] = $this->module->l('Guest customer data is not valid','order');
                else
                {
                    if (isset($customer_guest['email']) && $customer_guest['email'] && Customer::customerExists($customer_guest['email'])) {
                        $field_errors[] = array(
                            'field'=> 'customer_guest_email',
                            'error' => $this->module->l('An account using this email address has already been registered.','order'),                        
                        );
                    }
                    else
                    {
                        if( isset($customer_guest['firstname']) && $customer_guest['firstname'] && !Validate::isName($customer_guest['firstname']))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_guest_firstname',
                                'error' => $this->module->l('First name is invalid','order'),                        
                            );
                        }
                        if(isset($customer_guest['lastname']) && $customer_guest['lastname'] && !Validate::isName($customer_guest['lastname']))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_guest_lastname',
                                'error' => $this->module->l('Last name is invalid','order'),                        
                            );
                        }
                        if(isset($customer_guest['email']) && $customer_guest['email'] && !Validate::isEmail($customer_guest['email']))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_guest_email',
                                'error' => $this->module->l('Email is invalid','order'),                        
                            );
                        }
                        if(isset($customer_guest['password']) && $customer_guest['password'])
                        {
                            if(method_exists('Validate','isPasswd') && !Validate::isPasswd($customer_guest['password']))
                            {
                                $field_errors[] = array(
                                    'field'=> 'customer_guest_password',
                                    'error' => $this->module->l('Email is invalid','order'),
                                );
                            }
                            elseif($error = $this->checkPasswd($customer_guest['password']))
                            {
                                $field_errors[] = array(
                                    'field'=> 'customer_guest_password',
                                    'error' => $error,
                                );
                            }

                        } 
                        if(isset($customer_guest['birthday']) && $customer_guest['birthday'] && !Validate::isBirthDate($customer_guest['birthday'],$this->context->language->date_format_lite))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_guest_birthday',
                                'error' => $this->module->l('Birthday is invalid','order'),                        
                            );
                        }
                    }
                }
            }
            if($type_checkout_options=='create')
            {
                $customer_create = Tools::getValue('customer_create',array());
                if(!is_array($customer_create) || !Ets_onepagecheckout::validateArray($customer_create))
                    $this->errors[] = $this->module->l('Customer data is not valid','order');
                else
                {
                    if (isset($customer_create['email']) && $customer_create['email'] && Customer::customerExists($customer_create['email'])) {
                        $field_errors[] = array(
                            'field'=> 'customer_create_email',
                            'error' => $this->module->l('An account using this email address has already been registered','order'),                        
                        );
                    }
                    else
                    {
                        if(isset($customer_create['firstname']) && $customer_create['firstname'] && !Validate::isName($customer_create['firstname']))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_create_firstname',
                                'error' => $this->module->l('First name is invalid','order'),                        
                            );
                        }
                        if(isset($customer_create['lastname']) &&  $customer_create['lastname'] && !Validate::isName($customer_create['lastname']))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_create_lastname',
                                'error' => $this->module->l('Last name is invalid','order'),                        
                            );
                        }
                        if(isset($customer_create['email']) && $customer_create['email'] && !Validate::isEmail($customer_create['email']))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_create_email',
                                'error' => $this->module->l('Email is invalid','order'),                        
                            );
                        }
                        if(isset($customer_create['birthday']) && $customer_create['birthday'] && !Validate::isBirthDate($customer_create['birthday'],$this->context->language->date_format_lite))
                        {
                            $field_errors[] = array(
                                'field'=> 'customer_create_birthday',
                                'error' => $this->module->l('Birthday is invalid','order'),                        
                            );
                        }
                        if(isset($customer_create['password']) && $customer_create['password'])
                        {
                            if(method_exists('Validate','isPasswd') &&   !Validate::isPasswd($customer_create['password']))
                            {
                                $field_errors[] = array(
                                    'field'=> 'customer_create_password',
                                    'error' => $this->module->l('Password is invalid','order'),
                                );
                            }
                            elseif($error = $this->checkPasswd($customer_create['password']))
                            {
                                $field_errors[] = array(
                                    'field'=> 'customer_create_password',
                                    'error' => $error,
                                );
                            }

                        }
                    }
                }
            }
            if(isset($shipping_address['alias']) && $shipping_address['alias'] && !Validate::isGenericName($shipping_address['alias']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_alias',
                    'error' => $this->module->l('Alias is invalid','order'),                        
                );
            }
            if(isset($shipping_address['firstname']) && $shipping_address['firstname'] && !Validate::isName($shipping_address['firstname']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_firstname',
                    'error' => $this->module->l('First name is invalid','order'),                        
                );
            }
            if( isset($shipping_address['lastname']) && $shipping_address['lastname'] && !Validate::isName($shipping_address['lastname']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_lastname',
                    'error' => $this->module->l('Last name is invalid','order'),                        
                );
            }
            if(isset($shipping_address['company']) && $shipping_address['company'] && !Validate::isGenericName($shipping_address['company']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_company',
                    'error' => $this->module->l('Company is invalid','order'),                        
                );
            }
            if(isset($shipping_address['address1']) && $shipping_address['address1'] && !Validate::isAddress($shipping_address['address1']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_address1',
                    'error' => $this->module->l('Address is invalid','order'),                        
                );
            }
            if(isset($shipping_address['address2']) && $shipping_address['address2'] && !Validate::isAddress($shipping_address['address2']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_address2',
                    'error' => $this->module->l('Address complement is invalid','order'),
                );
            }
            if(isset($shipping_address['city']) && $shipping_address['city'] && !Validate::isCityName($shipping_address['city']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_city',
                    'error' => $this->module->l('City is invalid','order'),                        
                );
            }
            if(isset($shipping_address['phone']) && $shipping_address['phone'] && !Validate::isPhoneNumber($shipping_address['phone']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_phone',
                    'error' => $this->module->l('Phone is invalid','order'),                        
                );
            }
            if(isset($shipping_address['phone_mobile']) && $shipping_address['phone_mobile'] && !Validate::isPhoneNumber($shipping_address['phone_mobile']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_phone_mobile',
                    'error' => $this->module->l('Mobile phone is invalid','order'),                        
                );
            }
            if(isset($shipping_address['dni']) && $shipping_address['dni'] && !Validate::isDniLite($shipping_address['dni']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_dni',
                    'error' => $this->module->l('Identification number is invalid','order'),                        
                );
            }
            if(isset($shipping_address['other']) && $shipping_address['other'] && !Validate::isMessage($shipping_address['other']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_other',
                    'error' => $this->module->l('Other is invalid','order'),                        
                );
            }
            // Compatibility with Advanced VAT Manager Module
            if (Module::isEnabled('advancedvatmanager') && Configuration::get('ADVANCEDVATMANAGER_FRONTVALIDATION')== 1) {
                if(isset($shipping_address['vat_number']) && $shipping_address['vat_number']) {
                    include_once _PS_MODULE_DIR_.'advancedvatmanager/classes/ValidationEngine.php';
                    $advancedvatmanager = new ValidationEngine($shipping_address['vat_number']);
                    $verifications = $advancedvatmanager->VATValidationProcess($shipping_address['id_country'], $shipping_address['id_customer'], $shipping_address['id_address'], $shipping_address['company']);
                    if (!$verifications) {
                        $field_errors[] = array(
                            'field'=> 'shipping_address_vat_number',
                            'error' => $advancedvatmanager->getMessage(),                        
                        );
                    }
                }
            }
            elseif(isset($shipping_address['vat_number']) && $shipping_address['vat_number'] && !Validate::isGenericName($shipping_address['vat_number']))
            {
                $field_errors[] = array(
                    'field'=> 'shipping_address_vat_number',
                    'error' => $this->module->l('VAT number is invalid','order'),                        
                );
            }
            if(isset($shipping_address['id_country']) && $shipping_address['id_country'])
            {
                $country = new Country($shipping_address['id_country']);
                $postcode = isset($shipping_address['postcode']) ? $shipping_address['postcode'] :'';
                if ($postcode && $country->zip_code_format && !$country->checkZipCode($postcode)) {
                    $field_errors[] = array(
                        'field'=> 'shipping_address_postal_code',
                        'error' => $this->module->l('The Zip/Postal code  you\'ve entered is invalid. It must follow this format:','order').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))),                        
                    );
                } elseif ($postcode && !Validate::isPostCode($postcode)) {
                    $field_errors[] = array(
                        'field'=> 'shipping_address_postal_code',
                        'error' => $this->module->l('The Zip/Postal code is invalid.','order').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))),                        
                    );
                }
            }
            if($use_another_address_for_invoice)
            {
                if(isset($invoice_address['alias']) && $invoice_address['alias'] && !Validate::isGenericName($invoice_address['alias']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_alias',
                        'error' => $this->module->l('Alias is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['firstname']) && $invoice_address['firstname'] && !Validate::isName($invoice_address['firstname']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_firstname',
                        'error' => $this->module->l('First name is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['lastname']) && $invoice_address['lastname'] && !Validate::isName($invoice_address['lastname']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_lastname',
                        'error' => $this->module->l('Last name is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['company']) && $invoice_address['company'] && !Validate::isGenericName($invoice_address['company']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_company',
                        'error' => $this->module->l('Company is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['address1']) && $invoice_address['address1'] && !Validate::isAddress($invoice_address['address1']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_address1',
                        'error' => $this->module->l('Address is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['address2']) && $invoice_address['address2'] && !Validate::isAddress($invoice_address['address2']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_address2',
                        'error' => $this->module->l('Address complement is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['city']) && $invoice_address['city'] && !Validate::isCityName($invoice_address['city']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_city',
                        'error' => $this->module->l('City is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['phone']) && $invoice_address['phone'] && !Validate::isPhoneNumber($invoice_address['phone']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_phone',
                        'error' => $this->module->l('Phone is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['phone_mobile']) && $invoice_address['phone_mobile'] && !Validate::isPhoneNumber($invoice_address['phone_mobile']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_phone_mobile',
                        'error' => $this->module->l('Mobile phone is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['dni']) && $invoice_address['dni'] && !Validate::isDniLite($invoice_address['dni']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_dni',
                        'error' => $this->module->l('Identification number is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['other']) && $invoice_address['other'] && !Validate::isDniLite($invoice_address['other']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_other',
                        'error' => $this->module->l('Other is invalid','order'),                        
                    );
                }
                if (Module::isEnabled('advancedvatmanager') && Configuration::get('ADVANCEDVATMANAGER_FRONTVALIDATION')== 1) {
                    if(isset($invoice_address['vat_number']) && $invoice_address['vat_number']) {
                        include_once _PS_MODULE_DIR_.'advancedvatmanager/classes/ValidationEngine.php';
                        $advancedvatmanager = new ValidationEngine($invoice_address['vat_number']);
                        $verifications = $advancedvatmanager->VATValidationProcess($invoice_address['id_country'], $invoice_address['id_customer'], $invoice_address['id_address'], $invoice_address['company']);
                        if (!$verifications) {
                            $field_errors[] = array(
                                'field'=> 'invoice_address_vat_number',
                                'error' => $advancedvatmanager->getMessage(),                        
                            );
                        }
                    }
                }
                elseif(isset($invoice_address['vat_number']) && $invoice_address['vat_number'] && !Validate::isGenericName($invoice_address['vat_number']))
                {
                    $field_errors[] = array(
                        'field'=> 'invoice_address_vat_number',
                        'error' => $this->module->l('VAT number is invalid','order'),                        
                    );
                }
                if(isset($invoice_address['id_country']) && $invoice_address['id_country'])
                {
                    $country = new Country($invoice_address['id_country']);
                    $postcode = isset($invoice_address['postcode']) ? $invoice_address['postcode'] :'';
                    if ($postcode && $country->zip_code_format && !$country->checkZipCode($postcode)) {
                        $field_errors[] = array(
                            'field'=> 'invoice_address_postal_code',
                            'error' => $this->module->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format:','order').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))),                        
                        );
                    } elseif ($postcode && !Validate::isPostCode($postcode)) {
                        $field_errors =array(
                            'field'=> 'invoice_address_postal_code',
                            'error' => $this->module->l('The Zip/Postal code is invalid.','order'),
                        );
                    }
                } 
            }
            if($additional_fields)
            {
                foreach($additional_fields as $field)
                {
                    if($field['type']!='file' && isset($post_values[$field['id']]) && $post_values[$field['id']])
                    {
                        if($field['type']=='text' || $field['type']=='textarea')
                        {
                            if(!Validate::isCleanHtml($post_values[$field['id']]))
                            {
                                $field_errors[] = array(
                                    'field' => 'additional_info_'.$field['id'],
                                    'error' => sprintf($this->module->l('%s is not valid','order'),$field['title'])
                                );
                            }
                        }
                        if(($field['type']=='radio' || $field['type']=='select' || $field['type']=='checkbox') && $field['options'])
                        {
                            $values = array();
                            foreach(explode("\n",$field['options']) as $options)
                            {
                                $options = explode('|',$options);
                                if(isset($options[1]) && $options[1])
                                {
                                    $val = $options[1];
                                }
                                else
                                    $val = $options[0];
                                $val_texts = explode(':',$val);
                                $values[] = $val_texts[0];
                            }
                            if(($field['type']=='radio' || $field['type']=='select') && !in_array(trim($post_values[$field['id']]),array_map('trim',$values)))
                            {
                                $field_errors[] = array(
                                    'field' => 'additional_info_'.$field['id'],
                                    'error' => sprintf($this->module->l('%s is not valid','order'),$field['title'])
                                );
                            }
                            if($field['type']=='checkbox')
                            {
                                foreach($post_values[$field['id']] as $post_value)
                                {
                                    if(!in_array(trim($post_value),array_map('trim',$values)))
                                    {
                                        $field_errors[] = array(
                                            'field' => 'additional_info_'.$field['id'],
                                            'error' => sprintf($this->module->l('%s is not valid','order'),$field['title'])
                                        );
                                        break;
                                    }    
                                }
                            }
                        }
                        if(($field['type']=='date_time' || $field['type']=='date') && !Validate::isDateFormat($post_values[$field['id']]))
                        {
                            $field_errors[] = array(
                                'field' => 'additional_info_'.$field['id'],
                                'error' => sprintf($this->module->l('%s is not valid','order'),$field['title'])
                            );
                        }
                        if($field['type']=='number' && !Validate::isFloat($post_values[$field['id']]))
                        {
                            $field_errors[] = array(
                                'field' => 'additional_info_'.$field['id'],
                                'error' => sprintf($this->module->l('%s is not valid','order'),$field['title'])
                            );
                        }
                    }
                    if($field['type']=='file' && isset($file_values['name'][$field['id']]) && ($file_name = $file_values['name'][$field['id']]))
                    {
                        $file_errors = array();
                        $this->module->validateFile($file_name,$file_values['size'][$field['id']],$file_errors);
                        if($file_errors)
                        {
                            foreach($file_errors as $error)
                            {
                                $field_errors[] = array(
                                    'field' => 'additional_info_'.$field['id'],
                                    'error' => $error
                                );
                            }
                        }
                    }
                }
            }
        }
        if($field_errors)
        {
            die(
                json_encode(
                    array(
                        'hasError' => true,
                        'field_errors' => $field_errors,
                        'errors' => $this->module->displayError($this->module->l('Please fill in all required fields with valid information','order')),
                    )
                )
            );
        }
        elseif($this->errors)
        {
            die(
                json_encode(
                    array(
                        'hasError' => true,
                        'errors' => $this->module->displayError($this->errors),
                    )
                )
            );
        }
        else{
            $delivery_option = Tools::getValue('delivery_option');
            $payment_option = Tools::getValue('payment-option');
            if((!$delivery_option || !is_array($delivery_option) || !Ets_onepagecheckout::validateArray($delivery_option)) && !$this->context->cart->isVirtualCart())
            {
                $this->errors[] = $this->module->l('No shipping method has been selected.','order');
            }
            elseif((!$payment_option || !Validate::isModuleName($payment_option)) && $this->context->cart->getOrderTotal())
            {
                $this->errors[] = $this->module->l('No payment method has been selected.','order');
            }
            if (Module::isEnabled('ets_delivery')) {
                /* @var Ets_delivery $delivery */
                $delivery = Module::getInstanceByName('ets_delivery');
                if(method_exists($delivery,'checkoutProcess'))
                    $delivery->checkoutProcess(true);
            }
        } 
        $conditions_to_approve = Tools::getValue('conditions_to_approve');
        if(Configuration::get('PS_CONDITIONS') && ( !is_array($conditions_to_approve) || !Ets_onepagecheckout::validateArray($conditions_to_approve,'isInt') || !isset($conditions_to_approve['terms-and-conditions']) || !$conditions_to_approve['terms-and-conditions']))
        {
            $this->errors[] = $this->module->l('You must accept our terms of service in order to complete your order.','order');
        }
        $isAvailable = $this->areProductsAvailable();
        if($isAvailable!==true)
            $this->errors[] = $isAvailable;
        $delivery_message = Tools::getValue('delivery_message');
        if($delivery_message && (!Validate::isCleanHtml($delivery_message) || Tools::strlen($delivery_message)>1600))
        {
            $this->errors[] = $this->module->l('Message is invalid.','order');
        }
        if(Configuration::get('PS_GIFT_WRAPPING'))
        {
            $gift_message = Tools::getValue('gift_message');
            $gift = (int)Tools::getValue('gift');
            if($gift && $gift_message && !Validate::isMessage($gift_message)) 
            {
                $this->errors[] = $this->module->l('Gift message is invalid.','order');
            }
        }
        if($additional_fields && !$this->errors)
        {
            foreach($additional_fields as $field) 
            {
                if($field['type']!='file')
                {
                    if($id_ets_opc_field_value = Ets_opc_additionalinfo_field_value::getIdByField($field['id'],$this->context->cart->id))
                    {
                        $fieldValue = new Ets_opc_additionalinfo_field_value($id_ets_opc_field_value);
                    }
                    else
                    {
                        $fieldValue = new Ets_opc_additionalinfo_field_value();
                        $fieldValue->id_ets_opc_additionalinfo_field = $field['id'];
                        $fieldValue->id_cart = $this->context->cart->id;
                    }
                    $fieldValue->value = isset($post_values[$field['id']]) ? (is_array($post_values[$field['id']]) ? (Ets_onepagecheckout::validateArray($post_values[$field['id']]) ? implode(',',$post_values[$field['id']]):'' ) :$post_values[$field['id']]) :'';
                    if($fieldValue->id)
                    {
                        if(!$fieldValue->update())
                            $this->errors[] = sprintf($this->module->l('Saving %s failed','order'),$field['title']);
                    }
                    elseif(!$fieldValue->add())
                        $this->errors[] = sprintf($this->module->l('Saving %s failed','order'),$field['title']);
                }
                else
                {
                    $file_value = $this->module->uploadFile($file_values, $field['id'],$this->errors);
                    if($id_ets_opc_field_value = Ets_opc_additionalinfo_field_value::getIdByField($field['id'],$this->context->cart->id))
                    {
                        $fieldValue = new Ets_opc_additionalinfo_field_value($id_ets_opc_field_value);
                    }
                    else
                    {
                        $fieldValue = new Ets_opc_additionalinfo_field_value();
                        $fieldValue->id_ets_opc_additionalinfo_field = $field['id'];
                        $fieldValue->id_cart = $this->context->cart->id;
                    }
                    $file_old = $fieldValue->value;
                    $fieldValue->value = $file_value ? :'';
                    $fieldValue->file_name = isset($file_values['name'][$field['id']]) ? $file_values['name'][$field['id']]: '';
                    if($fieldValue->id)
                    {
                        if(!$fieldValue->update())
                        {
                            if($fieldValue->value && file_exists(_PS_ETS_OPC_UPLOAD_DIR_.$fieldValue->value))
                                @unlink(_PS_ETS_OPC_UPLOAD_DIR_.$fieldValue->value);
                            $this->errors[] = sprintf($this->module->l('Saving %s failed','order'),$field['title']);
                        }elseif($file_old  && file_exists(_PS_ETS_OPC_UPLOAD_DIR_.$file_old))
                            @unlink(_PS_ETS_OPC_UPLOAD_DIR_.$file_old);
                    }
                    elseif(!$fieldValue->add())
                    {
                        if($fieldValue->value && file_exists(_PS_ETS_OPC_UPLOAD_DIR_.$fieldValue->value))
                            @unlink(_PS_ETS_OPC_UPLOAD_DIR_.$fieldValue->value);
                        $this->errors[] = sprintf($this->module->l('Saving %s failed','order'),$field['title']);
                    }
                }
            }
            if($this->errors)
            {
                if($field_values = Ets_opc_additionalinfo_field_value::getFieldValuesByIDCart($this->context->cart->id))
                {
                    foreach($field_values as $field_value)
                    {
                        $fieldValue = new Ets_opc_additionalinfo_field_value($field_value['id_ets_opc_additionalinfo_field_value']);
                        $fieldValue->delete();
                    }
                }
            }
        }
        if(!$field_errors && !$this->errors && (($type_checkout_options=='create' && Configuration::get('ETS_OPC_CREATEACC_CAPTCHA_ENABLED')) || $type_checkout_options=='guest' && Configuration::get('ETS_OPC_GUEST_CAPTCHA_ENABLED')))
        {
            $this->checkCaptcha($field_errors);
        }
        if(!$this->errors)
        {
            if($type_checkout_options=='guest')
            {
                $customer_guest = Tools::getValue('customer_guest');
                if($customer_guest && is_array($customer_guest) && Ets_onepagecheckout::validateArray($customer_guest))
                {
                    $customer = new Customer();
                    $customer->firstname = $customer_guest['firstname'];
                    $customer->lastname = $customer_guest['lastname'];
                    $customer->email = $customer_guest['email'];
                    $customer->passwd = isset($customer_guest['password']) && $customer_guest['password'] ? md5(_COOKIE_KEY_.$customer_guest['password']) :   md5(time()._COOKIE_KEY_);
                    $birthday = isset($customer_guest['birthday']) ? $customer_guest['birthday']:'';
                    if($birthday)
                    {
                        $customer->birthday = $this->convertDateBirthday($birthday);
                    }
                    $customer->is_guest = isset($customer_guest['password']) && $customer_guest['password'] ? 0:1;
                    $customer->id_gender = isset($customer_guest['id_gender']) ? $customer_guest['id_gender']:0;
                    if(isset($customer_guest['newsletter']) && $customer_guest['newsletter'])
                    {
                        $customer->newsletter=1;
                        $customer->newsletter_date_add = date('Y-m-d H:i:s');
                    }
                    if(isset($customer_guest['optin']) && $customer_guest['optin'])
                        $customer->optin=1;
                    if($customer->add())
                    {
                        $customer->cleanGroups();
                        if($customer->is_guest)
                            $customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
                        else
                        {
                            $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
                        }
                        if(method_exists($this->context,'updateCustomer'))
                            $this->context->updateCustomer($customer);
                        else
                            $this->updateContext($customer);
                        if(!$customer->is_guest)
                            Hook::exec('actionCustomerAccountAdd',array('newCustomer'=>$customer));
                    }
                    else
                        $this->errors[] = $this->module->l('Creating guest customer failed','order');
                }
                
            }
            if($type_checkout_options=='create')
            {
                $customer_create = Tools::getValue('customer_create');
                if($customer_create && is_array($customer_create) && Ets_onepagecheckout::validateArray($customer_create))
                {
                    $customer = new Customer();
                    $customer->firstname = $customer_create['firstname'];
                    $customer->lastname = $customer_create['lastname'];
                    $customer->email = $customer_create['email'];
                    $customer->passwd = md5(_COOKIE_KEY_.$customer_create['password']);
                    $customer->is_guest = 0;
                    $birthday = isset($customer_create['birthday']) ? $customer_create['birthday']:'';
                    if($birthday)
                    {
                        $customer->birthday = $this->convertDateBirthday($birthday);
                    }
                    $customer->id_gender = isset($customer_create['id_gender']) ? $customer_create['id_gender']:0;
                    if(isset($customer_create['newsletter']) && $customer_create['newsletter'])
                    {
                        $customer->newsletter=1;
                        $customer->newsletter_date_add = date('Y-m-d H:i:s');
                    }
                    if(isset($customer_create['optin']) && $customer_create['optin'])
                        $customer->optin=1;
                    if($customer->add())
                    {
                        $customer->cleanGroups();
                        $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
                        if(method_exists($this->context,'updateCustomer'))
                            $this->context->updateCustomer($customer);
                        else
                            $this->updateContext($customer);
                        Hook::exec('actionCustomerAccountAdd',array('newCustomer'=>$customer));
                    }
                    else
                        $this->errors[] = $this->module->l('Creating guest customer failed','order');
                }
            }
        }
        if(!$this->errors)
        {
            if($shipping_address['id_address'])
                $address = new Address($shipping_address['id_address']);
            else
                $address = new Address();
            $address->alias =isset($shipping_address['alias']) && $shipping_address['alias'] ? $shipping_address['alias'] : $this->module->l('My Address','order');
            if(!$address->id)
                $address->id_customer = $this->context->customer->id;
            if(isset($shipping_address['firstname']) && $shipping_address['firstname'])
                $address->firstname = $shipping_address['firstname'];
            else
                $address->firstname =' ';
            if(isset($shipping_address['lastname']) && $shipping_address['lastname'])
                $address->lastname = $shipping_address['lastname'];
            else
                $address->lastname = ' ';
            if(isset($shipping_address['company']))
                $address->company = $shipping_address['company'];
            if(isset($shipping_address['address2']))
                $address->address2 = $shipping_address['address2'];
            if(isset($shipping_address['address1']) && $shipping_address['address1']) 
                $address->address1 = $shipping_address['address1'];
            else
                $address->address1 =' ';
            if(isset($shipping_address['city']) && $shipping_address['city'])
                $address->city = $shipping_address['city'];
            else
                 $address->city =' ';
            $address->id_state = isset($shipping_address['id_state']) ? $shipping_address['id_state'] :0;
            if(isset($shipping_address['id_country']) && $shipping_address['id_country'])
                $address->id_country = $shipping_address['id_country'];
            elseif(!$address->id_country)
                $address->id_country = (int)$this->context->country->id ? : (int)Configuration::get('PS_COUNTRY_DEFAULT');
            $address->postcode = isset($shipping_address['postcode']) ? $shipping_address['postcode'] :'';
            $address->phone = isset($shipping_address['phone']) ? $shipping_address['phone']:'';
            $address->phone_mobile = isset($shipping_address['phone_mobile']) ? $shipping_address['phone_mobile']:'';
            $address->dni = isset($shipping_address['dni']) ? $shipping_address['dni']:'dni';
            $address->other = isset($shipping_address['other']) ? $shipping_address['other']:'';
            $address->vat_number = isset($shipping_address['vat_number']) ? $shipping_address['vat_number']:'';
            if(Configuration::get('PS_RECYCLABLE_PACK'))
            {
                $recyclable = (int)Tools::getValue('recyclable');
                $this->context->cart->recyclable = $recyclable;
            }
            if(Configuration::get('PS_GIFT_WRAPPING'))
            {
                $gift = (int)Tools::getValue('gift');
                if($gift)
                    $this->context->cart->gift_message = $gift_message;
                $this->context->cart->gift= $gift;
            }
            if($address->id && $this->context->customer->is_guest)
            {
                $address->id_customer = $this->context->customer->id;
                $address->id =0;
            }
            if(isset($address->id) && $address->id)
            {
                if($address->id_customer!=$this->context->customer->id)
                {
                    $this->errors[] = $this->module->l('Shipping address is not valid','order');
                }
                else
                {
                    if($address->update())
                    {
                        $this->context->cart->id_address_delivery = $address->id;
                        if(!$use_another_address_for_invoice)
                            $this->context->cart->id_address_invoice = $address->id;
                        $this->context->cart->update();
                        Ets_opc_db::updateDeliveryAddress($address->id);
                    }
                    else
                        $this->errors[] = $this->module->l('Updating shipping address failed','order');
                }
                
            }
            else
            {
               if($address->add())
               {
                    $this->context->cart->id_address_delivery = $address->id;
                    if(!$use_another_address_for_invoice)
                        $this->context->cart->id_address_invoice = $address->id;
                    $this->context->cart->update();
                    Ets_opc_db::updateDeliveryAddress($address->id);
               }
               else
                    $this->errors[] = $this->module->l('Updating shipping address failed','order'); 
            }
            if($this->context->cart->delivery_option){
                $delivery_option = json_decode($this->context->cart->delivery_option,true);
                $deliveryOptionsFinder = new DeliveryOptionsFinder(
                    $this->context,
                    $this->getTranslator(),
                    $this->objectPresenter,
                    new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter()
                );
                $checkout_session = new CheckoutSession(
                    $this->context,
                    $deliveryOptionsFinder
                );
                if ($delivery_option) {
                    foreach($delivery_option as $id_address => $delivery){
                        if($id_address!= $this->context->cart->id_address_delivery){
                            unset($delivery_option[$id_address]);
                            $delivery_option[$this->context->cart->id_address_delivery] = $delivery;
                        }
                    }
                    $checkout_session->setDeliveryOption(
                        $delivery_option
                    );
                }
                Hook::exec('actionCarrierProcess', array('cart' => $checkout_session->getCart()));
            }
            if($use_another_address_for_invoice)
            {
                if($invoice_address['id_address'])
                    $address = new Address($invoice_address['id_address']);
                else
                    $address = new Address();
                $address->alias = isset($invoice_address['alias']) && $invoice_address['alias'] ? $invoice_address['alias'] : $this->module->l('My Address','order');
                if(!$address->id)
                    $address->id_customer = $this->context->customer->id;
                if(isset($invoice_address['firstname']) && $invoice_address['firstname'])
                    $address->firstname = $invoice_address['firstname'];
                else
                    $address->firstname =' ';
                if(isset($invoice_address['lastname']) && $invoice_address['lastname'])
                    $address->lastname = $invoice_address['lastname'];
                else
                    $address->lastname =' ';
                if(isset($invoice_address['company']))
                    $address->company = $invoice_address['company'];
                if(isset($invoice_address['address2']))
                    $address->address2 = $invoice_address['address2']; 
                if(isset($invoice_address['address1']) && $invoice_address['address1'])
                    $address->address1 = $invoice_address['address1'];
                else
                    $address->address1 =' ';
                if(isset($invoice_address['city']) && $invoice_address['city'])
                    $address->city = $invoice_address['city'];
                else
                    $address->city =' ';
                $address->id_state = isset($invoice_address['id_state']) ? $invoice_address['id_state'] :0;
                if(isset($invoice_address['id_country']) && $invoice_address['id_country'])
                    $address->id_country = $invoice_address['id_country'];
                elseif(!$address->id_country)
                    $address->id_country = $address->id_country = (int)$this->context->country->id ?: (int)Configuration::get('PS_COUNTRY_DEFAULT');
                $address->postcode =  isset($invoice_address['postcode']) ? $invoice_address['postcode'] :'';
                $address->phone = isset($invoice_address['phone']) && $invoice_address['phone'] ? $invoice_address['phone']:'';
                $address->phone_mobile = isset($invoice_address['phone_mobile']) ? $invoice_address['phone_mobile']:'';
                $address->dni = isset($invoice_address['dni']) ? $invoice_address['dni']:'dni';
                $address->other = isset($invoice_address['other']) ? $invoice_address['other']:'';
                $address->vat_number = isset($invoice_address['vat_number']) ? $invoice_address['vat_number']:'';
                if($address->id)
                {
                    if($address->id_customer!=$this->context->customer->id)
                    {
                        $this->errors[] = $this->module->l('Invoice address is not valid','order');
                    }
                    else
                    {
                        if($address->update())
                        {
                            $this->context->cart->id_address_invoice = $address->id;
                            $this->context->cart->update();
                        }
                        else
                            $this->errors[] = $this->module->l('Updating invoice address failed','order');
                    }
                    
                }
                else
                {
                   if($address->add())
                   {
                        $this->context->cart->id_address_invoice = $address->id;
                        $this->context->cart->update();
                   }
                   else
                        $this->errors[] = $this->module->l('Updating invoice address failed','order'); 
                }
            }
            
        }
        if(!$this->errors)
        {
            $message = Message::getMessageByCartId($this->context->cart->id);
            if($delivery_message)
            {
                if($message && isset($message['id_message']) && ($id_message = $message['id_message']))
                {
                    $messageObj = new Message($id_message);
                    $messageObj->message = $delivery_message;
                    $messageObj->update();
                }
                else
                {
                    $messageObj = new Message();
                    $messageObj->message = $delivery_message;
                    $messageObj->id_employee=0;
                    $messageObj->id_customer = $this->context->customer->id;
                    $messageObj->id_cart= $this->context->cart->id;
                    $messageObj->add();
                }
            }
            elseif($message && isset($message['id_message']) && ($id_message = $message['id_message']))
            {
                $messageObj = new Message($id_message);
                $messageObj->delete();
            } 
            if(Module::isInstalled('stripe_official'))
            {
                $stripe_official = Module::getInstanceByName('stripe_official');
                if (method_exists($stripe_official,'hookHeader')) {
                    $stripe_official->hookHeader();
                }
                else
                    $stripe_official->hookDisplayHeader();
                $js_def = Media::getJsDef();
                if(isset($js_def['prestashop']))
                    unset($js_def['prestashop']);
                $this->context->smarty->assign(array(
                    'js_def' => $js_def,
                ));
                $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');  
            }
            hook::exec('saveShippingCartShopLicense');
            $json = array(
                'hasError' => false,
                'java_script' => isset($javascript)?$javascript:'',
                'link_checkout_free' => $this->context->cart->getOrderTotal(true) == 0 ? $this->context->link->getPageLink('order-confirmation',null,null,array('free_order'=>1)):'',
            );
            if(in_array($type_checkout_options, ['create', 'guest']) && !$this->context->customer->is_guest){
                $json = array_merge([
                    'invoice_address' => $this->displayBlockInvoiceAddress($this->context->cart->id_address_delivery),
                    'shipping_address' => $this->displayBlockDeliveryAddress($this->context->cart->id_address_invoice),
                    'customer_block' => $this->displayBlockCustomerInFo(),
                    'jsCustomer' => $this->getTemplateVarCustomer($this->context->customer),
                ], $json);
            }
            die(
                json_encode(
                    $json
                )
            );
        }
        else
        {
            if($field_values = Ets_opc_additionalinfo_field_value::getFieldValuesByIDCart($this->context->cart->id))
            {
                foreach($field_values as $field_value)
                {
                    $fieldValue = new Ets_opc_additionalinfo_field_value($field_value['id_ets_opc_additionalinfo_field_value']);
                    $fieldValue->delete();
                }
            }
            die(
                json_encode(
                    array(
                        'hasError' => true,
                        'errors' => $this->module->displayError($this->errors),
                    )
                )
            );
        }   
    }
    protected function updateContext(Customer $customer)
    {
        $customer->logged = 1;
        $this->context->customer = $customer;
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = $customer->is_guest;
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cart->id_customer = $customer->id;
        $this->context->cookie->write();
        $this->context->cart->update();
        $customer->update();        
    }
    public function checkCaptcha()
    {
        
        if(Configuration::get('ETS_OPC_CAPTCHA_ENABLED') && Configuration::get('ETS_OPC_CAPTCHA_TYPE'))
        {
            $recaptcha = Tools::getValue('g-recaptcha-response',Tools::getValue('g_recaptcha_response')) ? : '';
            if(!$recaptcha)
            {
                $this->errors[] = $this->module->l('Please check on CAPTCHA to make sure you\'re not a robot','order');
                return false;
            }
            elseif(!Validate::isCleanHtml($recaptcha))
                $this->errors[] = $this->module->l('reCAPTCHA is not valid','order');
            else
            {
                $secret = Configuration::get('ETS_OPC_CAPTCHA_TYPE')=='google-v2' ? Configuration::get('ETS_OPC_CAPTCHA_SECRET_V2') : Configuration::get('ETS_OPC_CAPTCHA_SECRET_V3');
                $link_capcha="https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $recaptcha . "&remoteip=" . Tools::getRemoteAddr();
                if ($recaptcha) {
                    $response = json_decode(Tools::file_get_contents($link_capcha), true);
                    if ($response['success'] == false) {
                        $this->errors[] = $this->module->l('reCAPTCHA is invalid','order');
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
    protected function areProductsAvailable()
    {
        $products = $this->context->cart->getProducts();

        foreach ($products as $product) {
            $currentProduct = new Product();
            $currentProduct->hydrate($product);

            if ($currentProduct->hasAttributes() && $product['id_product_attribute'] === '0') {
                return sprintf($this->module->l('The item %s in your cart is now a product with attributes. Please delete it and choose one of its combinations to proceed with your order.','order'),$product['name']);
            }
        }

        $product = $this->context->cart->checkQuantities(true);

        if (true === $product || !is_array($product)) {
            return true;
        }

        if ($product['active']) {
            return sprintf($this->module->l('The item %s in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.','order'),$product['name']);
        }
        return sprintf($this->module->l('This product %s is no longer available.','order'),$product['name']);
    }
    public function convertDateBirthday($date)
    {
        $dateBuilt = DateTime::createFromFormat(
            $this->context->language->date_format_lite,
            $date
        );
        return $dateBuilt->format('Y-m-d');
    }
    public function checkPasswd($passwd)
    {
        if ($passwd && version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            if (Validate::isAcceptablePasswordLength($passwd) === false) {
                return sprintf($this->module->l('Password must be between %d and %d characters long','order'),Configuration::get(PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH),Configuration::get(PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH));
            }
            if (Validate::isAcceptablePasswordScore($passwd) === false) {
                $wordingsForScore = [
                    $this->module->l('Very weak','order'),
                    $this->module->l('Weak','order'),
                    $this->module->l('Average','order'),
                    $this->module->l('Strong','order'),
                    $this->module->l('Very strong','order'),
                ];
                return sprintf($this->module->l('The minimum score must be: %s','order'),$wordingsForScore[(int) Configuration::get(PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE)]);
            }
        }
        return '';
    }
}