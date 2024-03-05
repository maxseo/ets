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

class Ets_eto_attribute_class extends ObjectModel
{
    protected static $instance;
    public $used;
    public $id_product;
    public $id_shop;
    public $price;
    public $required;
    public $checked;
    public $use_tax;
    public $use_discount;
    public $active;
    public $position;
    public $name;
    public $description;
    public $display_by_option_group;
    public $id_ets_eto_attr_group;
    public static $definition = array(
        'table' => 'ets_eto_attr',
        'primary' => 'id_ets_eto_attr',
        'multilang' => true,
        'fields' => array(
            'used' => array('type' => self::TYPE_INT),
            'id_product' => array('type' => self::TYPE_INT),
            'id_shop' => array('type' => self::TYPE_INT),
            'price' => array('type' => self::TYPE_FLOAT),
            'required' => array('type' => self::TYPE_INT),
            'checked' => array('type'=>self::TYPE_DATE),
            'use_tax' => array('type' => self::TYPE_INT),
            'use_discount' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'display_by_option_group' => array('type' => self::TYPE_INT),
            'id_ets_eto_attr_group' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING,'lang'=>true),
            'description' => array('type' => self::TYPE_STRING,'lang'=>true),
        ),
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    public function add($auto_date=true,$null_values=false)
    {
        $max_posistion = Db::getInstance()->getValue('SELECT max(position) FROM  `'._DB_PREFIX_.'ets_eto_attr` WHERE id_product="'.(int)$this->id_product.'" AND id_shop="'.(int)Context::getContext()->shop->id.'" AND id_ets_eto_attr_group='.(int)$this->id_ets_eto_attr_group);
        $this->position = $max_posistion+1;
        return parent::add($auto_date,$null_values);
    }
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_eto_attribute_class();
        }
        return self::$instance;
    }
    public function renderListProductAttributes($id_product=0,$id_group = false)
    {
        $sort_type = Tools::strtolower(Tools::getValue('sort_type',$id_product ?'asc':'desc'));
        if(!in_array($sort_type,array('asc','desc')))
            $sort_type = 'asc';
        $sort_post = Tools::strtolower(Tools::getValue('sort',$id_product ? 'position':'id_ets_eto_attr'));
        $fields_list = array(
            'id_ets_eto_attr' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
            ),
            'product_image' => array(
                'title' => $this->l('Image'),
                'width' => 40,
                'type' => 'text',
                'sort' => false,
                'filter' => false,
                'strip_tag' => false,
            ),
            'product_name' => array(
                'title' => $this->l('Product name'),
                'width' => 40,
                'type' => 'text',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag' => false,
            ),
            'name' => array(
                'title' => $this->l('Option name'),
                'width' => 40,
                'type' => 'text',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'width' => 40,
                'type' => 'text',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag' => true,
            ),
            'used' => array(
                'title' => $this->l('Use global option'),
                'type' => 'active',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,   
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),          
            ),
            'price' => array(
                'title' => $this->l('Price'),
                'type' => 'int',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                
            ),
            'use_tax' => array(
                'title' => $this->l('Apply tax'),
                'type' => 'active',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'use_discount' => array(
                'title' => $this->l('Apply specific price'),
                'type' => 'active',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'required' => array(
                'title' => $this->l('Required'),
                'type' => 'active',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'checked' => array(
                'title' => $this->l('Checked by default'),
                'type' => 'active',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'type' => 'active',
                'sort' => $id_product ? false: true,
                'filter' => $id_product ? false: true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            //'position' => array(
//                'title' => $this->l('Position'),
//                'type' => 'int',
//                'sort' => true,
//                'update_position' => $sort_post=='position' && $sort_type=='asc' ? true :false,
//            ),
        );
        if($id_product){
            unset($fields_list['product_image']);
            unset($fields_list['product_name']);
            $fields_list['position'] = array(
                'title' => $this->l('Position'),
                'type' => 'int',
                'sort' => true,
                'update_position' => $sort_post=='position' && $sort_type=='asc' ? true :false,
            );
        }
        //Filter
        $show_resset = false;
        $filter = ($id_product ? " AND a.id_product=".(int)$id_product: " AND a.id_product!=0").($id_group!==false ? ' AND a.id_ets_eto_attr_group='.(int)$id_group:'');
        if(Tools::isSubmit('ets_eto_submit_ca_specific_attribute'))
        {
            if(($product_name = Tools::getValue('product_name'))!='' && Validate::isCleanHtml($product_name))
            {
                $filter .=' AND pl.name LIKE "%'.pSQL($product_name).'%"';
                $show_resset = true;
            }
            if(($id_attribute = Tools::getValue('id_ets_eto_attr'))!='' && Validate::isCleanHtml($id_attribute) )
            {
                $filter .=' AND a.id_ets_eto_attr ='.(int)$id_attribute;
                $show_resset = true;
            }
            if(($name = Tools::getValue('name'))!='' && Validate::isCleanHtml($name))
            {
                $filter .=' AND al.name LIKE "%'.pSQL($name).'%"';
                $show_resset = true;
            }
            if(($desc = Tools::getValue('description'))!='' && Validate::isCleanHtml($desc))
            {
                $filter .=' AND al.description LIKE "%'.pSQL($desc).'%"';
                $show_resset = true;
            }
            if(($used = Tools::getValue('used'))!='' && Validate::isCleanHtml($used))
            {
                $filter .=' AND a.used='.(int)$used;
                $show_resset = true;
            }
            if(($required = Tools::getValue('required'))!='' && Validate::isCleanHtml($required))
            {
                $filter .=' AND a.required='.(int)$required;
                $show_resset = true;
            }
            if(($checked = Tools::getValue('checked'))!='' && Validate::isCleanHtml($checked))
            {
                $filter .=' AND a.checked='.(int)$checked;
                $show_resset = true;
            }
            if(($active = Tools::getValue('active'))!='' && Validate::isCleanHtml($active))
            {
                $filter .=' AND a.active='.(int)$active;
                $show_resset = true;
            }
            if(($price_min = Tools::getValue('price_min'))!='' && Validate::isCleanHtml($price_min))
            {
                $filter .=' AND a.price >='.(float)$price_min;
                $show_resset = true;
            }
            if(($price_max = Tools::getValue('price_max'))!='' && Validate::isCleanHtml($price_max))
            {
                $filter .=' AND a.price <='.(float)$price_max;
                $show_resset = true;
            }
        }
        //Sort
        $sort = "";
        if($sort_post)
        {
            switch ($sort_post) {
                case 'id_ets_eto_attr':
                    $sort .='a.id_ets_eto_attr';
                    break;
                case 'name':
                    $sort .='al.name';
                    break;
                case 'description':
                    $sort .='al.description';
                    break;
                case 'used':
                    $sort .='a.used';
                    break;
                case 'price':
                    $sort .='a.price';
                    break;
                case 'required':
                    $sort .='a.required';
                    break;
                case 'checked':
                    $sort .='a.checked';
                    break;
                case 'active':
                    $sort .='a.active';
                    break;
                case 'position':
                    $sort .='a.position';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);  
        }
        //Paggination
        $module = Module::getInstanceByName('ets_extraoptions');
        $page = (int)Tools::getValue('page');
        if($page < 1)
            $page =1;
        $totalRecords = (int) Ets_eto_attribute_class::getAttributes($filter,0,0,'',true);
        $paggination = new Ets_eto_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=specific&page=_page_';
        $paggination->limit =  20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $attributes = Ets_eto_attribute_class::getAttributes($filter, $start,$id_product ? false: $paggination->limit,$sort,false);
        if($attributes)
        {
            foreach($attributes as &$attribute)
            {
                $attribute['description'] = Tools::nl2br($attribute['description']);
            }
        }
        if(isset($fields_list['product_image']))
        {
            if(version_compare(_PS_VERSION_, '1.7', '>='))
                $type_image= ImageType::getFormattedName('small');
            else
                $type_image= ImageType::getFormatedName('small');
            foreach($attributes as &$attribute)
            {
                if($attribute['id_product'])
                {
                    if(!$id_image = Db::getInstance()->getValue('SELECT id_image FROM  `'._DB_PREFIX_.'image` WHERE cover=1 AND id_product='.(int)$attribute['id_product']))
                        $id_image = Db::getInstance()->getValue('SELECT id_image FROM  `'._DB_PREFIX_.'image` WHERE id_product='.(int)$attribute['id_product']);
                    if($id_image)
                    {
                        $product = new Product($attribute['id_product'],false,Context::getContext()->language->id);
                        $attribute['product_image'] = Module::getInstanceByName('ets_extraoptions')->displayText(null,'img',null,null,null,null,Context::getContext()->link->getImageLink($product->link_rewrite,$id_image,$type_image));
                    }
                    $attribute['product_name'] = Module::getInstanceByName('ets_extraoptions')->displayText($attribute['product_name'],'a',null,null,Context::getContext()->link->getAdminLink('AdminProducts',true,array('id_product'=>$attribute['id_product'])));
                }
            }
            unset($attribute);
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $listData = array(
            'name' => 'ca_specific_attribute',
            'icon' => 'fa fa-bank',
            'actions' => array('view','delete'),
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=specific',
            'identifier' => 'id_ets_eto_attr',
            'show_toolbar' => $id_product ? false : true,
            'show_title' => $id_product ? false : true,
            'class_list' => 'list-specific-attributes',
            'show_action' => true,
            'title' => $this->l('Specific options'),
            'fields_list' => $fields_list,
            'field_values' => $attributes,
            'paggination' => $id_product ? '': $paggination->render(),
            'filter_params' => $module->getFilterParams($fields_list,'ca_attribute'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_post ? $sort_post : 'id_ets_eto_attr',
            'show_add_new'=> false,
            'link_new' => Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=specific&addattribute=1', 
            'bulk_actions' => false,
            'sort_type' => $sort_type,
            //'form_new' => !$id_product ? $this->renderFormAttribute():false,
        ); 
        return  $module->renderList($listData);
    }
    public function renderProductAttributes($id_product=0)
    {
        $groups = Ets_eto_attribute_group_class::getListGroupHasAttribute($id_product ? :false);
        if($groups)
        {
            foreach($groups as &$group)
            {
                $group['list_attributes'] = $this->renderListProductAttributes($id_product,$group['id_ets_eto_attr_group']);
            }
        }
        Context::getContext()->smarty->assign(
            array(
                'groups' => $groups,
                'form_new' => !$id_product ? $this->renderFormAttribute():false,
                'id_product' => $id_product,
            )
        );
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_extraoptions/views/templates/hook/list_product_attributes.tpl');
    }
    public function renderAttributes()
    {
        $groups = Ets_eto_attribute_group_class::getListGroupHasAttribute(0);
        if($groups)
        {
            foreach($groups as &$group)
            {
                $group['list_attributes'] = $this->rederListAttributes($group['id_ets_eto_attr_group']);
            }
        }
        Context::getContext()->smarty->assign(
            array(
                'groups' => $groups,
                'form_new' => Tools::isSubmit('saveAttribute') ? false : $this->renderFormAttribute(),
            )
        );
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ets_extraoptions/views/templates/hook/list_attributes.tpl');
    }
    public function rederListAttributes($id_group)
    {
        $sort_type = Tools::strtolower(Tools::getValue('sort_type','asc'));
        if(!in_array($sort_type,array('asc','desc')))
            $sort_type = 'asc';
        $sort_post = Tools::strtolower(Tools::getValue('sort','position'));
        $fields_list = array(
            'id_ets_eto_attr' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name' => array(
                'title' => $this->l('Option name'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
                'strip_tag' => true,
            ),
            'used' => array(
                'title' => $this->l('Use option'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,   
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),          
            ),
            'price' => array(
                'title' => $this->l('Price'),
                'type' => 'int',
                'sort' => true,
                'filter' => true,
                
            ),
            'use_tax' => array(
                'title' => $this->l('Apply tax'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'use_discount' => array(
                'title' => $this->l('Apply specific price'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'required' => array(
                'title' => $this->l('Required'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'checked' => array(
                'title' => $this->l('Checked by default'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag'=> false,
                'filter_list' => array(
                    'list' => array(
                        array(
                            'id_option'=>1,
                            'value' => $this->l('Yes'),
                        ),
                        array(
                            'id_option'=>0,
                            'value' => $this->l('No'),
                        ),
                    ),
                    'id_option' => 'id_option',
                    'value' => 'value',
                ),
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'type' => 'int',
                'sort' => true,
                'update_position' => $sort_post=='position' && $sort_type=='asc' ? true :false,
            ),
        );
        //Filter
        $show_resset = false;
        $filter = " AND a.id_product=0 AND a.id_ets_eto_attr_group=".(int)$id_group;
        if(Tools::isSubmit('ets_eto_submit_ca_attribute'))
        {
            if(($id_attribute = Tools::getValue('id_ets_eto_attr'))!='' && Validate::isCleanHtml($id_attribute) )
            {
                $filter .=' AND a.id_ets_eto_attr ='.(int)$id_attribute;
                $show_resset = true;
            }
            if(($name = Tools::getValue('name'))!='' && Validate::isCleanHtml($name))
            {
                $filter .=' AND al.name LIKE "%'.pSQL($name).'%"';
                $show_resset = true;
            }
            if(($desc = Tools::getValue('description'))!='' && Validate::isCleanHtml($desc))
            {
                $filter .=' AND al.description LIKE "%'.pSQL($desc).'%"';
                $show_resset = true;
            }
            if(($used = Tools::getValue('used'))!='' && Validate::isCleanHtml($used))
            {
                $filter .=' AND a.used='.(int)$used;
                $show_resset = true;
            }
            if(($required = Tools::getValue('required'))!='' && Validate::isCleanHtml($required))
            {
                $filter .=' AND a.required='.(int)$required;
                $show_resset = true;
            }
            if(($checked = Tools::getValue('checked'))!='' && Validate::isCleanHtml($checked))
            {
                $filter .=' AND a.checked='.(int)$checked;
                $show_resset = true;
            }
            if(($active = Tools::getValue('active'))!='' && Validate::isCleanHtml($active))
            {
                $filter .=' AND a.active='.(int)$active;
                $show_resset = true;
            }
            if(($price_min = Tools::getValue('price_min'))!='' && Validate::isCleanHtml($price_min))
            {
                $filter .=' AND a.price >='.(float)$price_min;
                $show_resset = true;
            }
            if(($price_max = Tools::getValue('price_max'))!='' && Validate::isCleanHtml($price_max))
            {
                $filter .=' AND a.price <='.(float)$price_max;
                $show_resset = true;
            }
        }
        //Sort
        $sort = "";
        if($sort_post)
        {
            switch ($sort_post) {
                case 'id_ets_eto_attr':
                    $sort .='a.id_ets_eto_attr';
                    break;
                case 'name':
                    $sort .='al.name';
                    break;
                case 'description':
                    $sort .='al.description';
                    break;
                case 'used':
                    $sort .='a.used';
                    break;
                case 'price':
                    $sort .='a.price';
                    break;
                case 'required':
                    $sort .='a.required';
                    break;
                case 'checked':
                    $sort .='a.checked';
                    break;
                case 'active':
                    $sort .='a.active';
                    break;
                case 'position':
                    $sort .='a.position';
                    break;
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.trim($sort_type);  
        }
        //Paggination
        $module = Module::getInstanceByName('ets_extraoptions');
        $page = (int)Tools::getValue('page');
        if($page < 1)
            $page =1;
        $totalRecords = (int) Ets_eto_attribute_class::getAttributes($filter,0,0,'',true);
        $paggination = new Ets_eto_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=global&page=_page_';
        $paggination->limit =  20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $attributes = Ets_eto_attribute_class::getAttributes($filter, $start,$paggination->limit,$sort,false);
        if($attributes)
        {
            foreach($attributes as &$attribute)
            {
                $attribute['description'] = Tools::nl2br($attribute['description']);
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $listData = array(
            'name' => 'ca_attribute',
            'icon' => 'fa fa-bank',
            'actions' => array('view','delete'),
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=global',
            'identifier' => 'id_ets_eto_attr',
            'show_toolbar' => true,
            'class_list' => 'list-attributes',
            'show_action' => true,
            'title' => $this->l('Global options'),
            'fields_list' => $fields_list,
            'field_values' => $attributes,
            'paggination' => $paggination->render(),
            'filter_params' => $module->getFilterParams($fields_list,'ca_attribute'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_post ? $sort_post : 'id_ets_eto_attr',
            'show_add_new'=> false,
            'link_new' => Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=global&addattribute=1', 
            'bulk_actions' => false,
            'sort_type' => $sort_type,
            'form_new' => false,
        ); 
        return $module->renderList($listData);
    }
    public function getAttributes($filter='',$start=0,$limit=12,$order_by='a.id_ets_eto_attr asc',$total=false)
    {
        if($total)
            $sql = 'SELECT COUNT(DISTINCT a.id_ets_eto_attr)';
        else
            $sql ='SELECT a.*,al.name,al.description,pl.name as product_name';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_eto_attr` a
        LEFT JOIN `'._DB_PREFIX_.'ets_eto_attr_lang` al ON (a.id_ets_eto_attr = al.id_ets_eto_attr AND al.id_lang = "'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (a.id_product = pl.id_product AND pl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE a.id_shop="'.(int)Context::getContext()->shop->id.'" '.($filter ? $filter:'');
        if(!$total)
        {
            $sql .=' GROUP BY a.id_ets_eto_attr ';
            $sql .= ($order_by ? ' ORDER By '.$order_by :'');
            if($limit!==false)
                $sql .= ' LIMIT '.(int)$start.','.(int)$limit;
        }
        if($total)
            return Db::getInstance()->getValue($sql);
        else
        {
            return Db::getInstance()->executeS($sql);
        }
    }
    public function getAttributesByProduct($id_product,$used = false,$filter='')
    {
        $sql = 'SELECT a.*,al.name,al.description,IFNULL(cap.position,a.position) AS sort_order, IF(cap.used IS NULL,-1,cap.used) AS product_used,IF(cap.price IS NULL,-1,cap.price) AS product_price,IF(cap.required IS NULL,-1,cap.required) AS product_required,IF(cap.checked IS NULL,-1,cap.checked) AS product_checked,IF(cap.use_tax IS NULL,-1,cap.use_tax) AS product_use_tax,IF(cap.use_discount IS NULL,-1,cap.use_discount) AS product_use_discount 
        FROM `'._DB_PREFIX_.'ets_eto_attr` a
        LEFT JOIN `'._DB_PREFIX_.'ets_eto_attr_lang` al ON (a.id_ets_eto_attr = al.id_ets_eto_attr AND al.id_lang="'.(int)Context::getContext()->language->id.'")
        LEFT JOIN `'._DB_PREFIX_.'ets_eto_attr_product` cap ON (cap.id_ets_eto_attr = a.id_ets_eto_attr AND cap.id_product="'.(int)$id_product.'")
        WHERE a.active=1 AND a.id_shop ="'.(int)Context::getContext()->shop->id.'" '.($used ? ' AND (cap.used=1 OR ((cap.used=-1 OR cap.used IS NULL) AND a.used=1)) ':'').($filter ? $filter :'').' ORDER BY sort_order ASC';
        return Db::getInstance()->executeS($sql);
    }
    public function renderFormAttribute($id_product=0)
    {
        $module = Module::getInstanceByName('ets_extraoptions');
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->id ? ($this->id_product ? $this->l('Edit specific option'): $this->l('Edit option')): ($id_product ? $this->l('Add specific option') : $this->l('Add option')),
                    'icon' =>'icon-attribute',				
				),
				'input' => array(
                    array(
                        'type'=>'hidden',
                        'name' => 'id_attribute',
                    ),
                    array(
                        'type'=>'hidden',
                        'name' => 'id_product',
                    ),					
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'name', 
                        'lang' => true,	
                        'required' => true,	                     
					),
                    array(
						'type' => 'textarea',
						'label' => $this->l('Description'),
						'name' => 'description', 
                        'lang' => true,			                     
					),  
                    array(
                        'type' => 'switch',
                        'label' => $id_product || $this->id_product ? $this->l('Use option'):  $this->l('Use option (Globally)'),
                        'desc' => $this->l('This option will be applied to all existing products in your store.'),
                        'name'=> 'used',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                    array(
						'type' => 'text',
						'label' => $this->l('Price'),
                        'desc' => $this->l('The price will be added to the product price if customers select this option.'),
						'name' => 'price', 
                        'suffix' => Context::getContext()->currency->sign,	
                        'col'=>3,
					),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Apply tax'),
                        'desc' => $this->l('Apply the tax rule defined on "Pricing" tab of the product editing page'),
                        'name'=> 'use_tax',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Apply specific price'),
                        'desc' => $this->l('Apply the specific price defined on "Pricing" tab of the product editing page'),
                        'name'=> 'use_discount',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $id_product || $this->id_product ? $this->l('Required'): $this->l('Required (Global)'),
                        'desc' => $this->l('Customers have to select this option when purchasing a product'),
                        'name'=> 'required',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Checked by default'),
                        'name'=> 'checked',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display by option group'),
                        'name'=> 'display_by_option_group',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Option group'),
                        'name' => 'id_ets_eto_attr_group',
                        'form_group_class' => 'id_ets_eto_attr_group',
                        'options' => array(
                            'query' => Ets_eto_attribute_group_class::getListAttributeGroups(),
                            'id' => 'id_ets_eto_attr_group',
                            'name' => 'name'
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name'=> 'active',
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('On')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Off')
							)
						),
                    ),
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				),
                'buttons' => array(
                    array(
                        'icon' => 'process-icon-cancel',
                        'title' => $this->l('Cancel'),
                        'class' => 'tbn-cancel'
                    )
                ),
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = 'ets_eto_attribute';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $module;
		$helper->identifier = 'id_ets_eto_att';
		$helper->submit_action = 'saveAttribute';
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminModules',false).'&configure=ets_extraoptions&tab_active=global';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => Context::getContext()->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getAttributeFieldsValues($id_product),
			'languages' => Context::getContext()->controller->getLanguages(),
			'id_language' => Context::getContext()->language->id,
            'link'=> Context::getContext()->link,
		);            
        return $helper->generateForm(array($fields_form));
    }
    public function getAttributeFieldsValues($id_product=0)
    {
        $fields = array();
        $fields['id_attribute'] = $this->id;
        $fields['id_product'] = $this->id_product ? : $id_product;
        $fields['active'] = (int)Tools::getValue('active',$this->id ? $this->active:1);
        $fields['checked'] = (int)Tools::getValue('checked',$this->id ? $this->checked:0);
        $fields['required'] = (int)Tools::getValue('required',$this->id ? $this->required:0);
        $fields['price'] = Tools::getValue('price', $this->id ? $this->price:'');
        $fields['used'] = (int)Tools::getValue('used',$this->id ? $this->used:1);
        $fields['use_tax'] = (int)Tools::getValue('use_tax',$this->id ? $this->use_tax:0);
        $fields['use_discount'] = (int)Tools::getValue('use_discount',$this->id ? $this->use_discount:0);
        $fields['display_by_option_group'] = (int)Tools::getValue('display_by_option_group',$this->id ? $this->display_by_option_group:0);
        $fields['id_ets_eto_attr_group'] = (int)Tools::getValue('id_ets_eto_attr_group',$this->id_ets_eto_attr_group);
        $languages = Language::getLanguages(false);
        foreach($languages as $language)
        {
            $fields['name'][$language['id_lang']] = Tools::getValue('name_'.$language['id_lang'],$this->name[$language['id_lang']]);
            $fields['description'][$language['id_lang']] = Tools::getValue('description_'.$language['id_lang'], $this->description[$language['id_lang']]);

        }
        return $fields;
    }
    public function _updateAttributesOrdering($attributes)
    {
        $page = (int)Tools::getValue('page',1);
        if($attributes)
        {
            $id_group =0;
            foreach($attributes as $key=> $id_attribute)
            {
                $position = ($page-1)*20 +$key+1;
                Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_eto_attr` SET position ="'.(int)$position.'" WHERE id_ets_eto_attr='.(int)$id_attribute);
                if(!$id_group)
                    $id_group = (int)Db::getInstance()->getValue('SELECT id_ets_eto_attr_group FROM  `'._DB_PREFIX_.'ets_eto_attr` WHERE id_ets_eto_attr='.(int)$id_attribute);
            }
            die(
                Tools::jsonEncode(
                    array(
                        'success' => $this->l('Successfully updated'),
                        'page'=>$page,
                        'id_group' => $id_group ? $id_group :'0',
                    )
                )
            );
        }
    }
    public function updateCustomAttribute($data)
    {
        if(Db::getInstance()->getRow('SELECT * FROM  `'._DB_PREFIX_.'ets_eto_attr_product` WHERE id_product= "'.(int)$data['id_product'].'" AND id_ets_eto_attr="'.(int)$data['id_ets_eto_attr'].'"'))
        {
            return Db::getInstance()->update('ets_eto_attr_product',$data,' id_product="'.(int)$data['id_product'].'" AND id_ets_eto_attr='.(int)$data['id_ets_eto_attr']);
        }
        else
           return Db::getInstance()->insert('ets_eto_attr_product',$data);
    }
    public static function processDeleteCustomAttribute($id_product,$id_attribute=0)
    {
        return Db::getInstance()->execute('DELETE FROM  `'._DB_PREFIX_.'ets_eto_cart` WHERE id_cart="'.(int)Context::getContext()->cart->id.'" AND id_product="'.(int)$id_product.'"'.($id_attribute ? ' AND id_ets_eto_attr ="'.(int)$id_attribute.'"':''));
    }
    
    public static function getPriceProductAttributeCustom($id_product,$id_attribute,$withTax= true,$withDiscount = true,$onlyNoDiscount=false)
    {
        $price = 0;
        $customattribute = false;
        $attributeObj = new Ets_eto_attribute_class($id_attribute);
        if(Validate::isLoadedObject($attributeObj))
        {
            $customattribute = true;
            $attribute_product = Db::getInstance()->getRow('
            SELECT cap.* FROM  `'._DB_PREFIX_.'ets_eto_attr_product` cap
            INNER JOIN  `'._DB_PREFIX_.'ets_eto_attr` ca ON (ca.id_ets_eto_attr = cap.id_ets_eto_attr)
            WHERE ca.id_shop="'.(int)Context::getContext()->shop->id.'" AND cap.id_product="'.(int)$id_product.'" AND cap.id_ets_eto_attr='.(int)$id_attribute
            );
            if(!$attribute_product || $attribute_product['price']==-1)
            {
                $price = $attributeObj->price;
                
            }
            else
            {
                $price = $attribute_product['price'];
            }
            if(!$attribute_product || $attribute_product['use_discount']==-1)
            {
                $apply_discount = $attributeObj->use_discount;
            }
            else
            {
                $apply_discount = $attribute_product['use_discount'];
            }
            
            if(!$attribute_product || $attribute_product['use_tax']==-1)
            {
                $apply_tax = $attributeObj->use_tax;
            }
            else
            {
                $apply_tax = $attribute_product['use_tax'];
            }
            if(($apply_discount && $withDiscount) || ($apply_tax && $withTax))
            {
                $product = new Product($id_product,true);
                if($apply_discount && $withDiscount && isset($product->specificPrice) &&  $product->specificPrice && $product->specificPrice['reduction_type']=='percentage' && ($reduction = $product->specificPrice['reduction']))
                {
                    if($onlyNoDiscount)
                        return 0;
                    $price -= $price*$reduction;
                }
                if($apply_tax && $withTax && $product->id_tax_rules_group)
                    $price  = Module::getInstanceByName('ets_extraoptions')->addTaxToPrice($product->id_tax_rules_group,$price);
                //if($product->specific_prices)
            }
        }
        if($customattribute)
            return $price;
        else
            return $price;
    }
    public function getProducts(&$cart,$refresh = false, $id_product = false, $id_country = null, $fullInfos = true,$keepOrderPrices=false)
    {
        if (!$cart->id) {
            return [];
        }
        // Build query
        $sql = new DbQuery();

        // Build SELECT
        $sql->select('cp.`id_product_attribute`, cp.`id_product`,IFNULL(ca.quantity,cp.`quantity`) AS cart_quantity,IFNULL(ca.id_combination,0) as id_combination, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`,
                        pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`,
                        p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
                        product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`,
                        stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
                        p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
                        CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id, cp.id_address_delivery,
                        product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');

        // Build FROM
        $sql->from('cart_product', 'cp');

        // Build JOIN
        $sql->leftJoin('ets_eto_cart','ca','ca.id_cart=cp.id_cart AND ca.id_product=cp.id_product AND ca.id_product_attribute = cp.id_product_attribute');
        $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
        $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $cart->id_lang . Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop')
        );

        $sql->leftJoin(
            'category_lang',
            'cl',
            'product_shop.`id_category_default` = cl.`id_category`
            AND cl.`id_lang` = ' . (int) $cart->id_lang . Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')
        );

        $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        // @todo test if everything is ok, then refactorise call of this method
        $sql->join(Product::sqlStock('cp', 'cp'));

        // Build WHERE clauses
        $sql->where('cp.`id_cart` = ' . (int) $cart->id);
        if ($id_product) {
            $sql->where('cp.`id_product` = ' . (int) $id_product);
        }
        $sql->where('p.`id_product` IS NOT NULL');

        // Build ORDER BY
        $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');

        if (Customization::isFeatureActive()) {
            $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
            $sql->leftJoin(
                'customization',
                'cu',
                'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = ' . (int) $cart->id
            );
            $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`,ca.id_combination');
        } else {
            $sql->select('NULL AS customization_quantity, NULL AS id_customization');
        }

        if (Combination::isFeatureActive()) {
            $sql->select('
                product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
                IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                (p.`weight`+ pa.`weight`) weight_attribute,
                IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
            ');

            $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
        } else {
            $sql->select(
                'p.`reference` AS reference, p.`ean13`, p.`isbn`,
                p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price'
            );
        }

        $sql->select('image_shop.`id_image` id_image, il.`legend`');
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $cart->id_shop);
        $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = ' . (int) $cart->id_lang);
        if(($action = Tools::getValue('action')) && $action =='add-to-cart' && ($controller=Tools::getValue('controller')) && $controller=='ajax')
            $sql->orderBy('ca.date_add DESC');
        $result = Db::getInstance()->executeS($sql);

        // Reset the cache before the following return, or else an empty cart will add dozens of queries
        $products_ids = [];
        $pa_ids = [];
        if ($result) {
            foreach ($result as $key => $row) {
                $products_ids[] = $row['id_product'];
                $pa_ids[] = $row['id_product_attribute'];
                $specific_price = SpecificPrice::getSpecificPrice($row['id_product'], $cart->id_shop, $cart->id_currency, $id_country, $cart->id_shop_group, $row['cart_quantity'], $row['id_product_attribute'], $cart->id_customer, $cart->id);
                if ($specific_price) {
                    $reduction_type_row = ['reduction_type' => $specific_price['reduction_type']];
                } else {
                    $reduction_type_row = ['reduction_type' => 0];
                }

                $result[$key] = array_merge($row, $reduction_type_row);
            }
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheProductsFeatures($products_ids);
        Cart::cacheSomeAttributesLists($pa_ids, $cart->id_lang);

        if (empty($result)) {
            $cart->_products = [];

            return [];
        }

        if ($fullInfos) {
            $cart_shop_context = Context::getContext()->cloneContext();

            $givenAwayProductsIds = [];
            $cart->_products = [];
            if($result)
            {
                $attribute_detail_title = Configuration::get('ETS_ETO_DETAIL_ATTRIBUTE_TITLE',Context::getContext()->language->id) ?: $this->l('Extra options');
                foreach ($result as &$row) {
                    if (!array_key_exists('is_gift', $row)) {
                        $row['is_gift'] = false;
                    }
    
                    $additionalRow = Product::getProductProperties((int) $cart->id_lang, $row);
                    $row['reduction'] = $additionalRow['reduction'];
                    $row['reduction_without_tax'] = $additionalRow['reduction_without_tax'];
                    $row['price_without_reduction'] = $additionalRow['price_without_reduction'];
                    $row['specific_prices'] = $additionalRow['specific_prices'];
                    unset($additionalRow);
    
                    $givenAwayQuantity = 0;
                    $giftIndex = $row['id_product'] . '-' . $row['id_product_attribute'];
                    if ($row['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                        $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                    }
    
                    if (!$row['is_gift'] || (int) $row['cart_quantity'] === $givenAwayQuantity) {
                        $row = $cart->applyProductCalculations($row, $cart_shop_context,null,$keepOrderPrices);
                    } else {
                        // Separate products given away from those manually added to cart
                        $cart->_products[] = $cart->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity,$keepOrderPrices);
                        unset($row['is_gift']);
                        $row = $cart->applyProductCalculations(
                            $row,
                            $cart_shop_context,
                            $row['cart_quantity'] - $givenAwayQuantity,
                            $keepOrderPrices
                        );
                    }
                    if($row['id_combination'])
                    {
                        $customPrice = Ets_eto_combination_class::getPriceProductCustom($row['id_product'],$row['id_combination'],false);
                        $customPriceTaxIncl = Ets_eto_combination_class::getPriceProductCustom($row['id_product'],$row['id_combination'],true);
                        $customPrice_without_reduction = Ets_eto_combination_class::getPriceProductCustom($row['id_product'],$row['id_combination'],false,false);
                        $customPriceTaxIncl_without_reduction = Ets_eto_combination_class::getPriceProductCustom($row['id_product'],$row['id_combination'],true,false);
                        if($customPrice!==false)
                        {
                            $combinationName = Ets_eto_combination_class::getAttributeName($row['id_combination']);
                            $row['total'] += $customPrice*$row['cart_quantity'];
                            $row['total_wt'] += $customPriceTaxIncl  *$row['cart_quantity'];
                            $row['price'] += $customPrice;
                            $row['price_wt'] += $customPriceTaxIncl;
                            $row['price_without_reduction_without_tax'] += $customPrice_without_reduction;
                            $row['price_with_reduction'] += $customPriceTaxIncl;
                            $row['price_with_reduction_without_tax'] +=$customPrice;
                            $row['price_without_reduction'] += $customPriceTaxIncl_without_reduction;
                            $row['custom_attribute_name'] = trim($combinationName,', ');// .' ('.($customPrice ? Tools::displayPrice(Tools::convertPrice($customPrice)): $this->l('Free')).')';                       
                            if(isset($row['attributes']) && $row['attributes'])
                                $row['attributes'] .='- '.$attribute_detail_title.': '.$row['custom_attribute_name'];
                            else
                                $row['attributes'] = $attribute_detail_title.': '. $row['custom_attribute_name'];
                        }
                    }
                    $cart->_products[] = $row;
                    
                }
            }
        } else {
            $cart->_products = $result;
        }
        unset($refresh);
        return $cart->_products;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_extraoptions', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
}