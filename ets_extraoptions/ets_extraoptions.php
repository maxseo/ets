<?php
/**
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
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))  
    exit;
require_once(dirname(__FILE__) . '/classes/ets_eto_defines.php');
require_once(dirname(__FILE__) . '/classes/ets_eto_paggination_class.php');
require_once(dirname(__FILE__) . '/classes/ets_eto_combination_class.php');
require_once(dirname(__FILE__) . '/classes/ets_eto_attribute_class.php');
require_once(dirname(__FILE__) . '/classes/ets_eto_attribute_cart_class.php');
require_once(dirname(__FILE__) . '/classes/ets_eto_attribute_order_class.php');
require_once(dirname(__FILE__) . '/classes/ets_eto_attribute_group_class.php');
class Ets_extraoptions extends Module
{
    public $_errors = array();
    public $is17 = false;
    public $hooks_display = array();
    public $partialRenderer= null;
    public function __construct()
    {
        $this->name = 'ets_extraoptions';
        $this->tab = 'front_office_features';
        $this->version = '1.0.7';
        $this->author = 'ETS-Soft';
        $this->need_instance = 0;
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        parent::__construct();
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->module_dir = $this->_path;
        $this->displayName = $this->l('Extra options');
        $this->description = $this->l('Allow you to create global options that apply for all existing products and price impact for each option. Faster and easier to manage than PrestaShop default combinations');
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
		$this->module_key = 'bde0ab61507ce6941600f358ed7ea0de';
    }
    public function install()
    {
        if(Module::isInstalled('ets_payment_with_fee')){
            throw new PrestaShopException($this->l("The module Payment With Fee has been installed"));
        }
        return parent::install() && Ets_eto_defines::getInstance()->_installDb() && $this->_installHooks() && $this->_installDefaultConfig();
    }
    public function unInstall()
    {
        return parent::unInstall() && Ets_eto_defines::getInstance()->_uninstallDb() && $this->_unInstallHooks() && $this->_unInstallDefaultConfig();
    }
    public function _installDefaultConfig()
    {
        $inputs = $this->getConfigInputs();
        $languages = Language::getLanguages(false);
        if($inputs)
        {
            foreach($inputs as $input)
            {
                if(isset($input['default']) && $input['default'])
                {
                    if(isset($input['lang']) && $input['lang'])
                    {
                        $values = array();
                        foreach($languages as $language)
                        {
                            $values[$language['id_lang']] = isset($input['default_lang']) && $input['default_lang'] ? $this->getTextLang($input['default_lang'],$language) : $input['default'];
                        }
                        Configuration::updateGlobalValue($input['name'],$values);
                    }
                    else
                        Configuration::updateGlobalValue($input['name'],$input['default']);
                }
            }
        }
        return true;
    }
    public function _unInstallDefaultConfig()
    {
        $inputs = $this->getConfigInputs();
        if($inputs)
        {
            foreach($inputs as $input)
            {
                Configuration::deleteByName($input['name']);
            }
        }
        return true;          
    }
    public function _installHooks()
    {
        if(!$this->is17)
            $this->registerHook('displayAdminProductsExtra');
        return $this->registerHook('displayBackOfficeHeader')
        && $this->registerHook('displayHeader')
        && $this->registerHook('actionValidateOrder')
        && $this->registerHook('displayAdminProductsExtraCustomAttribute')
        && $this->registerHook('displayProductPriceBlock')
        && $this->registerHook('displayOverrideTemplate')
        && $this->registerHook('actionObjectProductInCartDeleteBefore')
        && $this->registerHook('actionEmailSendBefore')
        && $this->registerHook('actionProductSave')
        && $this->registerHook('actionProductDelete')
        && $this->registerHook('actionProductAdd');
    }
    public function _unInstallHooks()
    {
        if(!$this->is17)
            $this->unregisterHook('displayAdminProductsExtra');
        return $this->unregisterHook('displayBackOfficeHeader')
        && $this->unregisterHook('displayHeader')
        && $this->unregisterHook('actionValidateOrder')
        && $this->unregisterHook('displayAdminProductsExtraCustomAttribute')
        && $this->unregisterHook('displayProductPriceBlock')
        && $this->unregisterHook('displayOverrideTemplate')
        && $this->unregisterHook('actionObjectProductInCartDeleteBefore')
        && $this->unregisterHook('actionEmailSendBefore')
        && $this->unregisterHook('actionProductSave')
        && $this->unregisterHook('actionProductDelete')
        && $this->unregisterHook('actionProductAdd');
    }
    public function getRequestContainer()
    {
        if($sfContainer = $this->getSfContainer())
        {
            return $sfContainer->get('request_stack')->getCurrentRequest();
        }
        return null;
    }
    public function getSfContainer()
    {
        if($this->is17)
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
        return null;
    }
    public function addTwigVar($key, $value)
    {
        if($sfContainer = $this->getSfContainer())
        {
            $sfContainer->get('twig')->addGlobal($key, $value);
        }
    }
    public function hookDisplayHeader()
    {
        //$this->context->controller->addJS(_PS_JS_DIR_.'jquery/ui/jquery.ui.tooltip.min.js');
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front_product.css');
        $this->context->controller->addCSS(_PS_JS_DIR_.'jquery/ui/themes/ui-lightness/jquery.ui.tooltip.css');
    }
    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');
        $configure = Tools::getValue('configure'); 
        if($controller=='AdminModules' && $configure== $this->name)
        {
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
            if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.7.0', '<'))
                $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery-'._PS_JQUERY_VERSION_.'.min.js');
            else
                $this->context->controller->addJquery();
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJs($this->_path.'views/js/admin.js');
            
        }
        if($controller=='AdminProducts')
        {
            if($request = $this->getRequestContainer())
            {
                $id_product = $request->get('id');
            }
            else
                $id_product = (int)Tools::getValue('id_product');
            if(Tools::isSubmit('updateCustomAttribute') && $id_product && ($product= new Product($id_product)) && Validate::isLoadedObject($product))
            {
                $this->updateCustomAttribute($id_product);
            }
            
            if($id_product)
            {
                if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.7.0', '<'))
                    $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery-'._PS_JQUERY_VERSION_.'.min.js');
                else
                    $this->context->controller->addJquery();
                $this->context->controller->addJqueryUI('ui.sortable');
                $this->context->controller->addJS($this->_path.'views/js/product.js');
                $this->context->controller->addCSS($this->_path.'views/css/product.css');
                $this->addTwigVar('ets_extraoptions_product',true);
                $this->addTwigVar('ets_extraoptions_product_text',$this->l('Extra options'));
            }
            
        }
        if(($controller=='AdminModules' && $configure== $this->name) || ($controller=='AdminProducts' && $id_product))
        {
            $this->context->controller->addCSS($this->_path.'views/css/popup.css');
            $this->context->controller->addJs($this->_path.'views/js/popup.js');
        }
    }
    public function updateCustomAttribute($id_product)
    {
        $use_attribute = Tools::getValue('use_attribute');
        $price_attribute = Tools::getValue('price_attribute');
        $required_attribute = Tools::getValue('required_attribute');
        $checked_default_attribute = Tools::getValue('checked_default_attribute');
        $price_attribute_custom = Tools::getValue('price_attribute_custom');
        $use_tax_attribute = Tools::getValue('use_tax_attribute');
        $use_discount_attribute = Tools::getValue('use_discount_attribute');
        $errors = array();
        if($use_attribute && is_array($use_attribute) && self::validateArray($use_attribute) && self::validateArray($price_attribute) && self::validateArray($required_attribute) && self::validateArray($checked_default_attribute) && self::validateArray($price_attribute_custom) && self::validateArray($use_tax_attribute) && self::validateArray($use_discount_attribute))
        {
            foreach($use_attribute  as $id_attribute=>$used)
            {
                $attribute = new Ets_eto_attribute_class($id_attribute,$this->context->language->id);
                if(isset($price_attribute[$id_attribute]) && $price_attribute[$id_attribute]!='default')
                {
                    if(isset($price_attribute_custom[$id_attribute]) && $price_attribute_custom[$id_attribute] && !Validate::isPrice($price_attribute_custom[$id_attribute]))
                        $errors[] = sprintf($this->l('Price of the custom option %s%s%s is not valid'),'"',$attribute->name,'"');
                }
            }
        }
        else
            $errors[] = $this->l('Error occurred: Data post is not valid');
        if(!$errors)
        {
            $position =1;
            foreach($use_attribute  as $id_attribute=>$used)
            {
                $price = isset($price_attribute[$id_attribute]) ? $price_attribute[$id_attribute] :'default';
                $required = isset($required_attribute[$id_attribute]) ? $required_attribute[$id_attribute]:-1;
                $checked = isset($checked_default_attribute[$id_attribute]) ? $checked_default_attribute[$id_attribute]:-1;
                $use_tax = isset($use_tax_attribute[$id_attribute]) ? $use_tax_attribute[$id_attribute] :-1;
                $use_discount = isset($use_discount_attribute[$id_attribute]) ? $use_discount_attribute[$id_attribute] :-1;
                if($price=='default')
                {
                    $price =-1;
                }
                else
                {
                    $price = isset($price_attribute_custom[$id_attribute]) ? $price_attribute_custom[$id_attribute]:0;
                }
                $data = array(
                    'id_ets_eto_attr' => $id_attribute,
                    'id_product' => $id_product,
                    'used' => $used,
                    'price' => $price,
                    'use_tax' => $use_tax,
                    'use_discount'=> $use_discount,
                    'required' => $required,
                    'checked' => $checked,
                    'position' => $position,
                );
                Ets_eto_attribute_class::updateCustomAttribute($data);
                $position++;
            }
            if(Tools::isSubmit('updateCustomAttribute'))
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'success'=> $this->l('Settings updated.'),
                        )
                    )
                );
            }
            
        }
        else
        {
            die(
                Tools::jsonEncode(
                    array(
                        'errors' => $this->displayError($errors)
                    )
                )
            );
        }
    }
    public function checkShowTax()
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
        return $group->price_display_method ? false : true;
    }
    public function addTaxToPrice($id_tax_group,$price)
    {
        if($id_tax_group)
        {
            $context = $this->context;
            if (is_object($context->cart) && $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} != null) {
                $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                $address = new Address($id_address);
            } else {
                $address = new Address();
            }
            $address = Address::initialize($address->id,true);
            $tax_manager = TaxManagerFactory::getManager($address, $id_tax_group);
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $priceTaxIncl = $product_tax_calculator->addTaxes($price);
            return $priceTaxIncl;
        }
        return $price;
    }
    public function hookDisplayProductPriceBlock($params)
    {
        $controller = Tools::getValue('controller');
        if($controller=='product' && ($id_product = (int)Tools::getValue('id_product')) && isset($params['type']) && $params['type']=='weight' && isset($params['product']) && ($product = $params['product']) && $product['id_product'] == $id_product)
        {
            $attribute_groups = Ets_eto_attribute_group_class::getListAttributeGroups();
            $attribute_groups[] = array(
                'id_ets_eto_attr_group' => 0,
                'name' =>  $this->l('Other options'),
            );
            if($attribute_groups)
            {
                foreach($attribute_groups as $index=>$group)
                {
                    $attributes = Ets_eto_attribute_class::getAttributesByProduct($id_product,true,' AND a.id_product=0 AND a.id_ets_eto_attr_group='.(int)$group['id_ets_eto_attr_group']);
                    $specific_attributes = Ets_eto_attribute_class::getAttributesByProduct($id_product,true,' AND a.id_product="'.(int)$id_product.'" AND a.id_ets_eto_attr_group='.(int)$group['id_ets_eto_attr_group']);
                    $attributes = array_merge($attributes,$specific_attributes);
                    if($attributes)
                    {
                        foreach($attributes as &$attribute)
                        {
                            if($attribute['product_use_tax']!=-1)
                                $attribute['use_tax'] = $attribute['product_use_tax'];
                            if($attribute['product_use_discount']!=-1)
                                $attribute['use_discount'] = $attribute['product_use_discount'];
                            if($attribute['product_price']!=-1)
                                $attribute['price'] = $attribute['product_price'];
                            if($attribute['product_required']!=-1)
                                $attribute['required'] = $attribute['product_required'];
                            if($attribute['product_checked']!=-1)
                                $attribute['checked'] = $attribute['product_checked'];
                            if($attribute['price']!=0 && $attribute['use_discount'] && (isset($product->specific_prices)) && ($specific_prices = $product->specific_prices) && $specific_prices['reduction_type']=='percentage' && ($reduction = $specific_prices['reduction']))
                            {
                                $attribute['price_without_reduction'] = Tools::convertPrice($attribute['price']);
                                $attribute['price'] -= $attribute['price']*$reduction;
                                $attribute['reduction'] = Tools::ps_round($reduction*100,2);
                            }
                            if($attribute['price']!=0 && $attribute['use_tax'] && isset($product->id_tax_rules_group) && $product->id_tax_rules_group && $this->checkShowTax())
                            {
                                $attribute['price'] = $this->addTaxToPrice($product->id_tax_rules_group, $attribute['price']);
                                if(isset($attribute['price_without_reduction']))
                                    $attribute['price_without_reduction'] = $this->addTaxToPrice($product->id_tax_rules_group, $attribute['price_without_reduction']);
                            }
                            if(isset($attribute['price_without_reduction']))
                                $attribute['price_without_reduction'] = Tools::displayPrice($attribute['price_without_reduction']);
                            if($attribute['price']!=0)
                                $attribute['price_text'] = Tools::displayPrice(Tools::convertPrice($attribute['price']));
                            else
                                $attribute['price_text'] = $this->l('Free');
                        }
                        $attribute_groups[$index]['attributes'] = $attributes;
                    }
                    else
                        unset($attribute_groups[$index]);
                }
            }
            $this->smarty->assign(
                array(
                    'attribute_groups' => $attribute_groups,
                    'attribute_title' => Configuration::get('ETS_ETO_ATTRIBUTE_TITLE',$this->context->language->id) ?: $this->l('Extra options'),
                    'attribute_total_title' => Configuration::get('ETS_ETO_TOTAL_ATTRIBUTE_TITLE',$this->context->language->id) ?: $this->l('Total option price'),
                    'ETS_ETO_CHECKBOX_BACKGROUD_COLOR' => Configuration::get('ETS_ETO_CHECKBOX_BACKGROUD_COLOR'),
                )
            );
            return $this->display(__FILE__,'product_attribute.tpl');
        }
    }
    public function hookDisplayAdminProductsExtraCustomAttribute($params)
    {
        if(isset($params['id_product']) && $params['id_product'])
        {
            $global_groups = Ets_eto_attribute_group_class::getListGroupHasAttribute(0);
            if($global_groups)
            {
                foreach($global_groups as &$group)
                {
                    $group['attributes'] = Ets_eto_attribute_class::getAttributesByProduct($params['id_product'],false,' AND a.id_product=0 AND a.id_ets_eto_attr_group='.(int)$group['id_ets_eto_attr_group']);
                }
            }
            $this->smarty->assign(
                array(
                    'global_groups' => $global_groups,
                    'list_specific_attributes' => Ets_eto_attribute_class::getInstance()->renderProductAttributes($params['id_product']),
                    'id_product' => $params['id_product'],
                    'link_add_new' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&getFromNewSpecificAttribute&id_product='.(int)$params['id_product'],
                )
            );
            return $this->display(__FILE__,'product_extra.tpl');
        }
        
    }
    public function hookActionValidateOrder($params)
    {
        if(isset($params['order']) && $params['order'] && isset($params['cart']) && $params['cart'])
        {
            Ets_eto_attribute_order_class::actionValidateOrder($params);
        }
    }
    public function initContentHTMLTemplateInvoice($id_order, $smarty)
    {
        $order = new Order($id_order);
        if (($customPrice = Ets_eto_attribute_order_class::getCustomPrice($order))!==false)
        {
            $smarty->assign(
                array(
                    'custom_attribute_price' => $customPrice,
                    'custom_attribute_price_text' => $this->l('Custom option'),
                )
            );
            $smarty->assign(
                array(
                    'total_tab' => $smarty->fetch(_PS_MODULE_DIR_ . 'ets_extraoptions/views/templates/hook/invoice.total-tab.tpl'),
                )
            );
        }
    }
    public function getContent()
    {
        $this->_html = '';
        if(Tools::isSubmit('editca_attribute_group') && ($id_group = (int)Tools::getValue('id_ets_eto_attr_group')))
        {
            $attributeGroup = new Ets_eto_attribute_group_class($id_group);
            if(Tools::isSubmit('ajax'))
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'html_form' => $attributeGroup->renderFormAttributeGroup(),
                        )
                    )
                );  
            }
            return $attributeGroup->renderFormAttributeGroup();
        }
        if(Tools::isSubmit('del'))
        {
            if(($id_ets_eto_attr = (int)Tools::getValue('id_ets_eto_attr')))
                $Obj = new Ets_eto_attribute_class($id_ets_eto_attr);
            elseif($id_group = (int)Tools::getValue('id_ets_eto_attr_group'))
                $Obj = new Ets_eto_attribute_group_class($id_group);
            if(isset($Obj) && Validate::isLoadedObject($Obj))
            {
                if($Obj->delete())
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'success' => $this->l('Deleted successfully'),
                            )
                        )
                    );
                }
            }
            die(
                Tools::jsonEncode(
                    array(
                        'errors' => $this->l('An error occurred while deleting the option'),
                    )
                )
            );
        }
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_errors)) {
                $inputs = $this->getConfigInputs();
                $languages = Language::getLanguages(false);
                $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
                foreach($inputs as $input)
                {
                    if(isset($input['lang']) && $input['lang'])
                    {
                        $values = array();
                        foreach($languages as $language)
                        {
                            $value_default = Tools::getValue($input['name'].'_'.$id_lang_default);
                            $value = Tools::getValue($input['name'].'_'.$language['id_lang']);
                            $values[$language['id_lang']] = ($value && Validate::isCleanHtml($value)) || !isset($input['required']) ? $value : (Validate::isCleanHtml($value_default) ? $value_default :'');
                        }
                        Configuration::updateValue($input['name'],$values);
                    }
                    else
                    {
                        $val = Tools::getValue($input['name']);
                        if(Validate::isCleanHtml($val))
                            Configuration::updateValue($input['name'],$val);
                    }
                }
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $this->_html .= $this->displayError($this->_errors);
            }
        }
        if(Tools::isSubmit('saveAttribute'))
        {
            $this->saveAttribute();
        }
        if(Tools::isSubmit('saveAttributeGroup'))
        {
            $this->saveAttributeGroup();
        }
        if((Tools::isSubmit('editca_attribute') || Tools::isSubmit('editca_specific_attribute')) && ($id_attribute = (int)Tools::getValue('id_ets_eto_attr')))
        {
            $attribute = new Ets_eto_attribute_class($id_attribute);
            if(Tools::isSubmit('ajax'))
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'html_form' => $attribute->renderFormAttribute(),
                        )
                    )
                );  
            }
            return $attribute->renderFormAttribute();
        }
        $action = Tools::getValue('action');
        if($action=='updateAttributeOrdering' && ($attributes = Tools::getValue('ca_attribute')) && Ets_extraoptions::validateArray($attributes,'isInt'))
        {
            Ets_eto_attribute_class::getInstance()->_updateAttributesOrdering($attributes);
        }
        if($action=='updateAttributeOrdering' && ($attributes = Tools::getValue('ca_specific_attribute')) && Ets_extraoptions::validateArray($attributes,'isInt'))
        {
            Ets_eto_attribute_class::getInstance()->_updateAttributesOrdering($attributes);
        }
        if($action=='updateAttributeOrdering' && ($attributeGroups = Tools::getValue('ca_attribute_group')) && Ets_extraoptions::validateArray($attributeGroups,'isInt'))
        {
            Ets_eto_attribute_group_class::getInstance()->_updateOrdering($attributeGroups);
        }
        if(Tools::isSubmit('change_enabled') && ($id_attribute = (int)Tools::getValue('id_ets_eto_attr')) && ($field = Tools::getValue('field')) && in_array($field,array('used','required','checked','active','use_tax','use_discount')) )
        {
            $this->_submitChangeStatus($id_attribute,$field);
        }
        if(Tools::isSubmit('getFromNewSpecificAttribute')&& ($id_product= (int)Tools::getValue('id_product')))
        {
            die(
                Tools::jsonEncode(
                    array(
                        'html_form' => Ets_eto_attribute_class::getInstance()->renderFormAttribute($id_product),
                    )
                )
            );
        }
        $this->_html .= $this->renderForm();
        $tab_active = Tools::getValue('tab_active','group');
        if($tab_active!='global' && $tab_active!='specific' && $tab_active!='group')
            $tab_active = 'group';
        $this->_html .= $this->displayTabs($tab_active);
        if($tab_active=='group')
            $this->_html .= Ets_eto_attribute_group_class::getInstance()->renderGroupAttributes();
        elseif($tab_active=='global')
            $this->_html .= Ets_eto_attribute_class::getInstance()->renderAttributes();
        else
            $this->_html .= Ets_eto_attribute_class::getInstance()->renderProductAttributes();
        return  $this->_html;
    }
    public function displayTabs($tab_active)
    {
        $this->smarty->assign(
            array(
                'tab_active' => $tab_active,
                'total_specific_attributes' => Ets_eto_attribute_class::getInstance()->getAttributes(' AND a.id_product!=0',0,0,'',true),
                'total_global_attributes' => Ets_eto_attribute_class::getInstance()->getAttributes(' AND a.id_product=0',0,0,'',true),
                'link' => $this->context->link,
            )
        );
        return $this->display(__FILE__,'tabs.tpl');
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
            return false;
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
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public function _postValidation()
    {
        $languages = Language::getLanguages(false);
        $inputs = $this->getConfigInputs();
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        foreach($inputs as $input)
        {
            if(isset($input['lang']) && $input['lang'])
            {
                if(isset($input['required']) && $input['required'])
                {
                    $val_default = Tools::getValue($input['name'].'_'.$id_lang_default);
                    if(!$val_default)
                    {
                        $this->_errors[] = sprintf($this->l('%s is required'),$input['label']);
                    }
                    elseif($val_default && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate) && !Validate::{$validate}($val_default))
                        $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    elseif($val_default && !Validate::isCleanHtml($val_default))
                        $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                    else
                    {
                        foreach($languages as $language)
                        {
                            if(($value = Tools::getValue($input['name'].'_'.$language['id_lang'])) && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate)  && !Validate::{$validate}($value))
                                $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                            elseif($value && !Validate::isCleanHtml($value))
                                $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                        }
                    }
                }
                else
                {
                    foreach($languages as $language)
                    {
                        if(($value = Tools::getValue($input['name'].'_'.$language['id_lang'])) && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate)  && !Validate::{$validate}($value))
                            $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                        elseif($value && !Validate::isCleanHtml($value))
                            $this->_errors[] = sprintf($this->l('%s is not valid in %s'),$input['label'],$language['iso_code']);
                    }
                }
            }
            else
            {
                $val = Tools::getValue($input['name']);
                if($val===''&& isset($input['required']))
                {
                    $this->_errors[] = sprintf($this->l('%s is required'),$input['label']);
                }
                if($val!=='' && isset($input['validate']) && ($validate = $input['validate']) && method_exists('Validate',$validate) && !Validate::{$validate}($val))
                {
                    $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                }
                elseif($val!='' && isset($input['validate']) && $input['validate']=='isEtsColor' && !self::isColor($val))
                {
                    $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
                }
                elseif($val!==''&& !Validate::isCleanHtml($val))
                    $this->_errors[] = sprintf($this->l('%s is not valid'),$input['label']);
            }
        }
    }
    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6})$/', $color);
    }
    public function getConfigInputs()
    {
        return array(
            array(
                'type' => 'text',
                'label' => $this->l('Option section title'),
                'desc' => $this->l('Title of the option section on the product detail page'),
                'lang' => true,
                'name' => 'ETS_ETO_ATTRIBUTE_TITLE',
                'placeholder' => $this->l('Option title'),
                'validate' =>'isCleanHtml',
                'default' => $this->l('Extra options'),
                'default_lang' => 'Extra options',
                'required' => true,
            ),
            array(
                'type' => 'text',
                'label' => $this->l('"Total option price" label'),
                'desc' => $this->l('The text displays on the product detail page and notifies customers about the total price for their selected options. Example: "Total price for your selected options"'),
                'lang' => true,
                'name' => 'ETS_ETO_TOTAL_ATTRIBUTE_TITLE',
                'placeholder' => $this->l('Total option'),
                'default' => $this->l('Total option price'),
                'default_lang' => 'Total option price',
                'required' => true,
                'validate' =>'isCleanHtml'
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Option details'),
                'desc' => $this->l('Title of the selected option list will be displayed on shopping cart page, order confirmation page, order detail and an email sent to customers.'),
                'lang' => true,
                'name' => 'ETS_ETO_DETAIL_ATTRIBUTE_TITLE',
                'validate' =>'isCleanHtml',
                'default' => $this->l('Extra options'),
                'default_lang' => 'Extra options',
                'required' => true,
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Checkbox background color'),
                'name' => 'ETS_ETO_CHECKBOX_BACKGROUD_COLOR',
                'default' => '#2fb5d2',
                'validate'=> 'isEtsColor',
            ),
        );
    }
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cog'
                ),
                'input' => $this->getConfigInputs(),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = $this->id;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $language->id;
        $helper->override_folder ='/';
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
            'link' => $this->context->link,
        );
        $this->fields_form = array();
        return $helper->generateForm(array($fields_form));
    }
    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();
        $inputs = $this->getConfigInputs();
        if($inputs)
        {
            foreach($inputs as $input)
            {
                if(!isset($input['lang']))
                {
                    $fields[$input['name']] = Tools::getValue($input['name'],Configuration::get($input['name']));
                }
                else
                {
                    foreach($languages as $language)
                    {
                        $fields[$input['name']][$language['id_lang']] = Tools::getValue($input['name'].'_'.$language['id_lang'],Configuration::get($input['name'],$language['id_lang']));
                    }
                }
            }
        }
        return $fields;
    }
    public function renderList($listData)
    { 
        if(isset($listData['fields_list']) && $listData['fields_list'])
        {
            foreach($listData['fields_list'] as $key => &$val)
            {
                if(isset($val['filter']) && $val['filter'] && ($val['type']=='int' || $val['type']=='date'))
                {
                    if(Tools::isSubmit('ets_eto_submit_'.$listData['name']))
                    {
                        $value_max = trim(Tools::getValue($key.'_max'));
                        $value_min = trim(Tools::getValue($key.'_min'));
                        $val['active']['max'] =  Validate::isCleanHtml($value_max) ? $value_max :'';   
                        $val['active']['min'] =  Validate::isCleanHtml($value_min) ? $value_min :''; 
                    }
                    else
                    {
                        $val['active']['max']='';
                        $val['active']['min']='';
                    }  
                }  
                elseif(!Tools::isSubmit('del') && Tools::isSubmit('ets_eto_submit_'.$listData['name']))               
                {
                    $value = trim(Tools::getValue($key));
                    $val['active'] = Validate::isCleanHtml($value) ? $value :'';
                }
                else
                    $val['active']='';
            }
        }    
        $this->smarty->assign($listData);
        return $this->display(__FILE__, 'list_helper.tpl');
    }
    public function getFilterParams($field_list,$table='')
    {
        $params = '';        
        if($field_list)
        {
            if(Tools::isSubmit('ets_eto_submit_'.$table))
                $params .='&ets_eto_submit_'.$table.='=1';
            foreach($field_list as $key => $val)
            {
                $value = Tools::getValue($key);
                $value_min = Tools::getValue($key.'_min');
                $value_max = Tools::getValue($key.'_max');
                if($value!='' && Validate::isCleanHtml($value))
                {
                    $params .= '&'.$key.'='.urlencode($value);
                }
                if($value_max!='' && Validate::isCleanHtml($value_max))
                {
                    $params .= '&'.$key.'_max='.urlencode($value_max);
                }
                if($value_min !='' && Validate::isCleanHtml($value_min))
                {
                    $params .= '&'.$key.'_min='.urlencode($value_min);
                } 
            }
            unset($val);
        }
        return $params;
    }
    public function saveAttribute(){
        if($id_attribute = (int)Tools::getValue('id_attribute'))
            $attribute = new Ets_eto_attribute_class($id_attribute);
        else
        {
            $attribute = new Ets_eto_attribute_class();
            $attribute->id_shop = $this->context->shop->id;
        }
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $errors = array();
        $languages = Language::getLanguages();
        $name_default = Tools::getValue('name_'.$id_lang_default);
        if($name_default=='')
            $errors[] = $this->l('Name is required');
        if($name_default && !Validate::isGenericName($name_default))
            $errors[] = $this->l('Name is not valid');
        $desc_default = Tools::getValue('description_'.$id_lang_default);
        if($desc_default && !Validate::isCleanHtml($desc_default))
            $errors[] = $this->l('Description is not valid');
        $display_by_option_group = (int)Tools::getValue('display_by_option_group');
        $id_ets_eto_attr_group = (int)Tools::getValue('id_ets_eto_attr_group');
        if($display_by_option_group && !$id_ets_eto_attr_group)
            $errors[] = $this->l('Option group is required');
        foreach($languages as $language)
        {
            $id_lang = $language['id_lang'];
            if($id_lang!= $id_lang_default)
            {
                $name = Tools::getValue('name_'.$id_lang);
                if($name && !Validate::isGenericName($name))
                    $errors[] = sprintf($this->l('Name is not valid in %s'),$language['iso_code']);
                else
                    $attribute->name[$id_lang] = $name ? : $name_default;
                $desc = Tools::getValue('description_'.$id_lang);
                if($desc && !Validate::isCleanHtml($desc))
                    $errors[] = sprintf($this->l('Description is not valid in %s'),$language['iso_code']);
                else
                    $attribute->description[$id_lang] = $desc ? : $desc_default;
            }
            else
            {
                $attribute->name[$id_lang_default] = $name_default;
                $attribute->description[$id_lang_default] = $desc_default;
            }
        }
        $price = Tools::getValue('price');
        if($price!=='' && !Validate::isPrice($price))
            $errors[] = $this->l('Price is not valid');
        $used = (int)Tools::getValue('used');
        $required = (int)Tools::getValue('required');
        $checked = (int)Tools::getValue('checked');
        $active = (int)Tools::getValue('active');
        $id_product = (int)Tools::getValue('id_product');
        $use_tax = (int)Tools::getValue('use_tax');
        $use_discount = (int)Tools::getValue('use_discount');
        if(!$errors)
        {
            $attribute->used = $used;
            $attribute->required = $required;
            $attribute->price = $price;
            $attribute->checked = $checked;
            $attribute->active = $active;
            $attribute->id_product = $id_product;
            $attribute->use_discount = $use_discount;
            $attribute->use_tax = $use_tax;
            $attribute->display_by_option_group = $display_by_option_group;
            if($display_by_option_group)
                $attribute->id_ets_eto_attr_group = $id_ets_eto_attr_group;
            else
                $attribute->id_ets_eto_attr_group = 0;
            if($attribute->id)
            {
                if($attribute->update())
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'success' => $this->l('Successfully updated.'),
                                'list_attributes' =>$attribute->id_product ? Ets_eto_attribute_class::getInstance()->renderProductAttributes(Tools::isSubmit('productPage') ? $attribute->id_product:0):Ets_eto_attribute_class::getInstance()->renderAttributes(),
                                'specific' => $id_product ? true : false,
                            )
                        )
                    );
                }
                else
                    $errors[] = $this->l('An error occurred while saving the option');
            }
            else
            {
                if($attribute->add())
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'success' => $this->l('Created successfully'),
                                'list_attributes' =>$attribute->id_product ? Ets_eto_attribute_class::getInstance()->renderProductAttributes(Tools::isSubmit('productPage') ? $attribute->id_product:0): Ets_eto_attribute_class::getInstance()->renderAttributes(),
                                'specific' => $id_product ? true : false,
                            )
                        )
                    );
                }
                else
                    $errors[] = $this->l('An error occurred while saving the option');
            }
        }
        if($errors)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
        }
    }
    public function displayText($content=null,$tag,$class=null,$id=null,$href=null,$blank=false,$src = null,$name = null,$value = null,$type = null,$data_id_product = null,$rel = null,$attr_datas=null)
    {
        $this->smarty->assign(
            array(
                'content' =>$content,
                'tag' => $tag,
                'text_class'=> $class,
                'id' => $id,
                'href' => $href,
                'blank' => $blank,
                'src' => $src,
                'name' => $name,
                'value' => $value,
                'type' => $type,
                'data_id_product' => $data_id_product,
                'attr_datas' => $attr_datas,
                'rel' => $rel,
            )
        );
        return $this->display(__FILE__,'html.tpl');
    }
    public function validateAddCustomAttributeToCart(&$errors,$id_product)
    {
        if(Tools::isSubmit('add'))
        {
            $attributes = Tools::getValue('ets-ca-custom-attribute',array());
            if(is_array($attributes) && self::validateArray($attributes))
            {
                $required_attributes = Ets_eto_attribute_class::getAttributesByProduct($id_product,true,' AND (a.id_product="'.(int)$id_product.'" OR a.id_product=0) AND (cap.required =1 OR ((cap.required=-1 OR cap.required is NULL) AND a.required=1))');
                if($required_attributes)
                {
                    foreach($required_attributes as $required_attribute)
                    {
                        if(!in_array($required_attribute['id_ets_eto_attr'],$attributes))
                            $errors[] = sprintf($this->l('%s%s%s is required'),'"',$required_attribute['name'],'"');
                    }
                }
            }
        }
        
    }
    public function postAddCustomAttributeToCart($id_product,$id_product_attribute,$quantity)
    {
        $attributes = Tools::getValue('ets-ca-custom-attribute',array());
        if($attributes && is_array($attributes) && self::validateArray($attributes))
        {
            foreach($attributes as $key=> $id_attribute)
            {
                $attributeObj  = new Ets_eto_attribute_class($id_attribute);
                if(!Validate::isLoadedObject($attributeObj) || ($attributeObj->id_product!=0 && $attributeObj->id_product!=$id_product) || $attributeObj->id_shop != $this->context->shop->id)
                {
                    unset($attributes[$key]);
                }
            }
        }
        if($attributes && is_array($attributes) && self::validateArray($attributes))
        {
            if($id_combination = Ets_eto_combination_class::getIdCombinationByAttributes($attributes))
            {
                Ets_eto_combination_class::processChangeCustomAttribute($id_product,$id_product_attribute,$quantity, $id_combination);
            }
        }
        if(!$attributes && Ets_eto_attribute_class::getAttributesByProduct($id_product,true,' AND (a.id_product="'.(int)$id_product.'" OR a.id_product=0)'))
        {
            if($id_combination = (int)Tools::getValue('id_combination'))
                Ets_eto_combination_class::processChangeCustomAttribute($id_product,$id_product_attribute,$quantity, $id_combination);
            else
            {
                Ets_eto_combination_class::processChangeCustomAttribute($id_product,$id_product_attribute,$quantity, 0);
            }
        }
        
    }
    public function getPriceAttributeCustom($cart,$widthTax=true)
    {
        return Ets_eto_attribute_cart_class::getPriceAttributeCustom($cart,$widthTax);
    }
    public function _submitChangeStatus($id_attribute,$field)
    {
        $attribute = new Ets_eto_attribute_class($id_attribute);
        $attribute->{$field} = (int)Tools::getValue('change_enabled');
        if($attribute->update())
        {
            if($attribute->{$field})
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'href' => $this->context->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&id_ets_eto_attr='.$attribute->id.'&change_enabled=0&field='.$field,
                            'title' => $this->l('Click to disable'),
                            'success' => $this->l('Successfully updated.'),
                            'enabled' => 1,
                        )
                    )  
                );
            }
            else
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'href' => $this->context->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&id_ets_eto_attr='.$attribute->id.'&change_enabled=1&field='.$field,
                            'title' => $this->l('Click to enable'),
                            'success' => $this->l('Successfully updated.'),
                            'enabled' => 0,
                        )
                    )  
                );
            }
        }
        else
        {
            die(
                Tools::jsonEncode(
                    array(
                        'errors' => $this->l('An error occurred while saving the option')
                    )
                )
            );
        }
    }
    public function copy_directory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    if (file_exists($dst . '/' . $file) && $file != 'index.php' && ($content = Tools::file_get_contents($dst . '/' . $file)) && Tools::strpos($content, 'overried_custom_payment by chung_ets') === false && Tools::strpos($content, 'overried by chung_ets') === false)
                        copy($dst . '/' . $file, $dst . '/backup_' . $file);
                    if (!file_exists($dst . '/' . $file) || (file_exists($dst . '/' . $file) && ($content = Tools::file_get_contents($dst . '/' . $file)) && Tools::strpos($content, 'overried by chung_ets') === false))
                        copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }
    public function delete_directory($directory)
    {
        $dir = opendir($directory);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($directory . '/' . $file)) {
                    $this->delete_directory($directory . '/' . $file);
                } else {
                    if (file_exists($directory . '/' . $file) && $file != 'index.php' && ($content = Tools::file_get_contents($directory . '/' . $file)) && Tools::strpos($content, 'overried by chung_ets') !== false) {
                        @unlink($directory . '/' . $file);
                        if (file_exists($directory . '/backup_' . $file))
                            copy($directory . '/backup_' . $file, $directory . '/' . $file);
                    }

                }
            }
        }
        closedir($dir);
        return true;
    }
    public function hookDisplayPaymentFeeOrder($params)
    {
        if(isset($params['orderId']) &&  ($id_order = (int)$params['orderId']) && ($order = new Order($id_order)) && Validate::isLoadedObject($order))
        {
            if (($customPrice = Ets_eto_attribute_order_class::getCustomPrice($order))!==false) {
                $this->context->smarty->assign(
                    array(
                        'ca_custom_attribute_price' => Tools::displayPrice($customPrice,new Currency($order->id_currency)),
                    )
                );
                return $this->display(__FILE__,'fee_order.tpl');
            }
            
        }
    }
    public function getProducts(&$cart,$refresh = false, $id_product = false, $id_country = null, $fullInfos = true,$keepOrderPrices=false)
    {
        return Ets_eto_attribute_class::getInstance()->getProducts($cart,$refresh,$id_product,$id_country,$fullInfos,$keepOrderPrices);
    }
    public function hookDisplayOverrideTemplate($params)
    {
        if (isset($params['template_file']) && $params['template_file'] == 'checkout/cart') {
            return $this->getTemplatePath('checkout/cart.tpl');
        }
    }
    public static function getContextLocale(Context $context)
    {
        $locale = $context->getCurrentLocale();
        if (null !== $locale) {
            return $locale;
        }

        $containerFinder = new PrestaShop\PrestaShop\Adapter\ContainerFinder($context);
        $container = $containerFinder->getContainer();
        if (null === $context->container) {
            $context->container = $container;
        }

        /** @var LocaleRepository $localeRepository */
        $localeRepository = $container->get(Tools::SERVICE_LOCALE_REPOSITORY);
        $locale = $localeRepository->getLocale(
            $context->language->getLocale()
        );

        return $locale;
    }
    public function hookActionEmailSendBefore($params)
    {
        if (isset($this->context->cart->id)) {
            $id_order = Order::getIdByCartId($this->context->cart->id);
            if ($id_order && ($order = new Order($id_order)) && Validate::isLoadedObject($order) && $params['template'] == 'order_conf') {
                $product_var_tpl_list = [];
                $products = $order->getProductsDetail();    
                foreach ($products as $product) {
                    $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? $product['unit_price_tax_excl'] : $product['unit_price_tax_incl'];
                    $product_var_tpl = [
                        'id_product' => $product['product_id'],
                        'reference' => $product['product_reference'],
                        'name' => $product['product_name'],
                        'price' => self::getContextLocale($this->context)->formatPrice($product_price * $product['product_quantity'], $this->context->currency->iso_code),
                        'quantity' => $product['product_quantity'],
                        'customization' => [],
                    ];
                    $product_var_tpl['unit_price'] = self::getContextLocale($this->context)->formatPrice($product_price, $this->context->currency->iso_code);
                    $product_var_tpl['unit_price_full'] = self::getContextLocale($this->context)->formatPrice($product_price, $this->context->currency->iso_code); 
                    $customized_datas = Product::getAllCustomizedDatas((int) $order->id_cart, null, true, null, (int) $product['id_customization']);
                    if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']])) {
                        $product_var_tpl['customization'] = [];
                        foreach ($customized_datas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization) {
                            $customization_text = '';
                            if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                    $customization_text .= $this->displayText($text['name'],'strong').' '. $text['value'] . $this->displayText('','br');
                                }
                            }

                            if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                $customization_text .= sprintf($this->l('%d image(s)'),count($customization['datas'][Product::CUSTOMIZE_FILE])) . $this->displayText('','br');
                            }
                            $customization_quantity = (int) $customization['quantity'];
                            $product_var_tpl['customization'][] = [
                                'customization_text' => $customization_text,
                                'customization_quantity' => $customization_quantity,
                                'quantity' => self::getContextLocale($this->context)->formatPrice($customization_quantity * $product_price, $this->context->currency->iso_code),
                            ];
                        }
                    }
                    $product_var_tpl_list[] = $product_var_tpl;
                    
                }
                $product_list_txt = '';
                $product_list_html = '';
                if (count($product_var_tpl_list) > 0) {
                    $product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
                    $product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
                }
                $params['templateVars']['{products}'] = $product_list_html;
                $params['templateVars']['{products_txt}'] = $product_list_txt;
            }
        }
    }
    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $pathToFindEmail = array(
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $this->context->language->iso_code . DIRECTORY_SEPARATOR . $template_name,
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . $template_name,
            _PS_MAIL_DIR_ . $this->context->language->iso_code . DIRECTORY_SEPARATOR . $template_name,
            _PS_MAIL_DIR_ . 'en' . DIRECTORY_SEPARATOR . $template_name,
            _PS_MAIL_DIR_ . '_partials' . DIRECTORY_SEPARATOR . $template_name,
        );

        foreach ($pathToFindEmail as $path) {
            if (Tools::file_exists_cache($path)) {
                $this->context->smarty->assign('list', $var);

                return $this->context->smarty->fetch($path);
            }
        }

        return '';
    }
    public function hookActionObjectProductInCartDeleteBefore($params)
    {
        $id_cart = isset($params['id_cart']) ? (int)$params['id_cart']:0;
        $id_product = isset($params['id_product']) ? (int)$params['id_product']:0;
        $id_product_attribute = isset($params['id_product_attribute']) ? (int)$params['id_product_attribute']:0;
        if($id_cart && $id_product)
        {
            if(Ets_eto_attribute_cart_class::deleteAttributeCustom($id_cart,$id_product,$id_product_attribute))
            {
                die(
                    Tools::jsonEncode(
                        array(
                            'success' => true,
                            'id_product' => $id_product,
                            'id_product_attribute' => $id_product_attribute,
                            'id_customization' => isset($params['customization_id'])? (int)$params['customization_id']:'',
                        )
                    )
                );
            }
        }
    }
    public function getTextLang($text, $lang,$file_name='')
    {
        if(is_array($lang))
            $iso_code = $lang['iso_code'];
        elseif(is_object($lang))
            $iso_code = $lang->iso_code;
        else
        {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
		$modulePath = rtrim(_PS_MODULE_DIR_, '/').'/'.$this->name;
        $fileTransDir = $modulePath.'/translations/'.$iso_code.'.'.'php';
        if(!@file_exists($fileTransDir)){
            return $text;
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $text_tras = preg_replace("/\\\*'/", "\'", $text);
        $strMd5 = md5($text_tras);
        $keyMd5 = '<{' . $this->name . '}prestashop>' . ($file_name ? : $this->name) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if($matches && isset($matches[2])){
           return  $matches[2];
        }
        return $text;
    }
    public function hookActionProductSave($params)
    {
        if(isset($params['id_product']) && $params['id_product'])
        {
            $use_attribute = Tools::getValue('use_attribute');
            $price_attribute = Tools::getValue('price_attribute');
            $required_attribute = Tools::getValue('required_attribute');
            $checked_default_attribute = Tools::getValue('checked_default_attribute');
            $price_attribute_custom = Tools::getValue('price_attribute_custom');
            $use_tax_attribute = Tools::getValue('use_tax_attribute');
            $use_discount_attribute = Tools::getValue('use_discount_attribute');
            $errors = array();
            if($use_attribute && is_array($use_attribute) && self::validateArray($use_attribute) && self::validateArray($price_attribute) && self::validateArray($required_attribute) && self::validateArray($checked_default_attribute) && self::validateArray($price_attribute_custom) && self::validateArray($use_tax_attribute) && self::validateArray($use_discount_attribute))
            {
                foreach(array_keys($use_attribute)  as $id_attribute)
                {
                    $attribute = new Ets_eto_attribute_class($id_attribute,$this->context->language->id);
                    if(isset($price_attribute[$id_attribute]) && $price_attribute[$id_attribute]!='default')
                    {
                        if(isset($price_attribute_custom[$id_attribute]) && $price_attribute_custom[$id_attribute] && !Validate::isPrice($price_attribute_custom[$id_attribute]))
                            $errors[] = sprintf($this->l('Price of extra option %s%s%s is not valid'),'"',$attribute->name,'"');
                    }
                }
            }
            if(!$errors && $use_attribute)
            {
                $this->updateCustomAttribute($params['id_product']);
            }
            elseif($errors)
            {
                http_response_code(422);
                die(Tools::jsonEncode(array(
                    'error' => $errors
                )));
            }
        }
    }
    public function saveAttributeGroup()
    {
        if($id_group = (int)Tools::getValue('id_ets_eto_attr_group'))
            $attributeGroup = new Ets_eto_attribute_group_class($id_group);
        else
        {
            $attributeGroup = new Ets_eto_attribute_group_class();
            $attributeGroup->id_shop = $this->context->shop->id;
        }
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $errors = array();
        $languages = Language::getLanguages();
        $name_default = Tools::getValue('name_'.$id_lang_default);
        if($name_default=='')
            $errors[] = $this->l('Name is required');
        if($name_default && !Validate::isGenericName($name_default))
            $errors[] = $this->l('Name is not valid');
        foreach($languages as $language)
        {
            $id_lang = $language['id_lang'];
            if($id_lang!= $id_lang_default)
            {
                $name = Tools::getValue('name_'.$id_lang);
                if($name && !Validate::isGenericName($name))
                    $errors[] = sprintf($this->l('Name is not valid in %s'),$language['iso_code']);
                else
                    $attributeGroup->name[$id_lang] = $name ? : $name_default;
            }
            else
            {
                $attributeGroup->name[$id_lang_default] = $name_default;
            }
        }
        
        if(!$errors)
        {
            if($attributeGroup->id)
            {
                if($attributeGroup->update())
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'success' => $this->l('Successfully updated.'),
                                'list_attribute_groups' =>Ets_eto_attribute_group_class::getInstance()->renderGroupAttributes(),
                            )
                        )
                    );
                }
                else
                    $errors[] = $this->l('An error occurred while saving the option group');
            }
            else
            {
                if($attributeGroup->add())
                {
                    die(
                        Tools::jsonEncode(
                            array(
                                'success' => $this->l('Created successfully'),
                                'list_attribute_groups' =>Ets_eto_attribute_group_class::getInstance()->renderGroupAttributes(),
                            )
                        )
                    );
                }
                else
                    $errors[] = $this->l('An error occurred while saving the option group');
            }
        }
        if($errors)
        {
            die(
                Tools::jsonEncode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
        }
    }
    public function checkCreatedColumn($table,$column)
    {
        $fieldsCustomers = Db::getInstance()->ExecuteS('DESCRIBE '._DB_PREFIX_.pSQL($table));
        $check_add=false;
        foreach($fieldsCustomers as $field)
        {
            if($field['Field']==$column)
            {
                $check_add=true;
                break;
            }    
        }
        return $check_add;
    }
}