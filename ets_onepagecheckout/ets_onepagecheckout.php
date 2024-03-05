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

if(!defined('_PS_VERSION_'))
	exit;
require_once(dirname(__FILE__).'/classes/src/autoload.php');
require_once(dirname(__FILE__).'/classes/Ets_opc_additionalinfo_field.php');
require_once(dirname(__FILE__).'/classes/Ets_opc_additionalinfo_field_value.php');
require_once(dirname(__FILE__).'/classes/Ets_opc_db.php');
if (!defined('_PS_ETS_OPC_UPLOAD_DIR_')) {
    define('_PS_ETS_OPC_UPLOAD_DIR_', _PS_DOWNLOAD_DIR_.'ets_onepagecheckout/');
}
if (!defined('_PS_ETS_OPC_UPLOAD_')) {
    define('_PS_ETS_OPC_UPLOAD_', __PS_BASE_URI__.'download/ets_onepagecheckout/');
}
class Ets_onepagecheckout extends Module
{
    public $is17=false;
    public static $document_link = 'https://demo2.presta-demos.com/docs/onepagecheckout/';
    public $file_types = array('jpg', 'gif', 'jpeg', 'png','doc','docs','docx','pdf','zip','txt');
    const LOGIN_PAYPAL = 1;
    const LOGIN_FACEBOOK = 2;
    const LOGIN_GOOGLE = 3;
    public function __construct()
	{
        $this->name = 'ets_onepagecheckout';
		$this->tab = 'front_office_features';
		$this->version = '2.6.0';
		$this->author = 'ETS-Soft';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        $this->module_key = 'fde54ba900cab00247b16782a636540a';
		parent::__construct();
        $this->displayName =$this->l('One Page Checkout & Social Login');
        $this->description = $this->l('Simplifies PrestaShop default checkout steps and helps customers checkout faster, easier and more secure thus reduce cart abandonment and increase conversion rate.');
$this->refs = 'https://prestahero.com/';
        $this->_path_module = $this->_path;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

    }
    public function hookActionDispatcherBefore($params)
    {
        if(isset($params['controller_type']) && $params['controller_type']==Dispatcher::FC_ADMIN)
        {
            $controller = Tools::getValue('controller');
            $context = $this->context;
            if(isset($context->employee->id) && $context->employee->id && $context->employee->isLoggedBack() && $controller=='AdminCustomers')
            {
                $this->addKeyTwig();
            }
        }
    }
    public function addKeyTwig()
    {
        if(version_compare(_PS_VERSION_, '1.7.6', '>=') && $this->active)
        {
            $this->assignTwigVar(
                array(
                    'text_ets_opc_create_google' => $this->l('Created with Google'),
                    'text_ets_opc_login_google' =>$this->l('Last logged in with Google'),
                    'text_ets_opc_create_paypal' => $this->l('Created with Paypal'),
                    'text_ets_opc_login_paypal' => $this->l('Last logged in with Paypal'),
                    'text_ets_opc_create_facebook' => $this->l('Created with Facebook'),
                    'text_ets_opc_login_facebook' => $this->l('Last logged in with Facebook')
                )
            );
        }
    }
    public function install()
	{
        if(self::isInstalled('etsextracustomerfields')){
            throw new PrestaShopException($this->l("The module etsextracustomerfields has been installed"));
        }
	    return parent::install() && Ets_opc_db::installDb()
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('displayHeader')
        && $this->registerHook('actionGetIDZoneByAddressID')
        && $this->registerHook('taxManager')
        && $this->registerHook('actionTaxManager')
        && $this->registerHook('displayOrderDetail')
        && $this->registerHook('displayAdminOrderLeft')
        && $this->registerHook('displayAdminOrderSide')
        && $this->registerHook('displayCustomerLoginFormAfter')
        && $this->registerHook('displayCustomerAccountForm')
        && $this->registerHook('actionAuthentication')
            && $this->registerHook('actionDispatcherBefore')
        && $this->registerHook('actionCustomerGridQueryBuilderModifier')
        && $this->_installDb()&& $this->_installLinkDefault() && $this->_installMailTemplate();
    }
    public function _installMailTemplate()
    {
        $languages = Language::getLanguages(false);
        if ($languages && is_array($languages)) {
            $temp_dir_ltr = dirname(__FILE__) . '/mails/en';
            if (!@file_exists($temp_dir_ltr))
                return true;
            foreach ($languages as $language) {
                if (isset($language['iso_code']) && ($language['iso_code'] != 'en' || $language['iso_code'] != 'he')) {
                    if (($new_dir = dirname(__FILE__) . '/mails/' . $language['iso_code'])) {
                        $this->recurseCopy($temp_dir_ltr, $new_dir);
                    }
                }
            }
        }
        return true;
    }
    public function recurseCopy($src, $dst)
    {
        if (!@file_exists($src))
            return false;
        $dir = opendir($src);
        if (!@is_dir($dst))
            @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } elseif (!@file_exists($dst . '/' . $file)) {
                    @copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public function _installDb()
    {
        $settings = $this->getSettingsField();
        if($settings)
        {
            foreach($settings as $setting)
            {
                if($setting['type']!='custom_html' && isset($setting['default']))
                    Configuration::updateValue($setting['name'],$setting['default']);
                    
            }
            Configuration::updateValue('ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED','firstname,lastname,country,state,postcode,city,address,post_code');
            Configuration::updateValue('ETS_OPC_CREATEACC_DISPLAY_FIELD_REQUIRED','psgdpr');
            Configuration::updateValue('ETS_OPC_GUEST_DISPLAY_FIELD_REQUIRED','psgdpr');
            Configuration::deleteByName('ETS_OPC_POSITION_FIELDS_ADDRESS');
        }  
        return true;
    }
    public function _installLinkDefault()
    {
        if(!Ets_opc_db::checkExistMeta())
        {
            $languages = Language::getLanguages(false);
            $meta_class = new Meta();
            $meta_class->page = 'module-'.$this->name.'-order';
            $meta_class->configurable=1;
            foreach($languages as $language)
            {
                $meta_class->title[$language['id_lang']] = $this->l('Checkout');
                $meta_class->url_rewrite[$language['id_lang']] = 'checkout';
                $meta_class->description[$language['id_lang']] = $this->l('Complete your order');
            }
            $meta_class->add();
        } 
        return true;
    }
    public function uninstall()
	{
        return parent::uninstall()
        && $this->unregisterHook('displayBackOfficeHeader') 
        && $this->unregisterHook('displayHeader')
        && $this->unregisterHook('actionGetIDZoneByAddressID')
        && $this->unregisterHook('taxManager')
            && $this->unregisterHook('actionDispatcherBefore')
        && $this->unregisterHook('actionTaxManager')
        && $this->unregisterHook('displayOrderDetail')
        && $this->unregisterHook('displayAdminOrderLeft')
        && $this->unregisterHook('displayAdminOrderSide')
        && $this->unregisterHook('displayCustomerLoginFormAfter')
        && $this->unregisterHook('displayCustomerAccountForm')
        && $this->unregisterHook('actionAuthentication')
        && $this->unregisterHook('actionCustomerGridQueryBuilderModifier')
        && $this->_uninstallDb() && Ets_opc_db::unInstallDb();
    }
    public function _uninstallDb()
    {
        $settings = $this->getSettingsField();
        if($settings)
        {
            foreach($settings as $setting)
            {
                if($setting['type']!='custom_html')
                    Configuration::deleteByName($setting['name']);
                if(isset($setting['name2']) && $setting['name2'])
                    Configuration::deleteByName($setting['name2']);
                    
            }
        }
        return true;
    }
    public function hookdisplayOverrideTemplate($params)
    {
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        $controller = Tools::getValue('controller');
        if (isset($params['template_file']) && $params['template_file'] == 'customer/_partials/address-form' && $fc=='module' && $module==$this->name && $controller=='order' ) {
            if($id_address = (int)Tools::getValue('id_address'))
                $address = new Address((int)$id_address);
            else
                $address = new Address();
            $this->context->smarty->assign(
                array(
                    'field_address' => $this->getListFieldsAddress(),
                    'id_country' => $address->id_country ? : ($this->context->country->id ? :(int)Configuration::get('PS_COUNTRY_DEFAULT')),
                )
            );
            return $this->getTemplatePath('form_address.tpl');
        }
    }
    public function checkValidateOnepage()
    {
        
        if(Configuration::get('ETS_OPC_1PAGE_ENABLED') && (!Configuration::get('ETS_OPC_TESTING_ENABLED') || (Configuration::get('ETS_OPC_TESTING_ENABLED') && Configuration::get('ETS_OPC_TEST_API') && in_array(Tools::getRemoteAddr(),array_map('trim',explode(',',Configuration::get('ETS_OPC_TEST_API')))))))
            return true;
        return false;
    }
    public function checkDisplayTax($id_customer)
    {
        $id_group = null;
        if ($id_customer) {
            $id_group = (int)Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }
        $group= new Group($id_group);
        return $group->price_display_method;
    }
    public function hookDisplayHeader()
    {
        $page_name =  $this->context->controller->php_self;
        $fc = Tools::getValue('fc');
        $module = Tools::getValue('module');
        $controller = Tools::getValue('controller');
        if($fc=='module' && $module==$this->name && $controller=='order')
        {
            $this->context->controller->addJqueryUI('datepicker');
            $this->context->controller->registerJavascript('jquery-ui-timepicker', '/js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.js', ['position' => 'bottom', 'priority' => 90]);            
            $this->context->controller->registerStylesheet('jquery-ui-timepicker', '/js/jquery/plugins/timepicker/jquery-ui-timepicker-addon.css', ['media' => 'all', 'priority' => 90]);
            $this->context->controller->addCSS($this->_path.'views/css/onepagecheckout.css');
            $this->context->controller->addJS($this->_path.'views/js/onepagecheckout.js');
            $this->context->controller->addJS($this->_path.'views/js/validate.js');
            $settings = $this->getSettingsField();
            $assign = array();
            if($settings)
            {
                foreach($settings as $setting)
                {
                    if($setting['type']!='custom_html')
                    {
                        if($setting['type']=='checkbox')
                        {
                            $assign[$setting['name']] = Configuration::get($setting['name']) ? explode(',',Configuration::get($setting['name'])) : array();
                            if(isset($setting['name2']))
                                $assign[$setting['name2']] = Configuration::get($setting['name2']) ? explode(',',Configuration::get($setting['name2'])) : array();
                        }    
                        else
                            $assign[$setting['name']] = Configuration::get($setting['name']);
                    }
                }
            }
            $this->context->smarty->assign($assign);
        }
        if($this->checkValidateOnepage() && ($page_name == 'order-opc' || ($page_name == 'order' && $module!= $this->name) ) )
        {
            Tools::redirect($this->context->link->getModuleLink($this->name,'order')); 
        }
        if(($ETS_OPC_PAGE_ENABLED_SOCIAL = Configuration::get('ETS_OPC_PAGE_ENABLED_SOCIAL')) && ($pages = explode(',',$ETS_OPC_PAGE_ENABLED_SOCIAL)))
        {
            if(( (in_array('login_page',$pages) || in_array('register_page',$pages))  && $controller=='authentication') || (in_array('checkout_page',$pages) && $fc=='module' && $module==$this->name && $controller=='order'))
            {   
                $this->context->controller->addJS($this->_path.'views/js/social.js');
                $this->context->controller->addCSS($this->_path.'views/css/social.css');
                $this->smarty->assign(
                    array(
                        'ETS_OPC_CHECK_BOX_NEWSLETTER' => (int)Configuration::get('ETS_OPC_CHECK_BOX_NEWSLETTER'),
                        'ETS_OPC_CHECK_BOX_OFFERS' => (int)Configuration::get('ETS_OPC_CHECK_BOX_OFFERS'),
                    )
                );
                return $this->display(__FILE__,'header.tpl');
            }
        }
        if($controller=='authentication' || $controller=='registration')
        {
            $this->smarty->assign(
                array(
                    'ETS_OPC_CHECK_BOX_NEWSLETTER' => (int)Configuration::get('ETS_OPC_CHECK_BOX_NEWSLETTER'),
                    'ETS_OPC_CHECK_BOX_OFFERS' => (int)Configuration::get('ETS_OPC_CHECK_BOX_OFFERS'),
                )
            );
            $this->context->controller->addJS($this->_path.'views/js/create_customer.js');
            return $this->display(__FILE__,'create_customer.tpl');
        }
        
    }
    public function hookDisplayBackOfficeHeader()
    {
        $controller= Tools::getValue('controller');
        $configure = Tools::getValue('configure');
        if($controller=='AdminModules' && $configure== $this->name)
        {
            $this->context->controller->addCSS($this->_path.'views/css/admin.css'); 
        }
        if($controller=='AdminCustomers')
        {
            $this->context->controller->addCSS($this->_path.'views/css/list_social.css');
        }
    }
    public function assignTwigVar($params)
    {
        /** @var \Twig\Environment $tw */
        if(!class_exists('Ets_onepagecheckout_twig'))
            require_once(dirname(__FILE__).'/classes/Ets_onepagecheckout_twig.php');
        if($sfContainer = $this->getSfContainer())
        {
            try {
                $tw = $sfContainer->get('twig');
                $tw->addExtension(new Ets_onepagecheckout_twig($params));
            } catch (\Twig\Error\RuntimeError $e) {
                // do no thing
            }
        }
    }
    public function addTwigVar($key, $value)
    {
        if($sfContainer = $this->getSfContainer())
        {
            $sfContainer->get('twig')->addGlobal($key, $value);
        }
    }
    public function getBreadCrumb()
    {
        $nodes = array();
        $controller = Tools::getValue('controller');
        if(!Validate::isControllerName($controller))
            $controller='';
        $nodes[] = array(
            'title' => $this->l('Home'),
            'url' => $this->context->link->getPageLink('index', true),
        );
        if($controller!='shop')
        {
            $nodes[] = array(
                'title' => $this->l('Checkout'),
                'url' => $this->context->link->getModuleLink($this->name,'order'),
            );
        }
        if($this->is17)
            return array('links' => $nodes,'count' => count($nodes));
        return $this->displayBreadcrumb($nodes);
    }
    public function displayBreadcrumb($nodes)
    {
        $this->smarty->assign(array('nodes' => $nodes));
        return  $this->display(__FILE__, 'nodes.tpl');
    }
    public function getContent()
    {
        $action = Tools::getValue('action');
        if($action =='updateAdditionalFieldOrdering')
        {
            if(($additionalfields = Tools::getValue('additionalfield')) && is_array($additionalfields) && Ets_onepagecheckout::validateArray($additionalfields,'isInt'))
            {
                if(Ets_opc_additionalinfo_field::updatePositions($additionalfields))
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated successfully'),
                            )
                        )
                    );
                }
            }
        }
        if($action=='updatePositionSocial')
        {
            $social = Tools::getValue('social');
            if($social && is_array($social) && Ets_onepagecheckout::validateArray($social))
                Configuration::updateValue('ETS_OPC_POSITION_SOCIAL',implode(',',$social));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )   
                )
            );
        }
        if($action=='updatePositionFieldAddress')
        {
            $fields = array();
            if(($field_address = Tools::getValue('field-address')) && is_array($field_address) && Ets_onepagecheckout::validateArray($field_address))
            {
                foreach($field_address as $field)
                {
                    $fields[] = $field;
                }
            }
            if($fields)
                Configuration::updateValue('ETS_OPC_POSITION_FIELDS_ADDRESS',implode(',',$fields));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )   
                )
            );
        }
        $this->context->controller->addJqueryUI('ui.sortable');
        if(Tools::isSubmit('btnSubmitRessetAddress'))
        {
            Configuration::updateValue('ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED','firstname,lastname,country,state,postcode,city,address,post_code');
            Configuration::deleteByName('ETS_OPC_POSITION_FIELDS_ADDRESS');
            Configuration::updateValue('ETS_OPC_ADDRESS_DISPLAY_FIELD','phone,alias,firstname,lastname,country,state,post_code,city,address,address2,company');   
            $this->context->cookie->success = $this->l('Reset to default successfully');
            $this->smarty->assign(
                array(
                    'field_address' => $this->getListFieldsAddress(),
                    'selected_address' => explode(',', Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD')),
                    'selected_required_address' => explode(',', Configuration::get('ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED')),
                )
            );
            die(
                json_encode(
                    array(
                        'success' => $this->l('Reset to default successfully'),
                        'list_fields_address' => $this->display(__FILE__,'list_fields_address.tpl'),
                    )
                )
            );        
        }
        if(Tools::isSubmit('btnSubmitRessetDesign'))
        {
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR1','#0cb7e2');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR2','#427e8d');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR3','#b2ced2');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR4','#2592a9');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR5','#ffffff');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR6','#ffffff');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR7','#0cb8e2');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR8','#427e8d');
            Configuration::updateValue('ETS_OPC_DESIGN_COLOR9','#ecfbff');
            die(
                json_encode(
                    array(
                        'success' => $this->l('Reset to default successfully'),
                    )
                )
            );
        }
        if(Tools::isSubmit('saveConfig'))
        {
            if($this->_checkValidatePost())
            {
                $this->_saveConfig();
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated successfully'),
                                'html_additional_info' => $this->_renderCustomField(),
                            )
                        )
                    );
                }
                else
                    Tools::redirect($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&conf=4');
            }
        }
        if(Tools::isSubmit('change_enabled') && ($table = Tools::getValue('table')))
        {
            if($table=='carrier')
            {
                if(($id_carrier = (int)Tools::getValue('id_carrier')) && Validate::isUnsignedId($id_carrier))
                {
                    $carrier = new Carrier($id_carrier);
                    $active= (int)Tools::getValue('change_enabled');
                    $carrier->active = $active ? 1:0;
                    if($carrier->update())
                    {
                        if($active)
                        {
                            die(
                                json_encode(
                                    array(
                                        'href' => $this->context->link->getAdminLink('AdminModules').'&configure=ets_onepagecheckout&table=carrier&id_carrier='.(int)$carrier->id.'&change_enabled=0&field=active',
                                        'title' => $this->l('Click to disable'),
                                        'success' => $this->l('Updated successfully'),
                                        'enabled' => 1,
                                    )
                                )  
                            );
                        }
                        else
                        {
                            die(
                                json_encode(
                                    array(
                                        'href' => $this->context->link->getAdminLink('AdminModules').'&configure=ets_onepagecheckout&table=carrier&id_carrier='.(int)$carrier->id.'&change_enabled=1&field=active',
                                        'title' => $this->l('Click to enable'),
                                        'success' => $this->l('Updated successfully'),
                                        'enabled' => 0,
                                    )
                                )  
                            );
                        }
                    }
                    else
                    {
                        die(
                            json_encode(
                                array(
                                    'errors' => $this->l('An error occurred while saving the carrier')
                                )
                            )
                        );
                    }
                }
            }
        }
        $this->context->smarty->assign(
            array(
                'form_html' => $this->_renderForm(),
                'ETS_OPC_MODULE_URL' => $this->_path,
                'link'=>$this->context->link
            )
        );
        if($this->context->cookie->success)
        {
            $success = $this->context->cookie->success;
            $this->context->cookie->success= '';
        }
        return (isset($success) ? $this->displayConfirmation($success):''). $this->display(__FILE__,'admin.tpl');
    }
    public function _checkValidatePost()
    {
        $errors = array();
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        if(!($ETS_OPC_ADDRESS_DISPLAY_FIELD = Tools::getValue('ETS_OPC_ADDRESS_DISPLAY_FIELD')))
            $errors[] = $this->l('Display fields are required');
        elseif(!is_array($ETS_OPC_ADDRESS_DISPLAY_FIELD) || !Ets_onepagecheckout::validateArray($ETS_OPC_ADDRESS_DISPLAY_FIELD))
            $errors[] = $this->l('Display fields are not valid');
        $ETS_OPC_TESTING_ENABLED = (int)Tools::getValue('ETS_OPC_TESTING_ENABLED');
        $ETS_OPC_TEST_API = Tools::getValue('ETS_OPC_TEST_API');
        if($ETS_OPC_TEST_API && !Validate::isCleanHtml($ETS_OPC_TEST_API))
            $errors[] = $this->l('A test IP address is not valid');
        if($ETS_OPC_TESTING_ENABLED &&  !$ETS_OPC_TEST_API)
            $errors[] = $this->l('A test IP address is required');
        $ETS_OPC_CAPTCHA_ENABLED = (int)Tools::getValue('ETS_OPC_CAPTCHA_ENABLED');
        $ETS_OPC_CAPTCHA_TYPE = Tools::getValue('ETS_OPC_CAPTCHA_TYPE');
        if($ETS_OPC_CAPTCHA_TYPE && !in_array($ETS_OPC_CAPTCHA_TYPE,array('google-v2','google-v3')))
            $errors[] = $this->l('Captcha type is not valid');
        $ETS_OPC_CAPTCHA_SITE_V2 = Tools::getValue('ETS_OPC_CAPTCHA_SITE_V2');
        if($ETS_OPC_CAPTCHA_SITE_V2 && !Validate::isCleanHtml($ETS_OPC_CAPTCHA_SITE_V2))
            $errors[]= $this->l('Site key for reCAPTCHA v2 is not valid');
        $ETS_OPC_CAPTCHA_SECRET_V2 = Tools::getValue('ETS_OPC_CAPTCHA_SECRET_V2');
        if($ETS_OPC_CAPTCHA_SECRET_V2 && !Validate::isCleanHtml($ETS_OPC_CAPTCHA_SECRET_V2))
            $errors[] = $this->l('Secret key for reCAPTCHA v2 is not valid');
        $ETS_OPC_CAPTCHA_SITE_V3 = Tools::getValue('ETS_OPC_CAPTCHA_SITE_V3');
        if($ETS_OPC_CAPTCHA_SITE_V3 && !Validate::isCleanHtml($ETS_OPC_CAPTCHA_SITE_V3))
            $errors[] = $this->l('Site key for reCAPTCHA v3 is not valid');
        $ETS_OPC_CAPTCHA_SECRET_V3 = Tools::getValue('ETS_OPC_CAPTCHA_SECRET_V3');
        if($ETS_OPC_CAPTCHA_SECRET_V3 && !Validate::isCleanHtml($ETS_OPC_CAPTCHA_SECRET_V3))
            $errors[] = $this->l('Secret key for reCAPTCHA v3 is not valid');
        if($ETS_OPC_CAPTCHA_ENABLED)
        {
            if($ETS_OPC_CAPTCHA_TYPE=='google-v2')
            {
                if(!$ETS_OPC_CAPTCHA_SITE_V2)
                    $errors[] = $this->l('Site key for reCAPTCHA v2 is required');
                if(!$ETS_OPC_CAPTCHA_SECRET_V2)
                    $errors[] = $this->l('Secret key for reCAPTCHA v2 is required');
            }
            else
            {
                if(!$ETS_OPC_CAPTCHA_SITE_V3)
                    $errors[] = $this->l('Site key for reCAPTCHA v3 is required');
                if(!$ETS_OPC_CAPTCHA_SECRET_V3)
                    $errors[] = $this->l('Secret key for reCAPTCHA v3 is required');
            }
        }
        $ETS_OPC_PAYPAL_ID = Tools::getValue('ETS_OPC_PAYPAL_ID');
        if($ETS_OPC_PAYPAL_ID && !Validate::isCleanHtml($ETS_OPC_PAYPAL_ID))
            $errors[] = $this->l('PayPal application ID is not valid');
        $ETS_OPC_PAYPAL_SECRET = Tools::getValue('ETS_OPC_PAYPAL_SECRET');
        if($ETS_OPC_PAYPAL_SECRET && !Validate::isCleanHtml($ETS_OPC_PAYPAL_SECRET))
            $errors[] = $this->l('PayPal application secret is not valid');
        $ETS_OPC_FACEBOOK_ID = Tools::getValue('ETS_OPC_FACEBOOK_ID');
        if($ETS_OPC_FACEBOOK_ID && !Validate::isCleanHtml($ETS_OPC_FACEBOOK_ID))
            $errors[] = $this->l('Facebook application ID is not valid');
        $ETS_OPC_FACEBOOK_SECRET = Tools::getValue('ETS_OPC_FACEBOOK_SECRET');
        if($ETS_OPC_FACEBOOK_SECRET && !Validate::isCleanHtml($ETS_OPC_FACEBOOK_SECRET))
            $errors[] = $this->l('Facebook application secret is not valid');
        $ETS_OPC_GOOGLE_ID = Tools::getValue('ETS_OPC_GOOGLE_ID');
        if($ETS_OPC_GOOGLE_ID && !Validate::isCleanHtml($ETS_OPC_GOOGLE_ID))
             $errors[] = $this->l('Google application ID is not valid');
        $ETS_OPC_GOOGLE_SECRET = Tools::getValue('ETS_OPC_GOOGLE_SECRET');
        if($ETS_OPC_GOOGLE_SECRET && !Validate::isCleanHtml($ETS_OPC_GOOGLE_SECRET))
            $errors[] = $this->l('Google application ID is not valid');
        $ETS_OPC_PAYPAL_ENABLED = (int)Tools::getValue('ETS_OPC_PAYPAL_ENABLED');
        if($ETS_OPC_PAYPAL_ENABLED)
        {
            if(!$ETS_OPC_PAYPAL_ID)
                $errors[] = $this->l('PayPal application ID is required');
            if(!$ETS_OPC_PAYPAL_SECRET)
                $errors[] = $this->l('PayPal application secret is required');
        }
        $ETS_OPC_FACEBOOK_ENABLED = (int)Tools::getValue('ETS_OPC_FACEBOOK_ENABLED');
        
        if($ETS_OPC_FACEBOOK_ENABLED)
        {
            if(!$ETS_OPC_FACEBOOK_ID)
                $errors[] = $this->l('Facebook application ID is required');
            if(!$ETS_OPC_FACEBOOK_SECRET)
                $errors[] = $this->l('Facebook application secret is required');
        }
        $ETS_OPC_GOOGLE_ENABLED = (int)Tools::getValue('ETS_OPC_GOOGLE_ENABLED');
        if($ETS_OPC_GOOGLE_ENABLED)
        {
            if(!$ETS_OPC_GOOGLE_ID)
                $errors[] = $this->l('Google application ID is required');
            if(!$ETS_OPC_GOOGLE_SECRET)
                $errors[] = $this->l('Google application secret is required');
        }
        $ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED = (int)Tools::getValue('ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED');
        $ETS_OPC_GOOGLE_KEY_API = Tools::getValue('ETS_OPC_GOOGLE_KEY_API');
        if($ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED && !$ETS_OPC_GOOGLE_KEY_API)
            $errors[] = $this->l('Google API key is required');
        if($ETS_OPC_GOOGLE_KEY_API && !Validate::isCleanHtml($ETS_OPC_GOOGLE_KEY_API))
            $errors[] = $this->l('Google API key is not valid');
        if (($fields = Tools::getValue('custom_field', array())) && is_array($fields) && Ets_onepagecheckout::validateArray($fields) ) {
            foreach ($fields as $field) {
                if (isset($field['title']) && is_array($field['title']) && Ets_onepagecheckout::validateArray($field['title']) && $field['title']) {
                    if(!isset($field['title'][$id_lang_default]) || !$field['title'][$id_lang_default])
                        $errors[] = $this->l('"Title" field is required');
                    foreach ($field['title'] as $title) {
                        if($title){
                            if (!Validate::isString($title)) {
                                $errors[] = $this->l('"Title" field must be a string');
                            }
                        }
                    }
                }
                else
                    $errors[] = $this->l('"Title" field is not valid');
                if (isset($field['description']) && is_array($field['description']) && Ets_onepagecheckout::validateArray($field['description']) && $field['description']) {
                    foreach ($field['description'] as $description) {
                        if($description){
                            if (!Validate::isString($description)) {
                                $errors[] = $this->l('"Description" field must be a string');
                            }
                        }
                    }
                }
                if($field['type']=='radio' || $field['type']=='checkbox' || $field['type']=='select')
                {
                    if(!isset($field['options'][$id_lang_default]) || !$field['options'][$id_lang_default])
                        $errors[] = $this->l('"Options" field is required');
                    foreach ($field['options'] as $option) {
                        if($option){
                            if (!Validate::isString($option)) {
                                $errors[] = $this->l('"Title" field must be a string');
                            }
                        }
                    }
                }
            }
        }
        $languages = Language::getLanguages(false);
        $meta_title_default = Tools::getValue('title_'.$id_lang_default);
        $url_rewrite_default = Tools::getValue('url_rewrite_'.$id_lang_default);
        if(!$meta_title_default)
            $errors[] = $this->l('Meta title is required');
        if(!$url_rewrite_default)
            $errors[] = $this->l('Url alias is required');
        if($languages)
        {
            foreach($languages as $language)
            {
                if(($title= Tools::getValue('title_'.$language['id_lang'])) && (!Validate::isGenericName($title) || Tools::strlen($title)>128))
                    $errors[] = $this->l('Meta title is not valid in').' '.$language['iso_code'];
                if(($description= Tools::getValue('description_'.$language['id_lang'])) && (!Validate::isGenericName($description) || Tools::strlen($description)>255))
                    $errors[] = $this->l('Meta description is not valid in').' '.$language['iso_code'];
                if(($url_rewrite= Tools::getValue('url_rewrite_'.$language['id_lang'])) && (!Validate::isLinkRewrite($url_rewrite) || Tools::strlen($url_rewrite)>255))
                    $errors[] = $this->l('Url alias is not valid in').' '.$language['iso_code'];
            }
        }
        $settings = $this->getSettingsField();
        foreach($settings as $input) {
            if ($input['type'] == 'html' || $input['type']=='custom_html')
                continue;
            if(isset($input['lang']) && $input['lang'])
            {
                if(isset($input['required']) && $input['required'])
                {
                    $val_default = Tools::getValue($input['name'].'_'.$id_lang_default);
                    if(!$val_default)
                    {
                        $errors[] = sprintf($this->l('%s is required'),$input['label']);
                    }
                    elseif($val_default && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate) && !Validate::{$validate}($val_default,true))
                        $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    elseif($val_default && !Validate::isCleanHtml($val_default,true))
                        $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    else
                    {
                        foreach($languages as $language)
                        {
                            if(($value = Tools::getValue($input['name'].'_'.$language['id_lang'])) && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate)  && !Validate::{$validate}($value,true))
                                $errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                            elseif($value && !Validate::isCleanHtml($value,true))
                                $errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                        }
                    }
                }
                else
                {
                    foreach($languages as $language)
                    {
                        if(($value = Tools::getValue($input['name'].'_'.$language['id_lang'])) && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate)  && !Validate::{$validate}($value,true))
                            $errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                        elseif($value && !Validate::isCleanHtml($value,true))
                            $errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                    }
                }
            }
            else
            {
                if($input['type']=='file')
                {

                    if(isset($input['required']) && $input['required'] && (!isset($_FILES[$input['name']]) || !isset($_FILES[$input['name']]['name']) ||!$_FILES[$input['name']]['name']))
                    {
                        $errors[] = sprintf($this->l('%s is required'),$input['label']);
                    }
                    elseif(isset($_FILES[$input['name']]) && isset($_FILES[$input['name']]['name'])  && $_FILES[$input['name']]['name'])
                    {
                        $file_name = $_FILES[$input['name']]['name'];
                        $file_size = $_FILES[$input['name']]['size'];
                        $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                        $type = Tools::strtolower(Tools::substr(strrchr($file_name, '.'), 1));
                        if(isset($input['is_image']) && $input['is_image'])
                            $file_types = array('jpg', 'png', 'gif', 'jpeg');
                        else
                            $file_types = array('jpg', 'png', 'gif', 'jpeg','zip','doc','docx');
                        if(!in_array($type,$file_types))
                            $errors[] = sprintf($this->l('The file name "%s" is not in the correct format, accepted formats: %s'),$file_name,'.'.trim(implode(', .',$file_types),', .'));
                        $max_file_size = $max_file_size ? : Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                        if($file_size > $max_file_size)
                            $errors[] = sprintf($this->l('The file name "%s" is too large. Limit: %s'),$file_name,Tools::ps_round($max_file_size/1048576,2).'Mb');
                    }
                }
                else
                {
                    $val = Tools::getValue($input['name']);
                    if($input['type']!='checkbox')
                    {

                        if($val===''&& isset($input['required']) && $input['required'])
                        {
                            $errors[] = sprintf($this->l('%s is required'),$input['label']);
                        }
                        if($val!=='' && isset($input['validate']) && ($validate = $input['validate']) && $validate=='isColor' && !self::isColor($val))
                        {
                            $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                        }
                        elseif($val!=='' && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate) && !Validate::{$validate}($val))
                        {
                            $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                        }
                        elseif($val!=='' && $val<=0 && isset($input['validate']) && ($validate = $input['validate']) && $validate=='isUnsignedInt')
                        {
                            $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                        }
                        elseif($val!==''&& !Validate::isCleanHtml($val))
                            $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    }
                    else
                    {
                        if(!$val&& isset($input['required']) && $input['required'] )
                        {
                            $errors[] = sprintf($this->l('%s is required'),$input['label']);
                        }
                        elseif($val && !self::validateArray($val,isset($input['validate']) ? $input['validate']:''))
                            $errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    }
                }
            }
        }
        if(!$errors)
            return true;
        else
        {
            die(
                json_encode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
        }
    }
    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6})$/', $color);
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = Ets_onepagecheckout::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public function _saveConfig()
    {
        $settings = $this->getSettingsField();
        $languages = Language::getLanguages(false);
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $meta = Meta::getMetaByPage('module-'.$this->name.'-order',$this->context->language->id);
        if($meta && ($id_meta = $meta['id_meta']))
        {
            $meta_class = new Meta($id_meta);
        }
        else
        {
            $meta_class = new Meta();
            $meta_class->page = 'module-'.$this->name.'-order';
        }
        $title_default = Tools::getValue('title_'.$id_lang_default);
        $description_default = Tools::getValue('description_'.$id_lang_default);
        $url_rewrite_default = Tools::getValue('url_rewrite_'.$id_lang_default);
        foreach($languages as $language)
        {
            $meta_class->title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']) ? : $title_default;
            $meta_class->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']) ?: $description_default;
            $meta_class->url_rewrite[$language['id_lang']] = Tools::getValue('url_rewrite_'.$language['id_lang']) ?: $url_rewrite_default;
        }
        if($meta_class->id)
            $meta_class->update();
        else
            $meta_class->add();
        if($settings)
        {
            foreach($settings as $setting)
            {
                if($setting['tab']!='seo')
                {
                    $name = $setting['name'];
                    if(isset($setting['lang']) && $setting['lang'])
                    {
                        $valules = array();
                        foreach($languages as $lang)
                        {
                            $valules[$lang['id_lang']] = trim(Tools::getValue($name.'_'.$lang['id_lang'])) ;//trim(Tools::getValue($key.'_'.$id_lang_default))
                        }
                        Configuration::updateValue($name,$valules);
                    }
                    else
                    {
                        if($setting['type']=='checkbox')
                        {
                            $value = Tools::getValue($name);
                            Configuration::updateValue($name,$value && is_array($value) && Ets_onepagecheckout::validateArray($value) ? implode(',',$value):'');
                            if($setting['extra_field'] && $setting)
                            {
                                $name2= $setting['name2'];
                                $value2 = Tools::getValue($name2);
                                Configuration::updateValue($name2,$value2 && is_array($value2) && Ets_onepagecheckout::validateArray($value2) ? implode(',',$value2):'');
                            }
                            if($name=='ETS_OPC_CREATEACC_DISPLAY_FIELD' || $name =='ETS_OPC_GUEST_DISPLAY_FIELD')
                            {
                                if($value && in_array('optin',$value))
                                    Configuration::updateValue('PS_CUSTOMER_OPTIN',1);
                                else
                                    Configuration::updateValue('PS_CUSTOMER_OPTIN',0);
                                if($value && in_array('psgdpr',$value))
                                    Configuration::updateValue('PSGDPR_CREATION_FORM_SWITCH',1);
                                else
                                    Configuration::updateValue('PSGDPR_CREATION_FORM_SWITCH',0);
                            }
                        }
                        else
                        {
                            $value = Tools::getValue($name);
                            Configuration::updateValue($name,$value);  
                        }
                        
                    }
                }
                
            }
        }
        Ets_opc_additionalinfo_field::deleteAllField();
        if (($fields = Tools::getValue('custom_field', array())) && is_array($fields) && Ets_onepagecheckout::validateArray($fields)) {
            $sort=1;
            foreach($fields as $field)
            {
                if(isset($field['id']) && $field['id'])
                    $class_field = new Ets_opc_additionalinfo_field($field['id']);
                else
                {
                    $class_field = new Ets_opc_additionalinfo_field();
                }    
                $class_field->type = $field['type'];
                $class_field->required = $field['required'];
                $class_field->enable = $field['enable'];
                $class_field->deleted=0;
                $class_field->sort=$sort;
                $sort++;
                foreach($languages as $language)
                {
                    $class_field->title[$language['id_lang']] = trim($field['title'][$language['id_lang']]) ? trim($field['title'][$language['id_lang']]) : trim($field['title'][$id_lang_default]);
                    $class_field->description[$language['id_lang']] = trim($field['description'][$language['id_lang']]) ? trim($field['description'][$language['id_lang']]) : trim($field['description'][$id_lang_default]);
                    if($field['type']=='radio' || $field['type']=='checkbox' || $field['type']=='select')
                        $class_field->options[$language['id_lang']] = trim($field['options'][$language['id_lang']]) ? trim($field['options'][$language['id_lang']]) : trim($field['options'][$id_lang_default]);
                    else
                        $class_field->options[$language['id_lang']] = ''; 
                }
                if($class_field->id)
                    $class_field->update();
                else
                    $class_field->add();
            }
        }
    }
    public function getSettingsField($render_form = false)
    {
        $create_account_fields = array(
            array(
                'id'=>'social_title',
                'label' => $this->l('Social title')
            ),
            array(
                'id'=>'firstname',
                'label' => $this->l('First name'),
                'required' => true,    
            ),
            array(
                'id'=>'lastname',
                'label' => $this->l('Last name'),
                'required' => true,    
            ),
            array(
                'id' => 'email', 
                'label' => $this->l('Email'),
                'required' => true,                                
            ),
            array(
                'id' => 'password', 
                'label' => $this->l('Password'),  
                'required' => true,                                
            ),
            array(
                'id'=>'birthday',
                'label' => $this->l('Birthday'),
            ),
            array(
                'id'=>'optin',
                'label' => $this->l('Receive offers from our partners '),
            )
        );
        $guest_account_fields = array(
            array(
                'id'=>'social_title',
                'label' => $this->l('Social title')
            ),
            array(
                'id'=>'firstname',
                'label' => $this->l('First name'),
                'required' => true,    
            ),
            array(
                'id'=>'lastname',
                'label' => $this->l('Last name'),
                'required' => true,    
            ),
            array(
                'id' => 'email', 
                'label' => $this->l('Email'),
                'required' => true,                                
            ),
            array(
                'id' => 'password', 
                'label' => $this->l('Password'),                                
            ),
            array(
                'id'=>'birthday',
                'label' => $this->l('Birthday'),
            ),
            array(
                'id'=>'optin',
                'label' => $this->l('Receive offers from our partners '),
            )
        );
        $default_guest_filed ='';
        $default_acount_filed='';
        if(Configuration::get('PS_CUSTOMER_OPTIN'))
        {
            $default_acount_filed .=',optin';
            $default_guest_filed .=',optin';
        }
        if(Module::isEnabled('ps_emailsubscription'))
        {
            $create_account_fields[] = array(
                'id'=>'newsletter',
                'label' => $this->l('Sign up for our newsletter'),
            );
            $guest_account_fields[] = array(
                'id'=>'newsletter',
                'label' => $this->l('Sign up for our newsletter'),
            );
            $default_guest_filed .=',newsletter';
            $default_acount_filed .=',newsletter';
        }
        if(Module::isEnabled('psgdpr'))
        {
            $default_acount_filed .= ',psgdpr';
            $default_guest_filed .= ',psgdpr';
            $create_account_fields[] = array(
                'id'=>'psgdpr',
                'label' => $this->l('I agree to the terms and conditions and the privacy policy'),
            );
            $guest_account_fields[] = array(
                'id'=>'psgdpr',
                'label' => $this->l('I agree to the terms and conditions and the privacy policy'),
            );
        }
        if(Module::isEnabled('ps_dataprivacy'))
        {
            $create_account_fields[] = array(
                'id'=>'customer_privacy',
                'label' => $this->l('Customer data privacy'),
            );
            $guest_account_fields[] = array(
                'id'=>'customer_privacy',
                'label' => $this->l('Customer data privacy'),
            );
        }
        return array(
            array(
                'type' =>'switch',
                'label' => $this->l('Enable One page checkout'),
                'name' => 'ETS_OPC_1PAGE_ENABLED',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable testing mode'),
                'name' => 'ETS_OPC_TESTING_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type'=>'text',
                'name' => 'ETS_OPC_TEST_API',
                'label' => $this->l('Add IP address to test'),
                'required2' => true,
                'desc' => $this->l('Each IP address is separated by a comma (,)'),
                'tab'=>'settings',
                'form_group_class' => 'test',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable captcha'),
                'name' => 'ETS_OPC_CAPTCHA_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'label' => $this->l('Captcha type'),
                'name' => 'ETS_OPC_CAPTCHA_TYPE',
                'type' => 'radio',
                'values'=>array(
                    array(
                        'id'=>'ETS_OPC_CAPTCHA_TYPE_google_v2',
                        'value'=>'google-v2',
                        'label'=> $this->l('Google reCAPTCHA - v2')
                    ),
                    array(
                        'id'=>'ETS_LC_LIVECHAT_ON_google_v3',
                        'value'=>'google-v3',
                        'label'=> $this->l('Google reCAPTCHA - v3')
                    ),
                ),   
                'default' => 'google-v2',
                'tab'=>'settings', 
                'form_group_class' => 'captchatype',
            ),
            array(
    			'type' => 'text',
    			'label' => $this->l('Site key'),
    			'name' => 'ETS_OPC_CAPTCHA_SITE_V2',   
                'form_group_class' => 'captcha google-v2',    
                'required2' => true,  
                'tab'=>'settings',       						
    		),
            array(
    			'type' => 'text',
    			'label' => $this->l('Secret key'),
    			'name' => 'ETS_OPC_CAPTCHA_SECRET_V2',   
                'form_group_class' => 'captcha google-v2', 
                'required2' => true, 
                'tab'=>'settings',
                'desc' => !$render_form ? '': $this->l('Get Site key and Secret key: ').Module::getInstanceByName('ets_onepagecheckout')->displayText('https://www.google.com/recaptcha/admin/create','a','','','https://www.google.com/recaptcha/admin/create','_blank','','','','','','noreferrer noopener'),
    		),
            array(
    			'type' => 'text',
    			'label' => $this->l('Site key'),
    			'name' => 'ETS_OPC_CAPTCHA_SITE_V3',   
                'form_group_class' => 'captcha google-v3',  
                'required2' => true,   
                'tab'=>'settings',        						
    		),
            array(
    			'type' => 'text',
    			'label' => $this->l('Secret key'),
    			'name' => 'ETS_OPC_CAPTCHA_SECRET_V3',   
                'form_group_class' => 'captcha google-v3',
                'required2' => true,  
                'tab'=>'settings',
                'desc' => !$render_form ? '': $this->l('Get Site key and Secret key: ').Module::getInstanceByName('ets_onepagecheckout')->displayText('https://www.google.com/recaptcha/admin/create','a','','','https://www.google.com/recaptcha/admin/create','_blank','','','','','','noreferrer noopener'),
    		),
            array(
                'label' => $this->l('Default account form'),
                'name' => 'ETS_OPC_DEFAULT_ACCOUNT_FORM',
                'type' => 'radio',
                'values'=>array(
                    array(
                        'id'=>'ETS_OPC_DEFAULT_ACCOUNT_FORM_create',
                        'value'=>'create',
                        'label'=> $this->l('Create account')
                    ),
                    array(
                        'id'=>'ETS_OPC_DEFAULT_ACCOUNT_FORM_login',
                        'value'=>'login',
                        'label'=> $this->l('Log in')
                    ),
                    array(
                        'id'=>'ETS_OPC_DEFAULT_ACCOUNT_FORM_guest',
                        'value'=>'guest',
                        'label'=> $this->l('Guest order')
                    )
                ),   
                'default' => 'create',
                'tab'=>'settings', 
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Display comment box'),
                'name' => 'ETS_OPC_CART_COMMENT_ENABLED',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Display Customer reassurance block on checkout page'),
                'name' => 'ETS_OPC_SHOW_CUSTOMER_REASSURANCE',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Display notification about shipping fee'),
                'desc' => $this->l('Display a notification to customers to remind them about how much money should they spend more to be able to enjoy free shipping'),
                'name' => 'ETS_OPC_SHOW_SHIPPING',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type' =>'switch',
                'label' => sprintf($this->l('Automatically check %sI agree with Terms and Conditions%s box'),'"','"'),
                'name' => 'ETS_OPC_CHECK_DEFAULT_CONDITION',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type' =>'switch',
                'label' => sprintf($this->l('Automatically check %sSign up for our newsletter%s box'),'"','"'),
                'name' => 'ETS_OPC_CHECK_BOX_NEWSLETTER',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'ETS_OPC_CHECK_BOX_NEWSLETTER_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'ETS_OPC_CHECK_BOX_NEWSLETTER_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'type' =>'switch',
                'label' => sprintf($this->l('Automatically check %sReceive offers from our partners%s box'),'"','"'),
                'name' => 'ETS_OPC_CHECK_BOX_OFFERS',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'ETS_OPC_CHECK_BOX_OFFERS_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'ETS_OPC_CHECK_BOX_OFFERS_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'settings',
            ),
            array(
                'name'=>'ETS_OPC_LOGIN_DISPLAY_FIELD',
                'name2' =>'ETS_OPC_LOGIN_DISPLAY_FIELD_REQUIRED',
                'label' => $this->l('Display fields'),
                'type' => 'checkbox',    
                'tab'=>'account',
                'sub_tab' =>'login',
                'extra_field'=>true,
                'values' => array(
                     'query' => array(
                        array(
                            'id' => 'email', 
                            'label' => $this->l('Email'),
                            'required' => true,                                
                        ),
                        array(
                            'id' => 'password', 
                            'label' => $this->l('Password'),  
                            'required' => true,                                
                        ),
                     ), 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                )
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable captcha'),
                'name' => 'ETS_OPC_LOGIN_CAPTCHA_ENABLED',
                'default'=>0,
                'sub_tab' =>'login',
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'account',
            ),
            
            array(
                'type' =>'switch',
                'label' => $this->l('Enable guest order'),
                'name' => 'PS_GUEST_CHECKOUT_ENABLED',
                'sub_tab' =>'guest',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'account',
            ),
            array(
                'name'=>'ETS_OPC_GUEST_DISPLAY_FIELD',
                'name2' =>'ETS_OPC_GUEST_DISPLAY_FIELD_REQUIRED',
                'label' => $this->l('Display fields'),
                'type' => 'checkbox',    
                'tab'=>'account',
                'extra_field'=>true,
                'sub_tab' =>'guest',
                'default' => trim($default_guest_filed,','),
                'values' => array(
                     'query' => $guest_account_fields, 
                     'id' => 'id',
    	             'name' => 'label',
                                                                     
                ),
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable captcha'),
                'name' => 'ETS_OPC_GUEST_CAPTCHA_ENABLED',
                'sub_tab' =>'guest',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'account',
            ),
            array(
                'name'=>'ETS_OPC_CREATEACC_DISPLAY_FIELD',
                'name2' =>'ETS_OPC_CREATEACC_DISPLAY_FIELD_REQUIRED',
                'label' => $this->l('Display fields'),
                'type' => 'checkbox',    
                'tab'=>'account',
                'extra_field'=>true,
                'sub_tab' =>'create',
                'default'=>trim($default_acount_filed,','),
                'values' => array(
                     'query' => $create_account_fields, 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                )
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable captcha'),
                'name' => 'ETS_OPC_CREATEACC_CAPTCHA_ENABLED',
                'sub_tab' =>'create',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'account',
            ),
            array(
                'name'=>'ETS_OPC_PAGE_ENABLED_SOCIAL',
                'label' => $this->l('Enable social login buttons on'),
                'type' => 'checkbox',    
                'tab'=>'social_login',
                'default'=>'register_page,checkout_page,login_page',
                'values' => array(
                     'query' => array(
                        array(
                            'id'=>'register_page',
                            'label' => $this->l('Registration page')
                        ),
                        array(
                            'id'=>'login_page',
                            'label' => $this->l('Login page')
                        ),
                        array(
                            'id'=>'checkout_page',
                            'label' => $this->l('Checkout page')
                        )
                     ), 
                     'id' => 'id',
    	             'name' => 'label',                                                               
                )
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable'),
                'name' => 'ETS_OPC_PAYPAL_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'start_social' => 'paypal',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application ID'),
                'required2' => true,
                'name' => 'ETS_OPC_PAYPAL_ID',
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'desc' =>!$render_form ? '': Module::getInstanceByName('ets_onepagecheckout')->displayText($this->l('Where do I get this info?'),'a','','',$this->context->link->getModuleLink($this->name,'document',array('doc'=>'GetPayPalInfo')),'_blank'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application Secret'),
                'required2' => true,
                'name' => 'ETS_OPC_PAYPAL_SECRET',
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'desc' => !$render_form ? '': Module::getInstanceByName('ets_onepagecheckout')->displayText($this->l('Where do I get this info?'),'a','','',$this->context->link->getModuleLink($this->name,'document',array('doc'=>'GetPayPalInfo')),'_blank'),
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Use sandbox mode'),
                'name' => 'ETS_OPC_PAYPAL_SANBOX_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'end_social' => 'paypal',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enabled'),
                'name' => 'ETS_OPC_FACEBOOK_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'start_social' => 'facebook',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application ID'),
                'required2' => true,
                'name' => 'ETS_OPC_FACEBOOK_ID',
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'desc' => !$render_form ? '': Module::getInstanceByName('ets_onepagecheckout')->displayText($this->l('Where do I get this info?'),'a','','',$this->context->link->getModuleLink($this->name,'document',array('doc'=>'GetFacebookAppInfo')),'_blank'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application Secret'),
                'required2' => true,
                'name' => 'ETS_OPC_FACEBOOK_SECRET',
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'desc' => !$render_form ? '': Module::getInstanceByName('ets_onepagecheckout')->displayText($this->l('Where do I get this info?'),'a','','',$this->context->link->getModuleLink($this->name,'document',array('doc'=>'GetFacebookAppInfo')),'_blank'),
                'end_social' => 'facebook',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enabled'),
                'name' => 'ETS_OPC_GOOGLE_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'start_social' => 'google',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application ID'),
                'required2' => true,
                'name' => 'ETS_OPC_GOOGLE_ID',
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'desc' => !$render_form ? '': Module::getInstanceByName('ets_onepagecheckout')->displayText($this->l('Where do I get this info?'),'a','','',$this->context->link->getModuleLink($this->name,'document',array('doc'=>'GetGoogleClientInfo')),'_blank'),

            ),
            array(
                'type' => 'text',
                'label' => $this->l('Application Secret'),
                'required2' => true,
                'name' => 'ETS_OPC_GOOGLE_SECRET',
                'tab'=>'social_login',
                'form_group_class' => 'social_login', 
                'desc' => !$render_form ? '': Module::getInstanceByName('ets_onepagecheckout')->displayText($this->l('Where do I get this info?'),'a','','',$this->context->link->getModuleLink($this->name,'document',array('doc'=>'GetGoogleClientInfo')),'_blank'),
                'end_social' => 'google',
            ),
            array(
                'name'=>'ETS_OPC_ADDRESS_DISPLAY_FIELD',
                'name2' =>'ETS_OPC_ADDRESS_DISPLAY_FIELD_REQUIRED',
                'label' => $this->l('Display fields'),
                'type' => 'checkbox',    
                'tab'=>'address',
                'extra_field'=>true,
                'values' => !$render_form ? array(): array(
                     'query' => $this->getListFieldsAddress(), 
                     'id' => 'id',
    	             'name' => 'label', 
                                                                                  
                ),
                'default'=>'use_address,phone,alias,firstname,lastname,country,state,post_code,city,address,address2,company,use_address_invoice', 
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Enable Google auto-fill for address'),
                'name' => 'ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'address',
                
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Google API key'),
                'required2' => true,
                'name' => 'ETS_OPC_GOOGLE_KEY_API',
                'tab'=>'address',
                'form_group_class' => 'autofill',
                'desc' => !$render_form ? '': $this->l('Make sure you have enabled "Places API" for the project associated with this key.').' '.$this->l('To learn how to get an API key, please visit this site').': '.Module::getInstanceByName('ets_onepagecheckout')->displayText('https://developers.google.com/places/web-service/get-api-key','a','','','https://developers.google.com/places/web-service/get-api-key','_blank','','','','','','noreferrer noopener'),
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Use customer\'s first name and last name for address when creating account'),
                'name' => 'ETS_OPC_USE_NAME_ACCOUNT',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'address',
            ),
            

            array(
                'type'=>'custom_html',
                'html_custom'=> !$render_form ? '':$this->displayCarrierList(),
                'name' => 'carrier_list',
                'tab' => 'shipping',
            ),
            array(
                'type' =>'switch',
                'label' => $this->l('Display shipping service logo'),
                'name' => 'ETS_OPC_SHIPPING_LOGO_ENABLED',
                'default'=>1,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'shipping',
            ),
            array(
                'type'=>'custom_html',
                'html_custom'=> !$render_form ? '':$this->displayPaymentList(),
                'tab' => 'payment',
                'name'=>'payment_list',
            ), 
            array(
                'type' =>'switch',
                'label' => $this->l('Display payment method logo'),
                'name' => 'ETS_OPC_PAYMENT_LOGO_ENABLED',
                'default'=>0,
                'values' => array(
    				array(
    					'id' => 'active_on',
    					'value' => 1,
    					'label' => $this->l('Yes')
    				),
    				array(
    					'id' => 'active_off',
    					'value' => 0,
    					'label' => $this->l('No')
    				)
    			),
                'tab'=>'payment',
            ),     
            array(
                'type' =>'select',
                'label' => $this->l('Default payment method'),
                'name' => 'ETS_OPC_PAYMENT_DEFAULT',
                'options' =>!$render_form ? array(): array(
                    'query' =>array_merge(array(array('name'=>'','module_name' => $this->l('-- Select method --') )), $this->getPayments()),
                    'id'=>'name',
                    'name' =>'module_name',
                ),
                'tab'=>'payment',
            ),      
            array(
                'type' =>'color',
                'label' => $this->l('Color 1'),
                'name' => 'ETS_OPC_DESIGN_COLOR1',
                'default' => '#0cb7e2',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Top border, price, total (Tax incl.), background and button border, etc.'),
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 2'),
                'name' => 'ETS_OPC_DESIGN_COLOR2',
                'default' => '#427e8d',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: title, icon, product name, quantity, etc.'),
                
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 3'),
                'name' => 'ETS_OPC_DESIGN_COLOR3',
                'default' => '#b2ced2',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: box border, field border, dropdown icon, password icon, selection, etc.'),
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 4'),
                'name' => 'ETS_OPC_DESIGN_COLOR4',
                'default' => '#2592a9',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Background and button border when hovering mouse pointer over'),
                
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 5'),
                'name' => 'ETS_OPC_DESIGN_COLOR5',
                'default' => '#ffffff',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Button text'),
                
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 6'),
                'name' => 'ETS_OPC_DESIGN_COLOR6',
                'default' => '#ffffff',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Button text when hovering mouse pointer over'),
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 7'),
                'name' => 'ETS_OPC_DESIGN_COLOR7',
                'default' => '#0cb8e2',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Text link'),
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 8'),
                'name' => 'ETS_OPC_DESIGN_COLOR8',
                'default' => '#427e8d',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Text link when hovering mouse pointer over'),
            ),
            array(
                'type' =>'color',
                'label' => $this->l('Color 9'),
                'name' => 'ETS_OPC_DESIGN_COLOR9',
                'default' => '#ecfbff',
                'tab' => 'design',
                'validate'=>'isColor',
                'desc' => $this->l('Change color for: Discount box background'),
            ),
            array(
                'type' =>'custom_html',
                'label' => '',
                'name' => 'additional_info',
                'tab' => 'additional',
                'html_custom' =>!$render_form ? '': $this->_renderCustomField(),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Meta title'),
                'name'=>'title',
                'lang'=>true,
                'tab' => 'seo',
                'required' => true,
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Url alias'),
                'name'=>'url_rewrite',
                'required' => true,
                'lang'=>true,
                'tab' => 'seo',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Meta description'),
                'name'=>'description',
                'lang'=>true,
                'tab' => 'seo',
                'row'=>4
            ),
        );
    }
    public function _renderCustomField()
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $fields = Ets_opc_additionalinfo_field::getListField();
        $this->smarty->assign(
            array(
                'custom_fields' => $fields,
            )
        );
        $this->smarty->assign(
            array(
                'languages' => $languages,
                'default_lang' => (int)$id_lang_default,
                'currency' => Currency::getDefaultCurrency(),
            )
        );
        return $this->display(__FILE__,'form_custom_field.tpl');
    }
    public function _renderForm()
    {
        $languages = Language::getLanguages(false);
        $fields_form = array(
    		'form' => array(
    			'legend' => array(
    				'title' => $this->l('General'),
    				'icon' => 'general-settings'
    			),
    			'input' => array(),
                'submit' => array(
    				'title' => $this->l('Save'),
    			),
                'reset' => array(
                    'title' => $this->l('Reset to default'),
                    'icon' => 'process-icon-refresh',
                ),
                'buttons' => array(
                    array(
                        'type' => 'submit',
                        'id' => 'btnSubmitRessetAddress',
                        'icon' => 'process-icon-refresh',
                        'title' => $this->l('Reset to default'),
                        'name' => 'btnSubmitRessetAddress',
                    )
                )
            ),
    	);
        $configs = $this->getSettingsField(true);
        $fields = array();
        $meta = Meta::getMetaByPage('module-'.$this->name.'-order',$this->context->language->id);
        if($meta && ($id_meta = $meta['id_meta']))
        {
            $meta_class = new Meta($id_meta);
        }
        else
        {
            $meta_class = new Meta();
        }
        foreach($languages as $language)
        {
            $fields['title'][$language['id_lang']] = Tools::getValue('title_'.$language['id_lang'],$meta_class->title[$language['id_lang']]);
            $fields['url_rewrite'][$language['id_lang']] = Tools::getValue('url_rewrite_'.$language['id_lang'],$meta_class->url_rewrite[$language['id_lang']]);
            $fields['description'][$language['id_lang']] = Tools::getValue('description_'.$language['id_lang'],$meta_class->description[$language['id_lang']]);
        }
        foreach($configs as $config)
        {
            $fields_form['form']['input'][] = $config;
            if($config['tab']!='seo')
            {
                if($config['type']!='checkbox' && $config['type']!='categories' && $config['type']!='tre_categories')
                {
                    if(isset($config['lang']) && $config['lang'])
                    {
                        foreach($languages as $language)
                        {
                            $fields[$config['name']][$language['id_lang']] = Tools::getValue($config['name'].'_'.$language['id_lang'],Configuration::get($config['name'],$language['id_lang']));
                        }
                        
                    }
                    else
                    {
                        $fields[$config['name']] = Tools::getValue($config['name'],Configuration::get($config['name']));
                    }    
                }
                else
                {
                    if($config['type']=='checkbox')
                        $fields[$config['name'].'_REQUIRED'] = Tools::isSubmit('saveConfig') ?  Tools::getValue($config['name'].'_REQUIRED') : explode(',',Configuration::get($config['name'].'_REQUIRED'));
                    $value = explode(',',Configuration::get($config['name']));
                    if($config['name'] =='ETS_OPC_CREATEACC_DISPLAY_FIELD' || $config['name'] =='ETS_OPC_GUEST_DISPLAY_FIELD')
                    {
                        if(Configuration::get('PS_CUSTOMER_OPTIN') && !in_array('optin',$value))
                            $value[] ='optin';
                        elseif(!Configuration::get('PS_CUSTOMER_OPTIN') && in_array('optin',$value))
                        {
                            foreach($value as $k=>$v)
                            {
                                if($v=='optin')
                                    unset($value[$k]);
                            }
                        }
                        if(Configuration::get('PSGDPR_CREATION_FORM_SWITCH') && !in_array('psgdpr',$value))
                            $value[] ='psgdpr';
                        elseif(!Configuration::get('PSGDPR_CREATION_FORM_SWITCH') && in_array('psgdpr',$value))
                        {
                            foreach($value as $k=>$v)
                            {
                                if($v=='psgdpr')
                                    unset($value[$k]);
                            }
                        } 
                    }
                    $fields[$config['name']] = Tools::isSubmit('saveConfig') ?  Tools::getValue($config['name']) : $value;
                }
            }
            
        }
        $helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = $this->table;
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$this->fields_form = array();
    	$helper->module = $this;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'saveConfig';
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
    	$helper->token = Tools::getAdminTokenLite('AdminModules');
    	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT')); 
        $link_calbacks = array();
        foreach(Language::getLanguages(true) as $l)
        {
            $link_calbacks[] = $this->context->link->getModuleLink($this->name,'callback',array(),null,$l['id_lang']);
        }           
        $helper->tpl_vars = array(
    		'base_url' => $this->context->shop->getBaseURL(),
    		'language' => array(
    			'id_lang' => $language->id,
    			'iso_code' => $language->iso_code
    		),
    		'fields_value' => $fields,
    		'languages' => $this->context->controller->getLanguages(),
    		'id_language' => $this->context->language->id,
            'isConfigForm' => true,
            'image_baseurl' => $this->_path.'views/img/',
            'link_calbacks' => $link_calbacks,
            'list_socials' => Configuration::get('ETS_OPC_POSITION_SOCIAL') ? explode(',',Configuration::get('ETS_OPC_POSITION_SOCIAL')): array('Paypal','Facebook','Google'),
        );
        return $helper->generateForm(array($fields_form));	
    }
    public function displayCarrierList()
    {
        if($carriers = Carrier::getCarriers($this->context->language->id,true))
        {
            foreach($carriers as &$carrier)
            {
                $carrier['url'] = $this->context->link->getAdminLink('AdminCarrierWizard').'&id_carrier='.$carrier['id_carrier'];
                if(file_exists(_PS_IMG_DIR_.'s/'.$carrier['id_carrier'].'.jpg'))
                    $carrier['logo'] = $this->context->link->getMediaLink(__PS_BASE_URI__.'img/s/'.$carrier['id_carrier'].'.jpg');
                if(!$carrier['name'])
                    $carrier['name'] = $this->context->shop->name;
            }
        }
        $this->context->smarty->assign(
            array(
                'carriers' => $carriers,
                'link'=> $this->context->link,
            )
        );
        return $this->display(__FILE__,'carriers.tpl');
    }  
    public function getPayments()
    {
        if($payments = Ets_opc_db::getModulePayments())
        {
            foreach($payments as &$payment)
            {
                $payment['url'] = $this->context->link->getAdminLink('AdminModules').'&configure='.$payment['name'];
                if(file_exists(_PS_MODULE_DIR_.$payment['name'].'/logo.png'))
                    $payment['logo'] = $this->context->link->getMediaLink(_MODULE_DIR_.$payment['name'].'/logo.png');
                $module = Module::getInstanceByName($payment['name']);
                $payment['module_name'] = $module->displayName;
                $payment['description'] = $module->description;
                $payment['link_disable'] = $this->getActionLinks($payment['name'],'disable');
                $payment['link_active'] = $this->getActionLinks($payment['name'],'enable');
                if(!method_exists($module,'getContent'))
                    $payment['setting']=0;
                else
                    $payment['setting']=1;
            }
        }
        return $payments;
    }  
    public function displayPaymentList()
    {
        $this->context->smarty->assign(
            array(
                'payments' => $this->getPayments(),
            )
        );
        return $this->display(__FILE__,'payments.tpl');
    }
    public static function getActionLinks($moduleName, $action, $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        try{
            $linkAction = $context->link->getAdminLink('AdminModulesManage', true,
            array(
                'route' => 'admin_module_manage_action',
                'module_name' => $moduleName,
                'action' => $action,
            ));
            if(strpos($linkAction, $action) !== false){
                return $linkAction;
            }
        }
        catch (Exception $ex){
            if($ex){
            //
            }
            return '';
        }
    }
    public function getLoginConfigs()
    {
        return array(
            'callback' => $this->context->link->getModuleLink($this->name, 'callback', array(), true),
            'providers' => array(
                'Google'=>array(
                    'enabled' => Configuration::get('ETS_OPC_GOOGLE_ENABLED') ? true :false,
                    'keys' => array(
                        'id' => Configuration::get('ETS_OPC_GOOGLE_ID'),
                        'secret' => Configuration::get('ETS_OPC_GOOGLE_SECRET'),
                        'key' => '',
                    ),
                ),
                'Facebook'=>array(
                    'enabled' => Configuration::get('ETS_OPC_FACEBOOK_ENABLED') ? true : false,
                    'keys' => array(
                        'id' => Configuration::get('ETS_OPC_FACEBOOK_ID'),
                        'secret' => Configuration::get('ETS_OPC_FACEBOOK_SECRET'),
                        'key' => '',
                    ),
                ),
                'Paypal'=>array(
                    'enabled' => Configuration::get('ETS_OPC_PAYPAL_ENABLED') ? true : false,
                    'keys' => array(
                        'id' => Configuration::get('ETS_OPC_PAYPAL_ID'),
                        'secret' => Configuration::get('ETS_OPC_PAYPAL_SECRET'),
                        'key' => '',
                    ),
                ), 
            )
        );
    }
    public function closePopup()
	{
		return $this->display(__FILE__, 'frontJs.tpl');
	}
    public function prepareDataToSave($profile)
	{
		if ($profile->firstName && $profile->lastName && Validate::isName($profile->firstName) && Validate::isName($profile->lastName)){
			return $profile;
		} elseif ($profile->firstName){
			$profile->lastName = $profile->firstName;
		} elseif ($profile->lastName){
			$profile->firstName = $profile->lastName;
		} elseif ($profile->displayName) {
			$profile->displayName = str_replace('+', '', $profile->displayName);
			$parts = explode(' ', trim($profile->displayName));
			$nameParts = array();
			foreach($parts as $part) {
				if (trim($part) == '') continue;
				$nameParts[] = $part;
			}
			if (count($nameParts) == 1) {
				$profile->firstName = $profile->lastName = $nameParts[0];
			} elseif (count($nameParts) > 1) {
				$profile->firstName = $nameParts[0];
				unset($nameParts[0]);
				$profile->lastName = implode(' ', $nameParts);
			}
		}
        if (!$profile->firstName || !\Validate::isName($profile->firstName))
            $profile->firstName = 'Unknown';
        if (!$profile->lastName || !\Validate::isName($profile->lastName))
            $profile->lastName = 'Unknown';

		return $profile;
	}
    public function createUser($profile, $provider)
	{
		if (!$profile) {
			die(json_encode(array('errors' => $this->displayError($this->l('Connecting API failed! Please check your account again.')))));
		}
		elseif ($provider)
		{
			$profile = $this->prepareDataToSave($profile);
			$customer = new Customer();
			$customer->id_shop = (int)$this->context->shop->id;
			$customer->lastname = $profile->lastName;
			$customer->firstname = $profile->firstName;
			$customer->email = $profile->email;
            $customer->newsletter =1;
            $customer->optin=1;
            $customer->newsletter_date_add = date('y-m-d H:i:s');
			$passwdGen = Tools::passwdGen(8);
			$customer->passwd = md5(_COOKIE_KEY_.$passwdGen);
			if ($customer->save())
			{
                $customer->updateGroup(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
                $this->context->updateCustomer($customer);
                Mail::Send(
                    $this->context->language->id,
                    'account',
                    $this->l('Your login password.'),
                    array(
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname,
                        '{customer_name}' =>$customer->firstname . ' ' . $customer->lastname,
                        '{customer_email}' => $customer->email,
                        '{auto_generated_password}' => $passwdGen,
                        '{link_change_password}' => $this->context->link->getPageLink('identity'),
                    ),
                    $customer->email,
                    $customer->firstname . ' ' . $customer->lastname,
                    null,
                    null,
                    null,
                    null,
                    dirname(__FILE__).'/mails/'
                );
                Hook::exec('actionCreateAccountBySocial', array('customer' => $customer));
                Hook::exec('actionAuthentication', array('customer' => $customer,'login_social'=>true));
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
			}
			else
                die(json_encode(array('errors' => $this->displayError($this->l('Creating account failed. Please check your account profile.')))));
		}
	}
    public function getCssExtraDesign()
    {
        $css_content = Tools::file_get_contents(dirname(__file__).'/views/css/color.css');
        $searchs = array('ETS_OPC_DESIGN_COLOR1','ETS_OPC_DESIGN_COLOR2','ETS_OPC_DESIGN_COLOR3','ETS_OPC_DESIGN_COLOR4','ETS_OPC_DESIGN_COLOR5','ETS_OPC_DESIGN_COLOR6','ETS_OPC_DESIGN_COLOR7','ETS_OPC_DESIGN_COLOR8','ETS_OPC_DESIGN_COLOR9');
        $repalces = array(
            Configuration::get('ETS_OPC_DESIGN_COLOR1') ? :'#0cb7e2',
            Configuration::get('ETS_OPC_DESIGN_COLOR2') ? :'#427e8d',
            Configuration::get('ETS_OPC_DESIGN_COLOR3') ? :'#b2ced2',
            Configuration::get('ETS_OPC_DESIGN_COLOR4') ? :'#2592a9',
            Configuration::get('ETS_OPC_DESIGN_COLOR5') ? :'#ffffff',
            Configuration::get('ETS_OPC_DESIGN_COLOR6') ? :'#ffffff',
            Configuration::get('ETS_OPC_DESIGN_COLOR7') ? :'#0cb8e2',
            Configuration::get('ETS_OPC_DESIGN_COLOR8') ? :'#427e8d',
            Configuration::get('ETS_OPC_DESIGN_COLOR9') ? :'#ecfbff',
        );
        $css_content =str_replace($searchs,$repalces,$css_content);
        return $css_content;
    }
    public function hookActionGetIDZoneByAddressID()
    {
        $id_state = (int)Tools::getValue('id_state');
        $id_country = (int)Tools::getValue('id_country');
        if($id_state && Validate::isUnsignedId($id_state))
        {
            if(($state = new State($id_state)) && Validate::isLoadedObject($state) && $state->active && $state->id_zone)
                return $state->id_zone;
        }
        if($id_country && Validate::isUnsignedId($id_country))
        {
            if(($country = new Country($id_country)) && Validate::isLoadedObject($country)  && $country->active && $country->id_zone)
                return $country->id_zone;
        }
    }
    public function hookTaxManager($params)
    {
        if(isset($params['address']) && isset($params['params']) && $address = $params['address'])
        {
            if(($id_country = (int)Tools::getValue('id_country')) && Validate::isUnsignedId($id_country))
                $address->id_country = $id_country;
            if(($id_state = (int)Tools::getValue('id_state')) && Validate::isUnsignedId($id_state))
                $address->id_state = $id_state;
            if(($postal_code= Tools::getValue('postal_code')) && Validate::isPostCode($postal_code))
                $address->postcode = $postal_code;
            return new TaxRulesTaxManager($address, $params['params']);
        }
        
    }
    public function validateFile($file_name,$file_size,&$errors,$file_types=array(),$max_file_size= false)
    {
        $file_name = str_replace(' ','_',$file_name);
        if($file_name)
        {
            if(!Validate::isFileName($file_name))
            {
                $errors[] = sprintf($this->l('The file name "%s" is invalid'),$file_name);
            }
            else
            {
                $type = Tools::strtolower(Tools::substr(strrchr($file_name, '.'), 1));
                if(!$file_types)
                    $file_types = $this->file_types;
                if(!in_array($type,$file_types))
                    $errors[] = sprintf($this->l('The file name "%s" is not in the correct format, accepted formats: %s'),$file_name,'.'.trim(implode(', .',$file_types),', .'));
                $max_file_size = $max_file_size ? : Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
                if($file_size > $max_file_size)
                    $errors[] = sprintf($this->l('The size of file "%s" is too large. Limit: %s'),$file_name,Tools::ps_round($max_file_size/1048576,2).'Mb');
            }
        }
    }
    public function uploadFile($files,$id_field,&$errors)
    {
        if(!is_dir(_PS_ETS_OPC_UPLOAD_DIR_))
            @mkdir(_PS_ETS_OPC_UPLOAD_DIR_,0777,true);
        if(isset($files['tmp_name'][$id_field]) && isset($files['name'][$id_field]) && $files['name'][$id_field])
        {
            $files['name'][$id_field] = Tools::strtolower(Tools::passwdGen(30));
            if (!move_uploaded_file($files['tmp_name'][$id_field], _PS_ETS_OPC_UPLOAD_DIR_.$files['name'][$id_field]))
				$errors[] = sprintf($this->l('Cannot upload the file %s'),$files['name'][$id_field]);
            if(!$errors)
                return $files['name'][$id_field];
            
        }
        return '';
    }
    public function hookDisplayOrderDetail($params)
    {
        if(isset($params['order']) && Validate::isLoadedObject($params['order']))
        {
            $order = $params['order'];
            if($additional_fields = Ets_opc_additionalinfo_field_value::getFieldValuesByIDCart($order->id_cart))
            {
                $this->context->smarty->assign(
                    array(
                        'additional_fields' => $additional_fields,
                        'link'=> $this->context->link,
                    )
                );
                return $this->display(__FILE__,'order-additional-detail.tpl');
            }
        }
    }
    public function hookDisplayAdminOrderLeft($params)
    {
        if(isset($params['id_order']) && ($order = new Order($params['id_order'])) && Validate::isLoadedObject($order))
        {
            if($additional_fields = Ets_opc_additionalinfo_field_value::getFieldValuesByIDCart($order->id_cart,$order->id_lang))
            {
                $this->context->smarty->assign(
                    array(
                        'additional_fields' => $additional_fields,
                        'id_order'=> $order->id,
                    )
                );
                return $this->display(__FILE__,'order-additional-detail-admin.tpl');
            }
        }
    }
    public function hookDisplayAdminOrderSide($params)
    {
        return $this->hookDisplayAdminOrderLeft($params);
    }
    public function getBaseLink()
    {
        $url =(Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
        return trim($url,'/');
    }
    public function getListFieldsAddress()
    {
        $fields = array(
           'firstname'=> array(
                'id'=>'firstname',
                'label' => $this->l('First name'),

            ),
           'lastname'=> array(
                'id'=>'lastname',
                'label' => $this->l('Last name'),
            ),
           'country'=> array(
                'id' => 'country', 
                'label' => $this->l('Country'),
            ),
          'state'=>  array(
                'id' => 'state', 
                'label' => $this->l('State'),
            ),
          'postcode'=>  array(
                'id'=>'post_code',
                'label' => $this->l('Zip code'),
            ),
           'city'=> array(
                'id'=>'city',
                'label' => $this->l('City'),
            ),
           'address'=> array(
                'id'=>'address',
                'label' => $this->l('Address'),
            ),
           'address2'=> array(
                'id'=>'address2',
                'label' => $this->l('Address complement')
            ),
            'phone'=>array(
                'id'=>'phone',
                'label' => $this->l('Phone'), 
            ),
            'phonemobile'=> array(
                'id'=>'phone_mobile',
                'label' => $this->l('Mobile phone'),
            ),
            'company' => array(
                'id'=>'company',
                'label' => $this->l('Company')
            ),
            'vatnumber' =>array(
                'id'=>'vat_number',
                'label' => $this->l('VAT number')
            ),
           'dni'=> array(
                'id'=>'dni',
                'label' => $this->l('DNI'), 
            ),
            'other' => array(
                'id'=>'other',
                'label' => $this->l('Other'), 
            ),
            'alias' =>array(
                'id'=>'alias',
                'label' => $this->l('Address alias'),
            ),
         );
         if(Configuration::get('ETS_OPC_POSITION_FIELDS_ADDRESS'))
         {
            $positions = explode(',',Configuration::get('ETS_OPC_POSITION_FIELDS_ADDRESS'));
         }
         else
            $positions = array('alias','firstname','lastname','company','address','address2','city','state','postcode','country','phone','phonemobile','vatnumber','dni','other');
         $address_fields = array();
         if($positions)
         {
            foreach($positions as $position)
            {
                if(isset($fields[$position]))
                    $address_fields[$position] = $fields[$position];
            }
         }
         else
            $address_fields = $fields;
        return $address_fields;
    }
    public function displayText($content=null,$tag,$class=null,$id=null,$href=null,$blank=false,$src = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
    {
        $this->smarty->assign(
            array(
                'content' =>$content,
                'tag' => $tag,
                'class'=> $class,
                'id' => $id,
                'href' => $href,
                'blank' => $blank,
                'src' => $src,
                'value' => $value,
                'type' => $type,
                'data_id_product' => $data_id_product,
                'attr_datas' => $attr_datas,
                'rel' => $rel,
            )
        );
        return $this->display(__FILE__,'html.tpl');
    }
    public static function isDomain($inputDomainName)
    {
        return (bool)preg_match('/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $inputDomainName);
    }
    public function hookDisplayCustomerLoginFormAfter()
    {
        $controller = Tools::getValue('controller');
        if(($ETS_OPC_PAGE_ENABLED_SOCIAL = Configuration::get('ETS_OPC_PAGE_ENABLED_SOCIAL')) && ($pages = explode(',',$ETS_OPC_PAGE_ENABLED_SOCIAL)))
        {
            if(in_array('login_page',$pages) && $controller=='authentication')
            {   
                return $this->displaySocialLogin();
            }
        }
    }
    public function hookDisplayCustomerAccountForm()
    {
        $controller = Tools::getValue('controller');
        if(($ETS_OPC_PAGE_ENABLED_SOCIAL = Configuration::get('ETS_OPC_PAGE_ENABLED_SOCIAL')) && ($pages = explode(',',$ETS_OPC_PAGE_ENABLED_SOCIAL)))
        {
            if( in_array('register_page',$pages)  && $controller=='authentication')
            {   
                return $this->displaySocialLogin();
            }
        }
    }
    public function displaySocialLogin()
    {
        $this->smarty->assign(
            array(
                'list_socials' => $this->getListSocialLogin(),
            )
        );
        return $this->display(__FILE__,'social.tpl');
    }
    public function getListSocialLogin()
    {
        $list_socials = Configuration::get('ETS_OPC_POSITION_SOCIAL') ? explode(',',Configuration::get('ETS_OPC_POSITION_SOCIAL')): array('Paypal','Facebook','Google');
        if($list_socials)
        {
            foreach($list_socials as $key=>$social)
            {
                if(!Configuration::get('ETS_OPC_'.Tools::strtoupper($social).'_ENABLED'))
                    unset($list_socials[$key]);
            }
        }
        return $list_socials;
    }
    public function hookActionAuthentication($params)
    {
        if(!isset($params['login_social']) && isset($params['customer']) && ($customer = $params['customer']) && Validate::isLoadedObject($customer))
        {
            Ets_opc_db::updateCustomerSocial($customer->id);
        }
    }
   
    public function hookActionCustomerGridQueryBuilderModifier($params)
    {
        $request = $this->getRequestContainer();
        if(($id_customer = (int)Tools::getValue('id_customer')) || ($request && ($id_customer = $request->get('cusotmerId'))))
            return $id_customer;     
        if(isset($params['search_query_builder']) && $params['search_query_builder'] && isset($params['count_query_builder']) && $params['count_query_builder'])
        {
            $searchQueryBuilder = &$params['search_query_builder'];
            $countQueryBuilder = &$params['count_query_builder'];
            $searchQueryBuilder->addSelect( 'social.registered_network as create_social')
            ->addSelect( 'social.last_login_network as login_social')
            ->leftJoin('c',_DB_PREFIX_.'ets_opc_login_social','social','social.id_customer = c.id_customer');
            $countQueryBuilder->leftJoin('c',_DB_PREFIX_.'ets_opc_login_social','social','social.id_customer = c.id_customer');
        }
    }
    public function getRequestContainer()
    {
        if ($this->is17)
        {
            if($sfContainer = $this->getSfContainer())
            {
                return $sfContainer->get('request_stack')->getCurrentRequest();
            }
        }
        return null;
    }
    public function getSfContainer()
    {
        if(!class_exists('\PrestaShop\PrestaShop\Adapter\SymfonyContainer'))
        {
            $kernel = null;
            try{
                $kernel = new AppKernel('prod', false);
                $kernel->boot();
                return $kernel->getContainer();
            }
            catch (Exception $ex){
                return null;
            }
        }
        $sfContainer = call_user_func(array('\PrestaShop\PrestaShop\Adapter\SymfonyContainer', 'getInstance'));
        return $sfContainer;
    }
    public function copy_directory($src, $dst)
    {
        if(!is_dir($src))
            return '';
        $dir = opendir($src);
        if(!file_exists($dst))
            @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } elseif(!file_exists($dst . '/' . $file)) {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    public function rrmdir($dir)
    {
        $dir = rtrim($dir, '/');
        if ($dir && is_dir($dir)) {
            if ($objects = scandir($dir)) {
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object))
                            $this->rrmdir($dir . "/" . $object);
                        elseif(file_exists($dir . "/" . $object))
                            @unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
    public function enable($force_all = false)
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $class= 'Ets_opc_overrideUtil';
        $method = 'resolveConflict';
        call_user_func_array(array($class, $method),array($this));
        return parent::enable($force_all);
    }
    public function disable($force_all = false)
    {
        require_once(dirname(__FILE__) . '/classes/OverrideUtil');
        $parentResult = parent::disable($force_all);
        $class= 'Ets_opc_overrideUtil';
        $method = 'restoreReplacedMethod';
        call_user_func_array(array($class, $method),array($this));
        return $parentResult;
    }
}