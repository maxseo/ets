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

class Ets_onepagecheckoutCallbackModuleFrontController extends ModuleFrontController
{
	public $errors = array();
	public function __construct()
	{	
       parent::__construct();
	}
	public function initContent()
	{
		parent::initContent();
		try
        {
            $hybridauth = new Hybridauth\Hybridauth($this->module->getLoginConfigs());
            $storage = new Hybridauth\Storage\Session();
            if (($provider = $storage->get('provider')))
            {
                if (!(isset($this->context->cookie->soloProvider)) || !$this->context->cookie->soloProvider || $this->context->cookie->soloProvider != $provider)
                {
                    $this->context->cookie->soloProvider = $provider;
                    $this->context->cookie->write();
                }
                $adapter = $hybridauth->getAdapter($provider);
                
                $adapter->authenticate();
                
                $userProfile = $adapter->getUserProfile();
                return $this->etsProsessProfile($userProfile,$storage,$provider);
            }
        }
        catch (Hybridauth\Exception\Exception $exception)
        {
            echo $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/hook/frontJs.tpl');
            exit;
        }
        if (!$this->context->customer->isLogged()){
            Tools::redirectLink($this->context->link->getPageLink('index', Tools::usingSecureMode()? true : false));
        }
	}
    
    public function etsProsessProfile($userProfile = false, $storage = false, $provider = false){
        if (empty($userProfile->email)){
            if (($registerEmail = Tools::getValue('email', null))) {
                if (!Validate::isEmail($registerEmail)) {
                    $this->errors[] = $this->module->l('Email is invalid','callback');
                } elseif(Customer::customerExists($registerEmail)) {
                    $this->errors[] = $this->module->l('Email is exist. Please enter other email.','callback');
                } else {
                    $userProfile->email = $registerEmail;
                }
            }
        }
        if (empty($userProfile->email)){
            $this->context->smarty->assign(array(
                'action' => $this->context->link->getModuleLink($this->module->name, 'callback', array('provider' => $provider), true),
                'errors' => $this->errors? $this->module->displayError($this->errors) : false,
                'userProfile' =>$userProfile,
            ));
            return $this->setTemplate('module:'.$this->module->name.'/views/templates/front/register.tpl');
        }
        else{
            if (($id_customer = Customer::customerExists($userProfile->email, true, true))) {
                $customer = new Customer($id_customer);
                $customer->newsletter = 1;
                $customer->optin =1;
                if($customer->newsletter_date_add=='0000-00-00 00:00:00')
                    $customer->newsletter_date_add = date('y-m-d H:i:s');
                $customer->update();
                $this->context->updateCustomer($customer);
                Hook::exec('actionAuthentication', array('customer' => $customer,'login_social'=>true));
                $this->saveLogin(false);
            } else {
                $this->module->createUser($userProfile, $provider);
                $this->saveLogin(true);
            }
            if ($storage){
                $storage->set('provider', null);
            }
            if (!$this->errors) {
                echo $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/hook/frontJs.tpl');
                exit;
            }
        }
    }
    public function saveLogin($create=false)
    {
        if($this->context->cookie->soloProvider && $this->context->customer->id)
        {
            $social =0;
            switch(Tools::strtolower($this->context->cookie->soloProvider))
            {
                case 'paypal' :
                    $social = Ets_onepagecheckout::LOGIN_PAYPAL;
                break;
                case 'facebook' :
                    $social = Ets_onepagecheckout::LOGIN_FACEBOOK;
                break;
                case 'google' :
                    $social = Ets_onepagecheckout::LOGIN_GOOGLE;
                break;
            }
            if($social)
            {
                Ets_opc_db::updateCustomerSocial($this->context->customer->id,$social,$create);
            }
        }
    }
}