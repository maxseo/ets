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

class Ets_eto_attribute_group_class extends ObjectModel
{
    protected static $instance;
    public $position;
    public $id_shop;
    public $name;
    public static $definition = array(
        'table' => 'ets_eto_attr_group',
        'primary' => 'id_ets_eto_attr_group',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING,'lang'=>true),
        ),
    );
    public	function __construct($id_item = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_item, $id_lang, $id_shop);
    }
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_eto_attribute_group_class();
        }
        return self::$instance;
    }
    public function add($auto_date=true,$null_values=false)
    {
        $max_posistion = Db::getInstance()->getValue('SELECT max(position) FROM  `'._DB_PREFIX_.'ets_eto_attr_group` WHERE id_shop="'.(int)Context::getContext()->shop->id.'"');
        $this->position = $max_posistion+1;
        return parent::add($auto_date,$null_values);
    }
    public function delete()
    {
        if(parent::delete())
        {
            Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_eto_attr` SET id_ets_eto_attr_group=0,display_by_option_group=0 WHERE id_ets_eto_attr_group='.(int)$this->id);
            return true;
        }
    }
    public function renderGroupAttributes()
    {
        $sort_type = Tools::strtolower(Tools::getValue('sort_type','asc'));
        if(!in_array($sort_type,array('asc','desc')))
            $sort_type = 'asc';
        $sort_post = Tools::strtolower(Tools::getValue('sort','position'));
        $fields_list = array(
            'id_ets_eto_attr_group' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
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
        $filter = "";
        if(Tools::isSubmit('ets_eto_submit_ca_attribute_group'))
        {
            if(($id_attribute_group = Tools::getValue('id_ets_eto_attr_group'))!='' && Validate::isCleanHtml($id_attribute_group) )
            {
                $filter .=' AND g.id_ets_eto_attr_group ='.(int)$id_attribute_group;
                $show_resset = true;
            }
            if(($name = Tools::getValue('name'))!='' && Validate::isCleanHtml($name))
            {
                $filter .=' AND gl.name LIKE "%'.pSQL($name).'%"';
                $show_resset = true;
            }
        }
        //Sort
        $sort = "";
        if($sort_post)
        {
            switch ($sort_post) {
                case 'id_ets_eto_attr_group':
                    $sort .='g.id_ets_eto_attr_group';
                    break;
                case 'name':
                    $sort .='gl.name';
                    break;
                case 'position':
                    $sort .='g.position';
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
        $totalRecords = (int) self::getAttributeGroups($filter,0,0,'',true);
        $paggination = new Ets_eto_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=group&page=_page_';
        $paggination->limit =  20;
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $attributes = self::getAttributeGroups($filter, $start,$paggination->limit,$sort,false);
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $listData = array(
            'name' => 'ca_attribute_group',
            'icon' => 'fa fa-bank',
            'actions' => array('view','delete'),
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=group',
            'identifier' => 'id_ets_eto_attr_group',
            'show_toolbar' => true,
            'class_list' => 'list-attribute-groups',
            'show_action' => true,
            'title' => $this->l('Option groups'),
            'fields_list' => $fields_list,
            'field_values' => $attributes,
            'paggination' => $paggination->render(),
            'filter_params' => $module->getFilterParams($fields_list,'ca_attribute_group'),
            'show_reset' =>$show_resset,
            'totalRecords' => $totalRecords,
            'sort'=> $sort_post ? $sort_post : 'id_ets_eto_attr_group',
            'show_add_new'=> true,
            'link_new' => Context::getContext()->link->getAdminLink('AdminModules').'&configure=ets_extraoptions&tab_active=group&addattributegroup=1', 
            'bulk_actions' => false,
            'sort_type' => $sort_type,
            'form_new' => $this->renderFormAttributeGroup(),
        ); 
        return $module->renderList($listData);
    }
    public static function getAttributeGroups($filter='',$start=0,$limit=12,$order_by='g.id_ets_eto_attr_group asc',$total=false)
    {
        if($total)
            $sql = 'SELECT COUNT(DISTINCT g.id_ets_eto_attr_group)';
        else
            $sql ='SELECT g.*,gl.name';
        $sql .= ' FROM `'._DB_PREFIX_.'ets_eto_attr_group` g
        LEFT JOIN `'._DB_PREFIX_.'ets_eto_attr_group_lang` gl ON (g.id_ets_eto_attr_group = gl.id_ets_eto_attr_group AND gl.id_lang = "'.(int)Context::getContext()->language->id.'")
        WHERE g.id_shop="'.(int)Context::getContext()->shop->id.'" '.($filter ? $filter:'');
        if(!$total)
        {
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
    public function renderFormAttributeGroup()
    {
        $module = Module::getInstanceByName('ets_extraoptions');
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->id ? $this->l('Edit option group'):  $this->l('Add option group'),
                    'icon' =>'icon-attribute',				
				),
				'input' => array(
                    array(
                        'type'=>'hidden',
                        'name' => 'id_ets_eto_attr_group',
                    ),				
					array(
						'type' => 'text',
						'label' => $this->l('Name'),
						'name' => 'name', 
                        'lang' => true,	
                        'required' => true,	                     
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
		$helper->table = 'ets_eto_attr_group';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $module;
		$helper->identifier = 'id_ets_eto_attr_group';
		$helper->submit_action = 'saveAttributeGroup';
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminModules',false).'&configure=ets_extraoptions&tab_active=group';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => Context::getContext()->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $this->getAttributeGroupFieldsValues(),
			'languages' => Context::getContext()->controller->getLanguages(),
			'id_language' => Context::getContext()->language->id,
		);            
        return $helper->generateForm(array($fields_form));
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_extraoptions', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function getAttributeGroupFieldsValues()
    {
        $fields = array();
        $fields['id_ets_eto_attr_group'] = $this->id;
        $languages = Language::getLanguages(false);
        foreach($languages as $language)
        {
            $fields['name'][$language['id_lang']] = Tools::getValue('name_'.$language['id_lang'],$this->name[$language['id_lang']]);
        }
        return $fields;
    }
    public function _updateOrdering($attributeGroups)
    {
        $page = (int)Tools::getValue('page',1);
        if($attributeGroups)
        {
            foreach($attributeGroups as $key=> $id)
            {
                $position = ($page-1)*20 +$key+1;
                Db::getInstance()->execute('UPDATE  `'._DB_PREFIX_.'ets_eto_attr_group` SET position ="'.(int)$position.'" WHERE id_ets_eto_attr_group='.(int)$id);
            }
            die(
                Tools::jsonEncode(
                    array(
                        'success' => $this->l('Successfully updated'),
                        'page'=>$page
                    )
                )
            );
        }
    }
    public static function getListAttributeGroups()
    {
        $sql ='SELECT g.id_ets_eto_attr_group,gl.name FROM  `'._DB_PREFIX_.'ets_eto_attr_group` g
        LEfT JOIN  `'._DB_PREFIX_.'ets_eto_attr_group_lang` gl ON (g.id_ets_eto_attr_group=gl.id_ets_eto_attr_group AND gl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE g.id_shop="'.(int)Context::getContext()->shop->id.'" ORDER BY g.position ASC';
        return Db::getInstance()->executeS($sql);
    }
    public static function getListGroupHasAttribute($id_product=false)
    {
        $sql = 'SELECT a.id_ets_eto_attr_group,gl.name FROM  `'._DB_PREFIX_.'ets_eto_attr` a
        LEFT JOIN  `'._DB_PREFIX_.'ets_eto_attr_group` g ON (g.id_ets_eto_attr_group=a.id_ets_eto_attr_group)
        LEFT JOIN  `'._DB_PREFIX_.'ets_eto_attr_group_lang` gl ON (g.id_ets_eto_attr_group = gl.id_ets_eto_attr_group AND gl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE a.id_shop="'.(int)Context::getContext()->shop->id.'" '.($id_product!==false ? ' AND a.id_product="'.(int)$id_product.'"':'AND a.id_product!=0').' GROUP BY a.id_ets_eto_attr_group ORDER BY g.position ASC';
        $groups = Db::getInstance()->executeS($sql);
        if(Count($groups) >1 && !$groups[0]['id_ets_eto_attr_group'])
        {
            $groups[] = $groups[0];
            unset($groups[0]);
        }
        return $groups;
    }
 }