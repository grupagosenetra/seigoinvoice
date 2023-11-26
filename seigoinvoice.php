<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Seigoinvoice extends Module
{
    public function __construct()
    {
        $this->name = 'seigoinvoice';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Rafał Senetra';
        $this->need_instance = 1;        
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SEIGO Invoice Uploader');
        $this->description = $this->l('SEIGO Invoice Uploader');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
      
    }

    public function install()
    {
        return (parent::install() &&
            $this->registerHook([
                'actionFrontControllerSetMedia',
                'displayBackOfficeHeader',
                'displayCustomerAccount'
            ])
            && self::createTables()
        );
    }

    private static function createTables(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'seigo_invoice(
            id_seigo_invoice INT NOT NULL AUTO_INCREMENT,
            id_order INT NOT NULL,
            filename varchar(100) NOT NULL,
            date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            primary key(id_seigo_invoice,id_order)
        );';
        Db::getInstance()->execute($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'seigo_invoice_customer(
            id_seigo_invoice_customer INT NOT NULL AUTO_INCREMENT,
            id_customer INT NOT NULL,
            email_invoice varchar(100) NOT NULL,
            date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            primary key(id_seigo_invoice_customer,id_customer)
        );';
        Db::getInstance()->execute($sql);
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent(){
        return $this->fetch('module:seigoinvoice/views/templates/admin/advert.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addJS($this->_path . 'views/js/main.js');
        $this->context->controller->addCSS($this->_path . 'views/css/main.css');

        $this->initJSParams();
    }


    public function hookDisplayBackOfficeHeader()
    {
        $this->hookActionFrontControllerSetMedia();
    }
    public function hookDisplayCustomerAccount(array $params)
    {
        $this->smarty->assign([
            'url' => $this->context->link->getModuleLink('seigoinvoice', 'invoice'),
            'wishlistsTitlePage' => Configuration::get('blockwishlist_WishlistPageName', $this->context->language->id),
        ]);

        return $this->fetch('module:seigoinvoice/views/templates/hook/displayCustomerAccount.tpl');
    }    

    private function initJSParams(): void
    {
        Media::addJsDef([
            'SeigoInvoiceControllerUrl' => $this->context->link->getModuleLink(
                'seigoinvoice',
                'invoice'
            ),
            'seigotoken'=>Tools::getAdminToken('AdminDashboard')
        ]);
        Media::addJsDef(['id_order' => (int)Tools::getValue('id_order')]);
    }
}
