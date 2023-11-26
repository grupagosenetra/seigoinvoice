<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Scustomer extends ObjectModel
{
    
    public $id;

    public $id_seigo_customer;

    public $id_customer;

    public $email_invoice;

    public static $definition = array(
        'table' => 'seigo_invoice_customer',
        'primary' => 'id_seigo_invoice_customer',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'email_invoice' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),            
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function checkCustomerExists(int $idCustomer): ?int{
        $sql = 'SELECT id_seigo_invoice_customer FROM '._DB_PREFIX_.'seigo_invoice_customer WHERE id_customer='.$idCustomer;
        
        $id = Db::getInstance()->getValue($sql);
        return $id;
    }
}
