<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Sinvoice extends ObjectModel
{
    public $id;

    public $id_seigo_invoice;

    public $id_order;

    public $filename;

    public $date_add;

    public static $dir = _PS_CORE_DIR_ . '/img/seigoinvoice/';

    public static $definition = array(
        'table' => 'seigo_invoice',
        'primary' => 'id_seigo_invoice',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'filename' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'date_add' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true)
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getFilenameByIdOrder(int $id_order): ?string
    {
        $sql = 'SELECT filename FROM ' . _DB_PREFIX_ . 'seigo_invoice WHERE id_order = ' . $id_order;
        return Db::getInstance()->getValue($sql);
    }

    public static function getInvoiceByIdOrder(int $id_order): ?Sinvoice
    {
        $sql = 'SELECT id_seigo_invoice FROM ' . _DB_PREFIX_ . 'seigo_invoice WHERE id_order = ' . $id_order;
        $id = Db::getInstance()->getValue($sql);
        return new Sinvoice($id);
    }

    public static function getInvoiceByIdCustomer(int $id_customer): ?array
    {
        $sql = 'SELECT si.id_seigo_invoice, si.id_order FROM ' . _DB_PREFIX_ . 'seigo_invoice si 
        JOIN ' . _DB_PREFIX_ . 'orders o ON si.id_order = o.id_order
        JOIN ' . _DB_PREFIX_ . 'customer c ON o.id_customer = c.id_customer
        WHERE c.id_customer = ' . $id_customer;
        $result = Db::getInstance()->executeS($sql);
        
        $arr = [];        
        foreach($result as $item){
            $arr[] = [
                'invoice'=>new Sinvoice($item['id_seigo_invoice']),
                'order'=>new Order($item['id_order'])
            ];
        }
        return $arr;
    }
}
