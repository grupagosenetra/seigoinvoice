<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class SeigoInvoiceInvoiceModuleFrontController extends ModuleFrontController
{

    public $template;
    public $php_self;
    protected $template_path = '';
    public $auth = false;
    public $guestAllowed = false;


    public function __construct()
    {

        parent::__construct();
        require_once dirname(__FILE__) . '/../../classes/Sinvoice.php';
        require_once dirname(__FILE__) . '/../../classes/Scustomer.php';
        $this->bootstrap = true;
        $this->context = Context::getContext();
    }


    public function initContent()
    {   
        parent::initContent();

        $id_customer = (int) $this->context->customer->id;
        $idScustomer = Scustomer::checkCustomerExists($id_customer);
        if (!empty($idScustomer)) {

            $scustomer = new Scustomer($idScustomer);

            $this->context->smarty->assign(array(
                'email_invoice' => $scustomer->email_invoice
            ));
        }

        $invoices = Sinvoice::getInvoiceByIdCustomer($id_customer);
        if (!empty($invoices)) {
            $this->context->smarty->assign(array(
                'invoices' => $invoices
            ));
        }

        $this->setTemplate('module:seigoinvoice/views/templates/front/list.tpl');
    }

    public function postProcess()
    {
        $email = Tools::getValue('emailInvoice');

        if (Validate::isEmail($email)) {

            $id_customer = (int) $this->context->customer->id;
            if (empty($id_customer))
                return;
            $idScustomer = Scustomer::checkCustomerExists($id_customer);

            $scustomer = new Scustomer($idScustomer);
            $scustomer->email_invoice = $email;
            $scustomer->id_customer = $id_customer;
            $scustomer->save();
        }
    }

    public function displayAjaxUploadInvoice()
    {
        if (Tools::getAdminToken('AdminDashboard') != Tools::getValue('seigotoken')) {
            die();
        }
        $id_order = (int) Tools::getValue('id_order');
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $file_name = $id_order . '_' . md5(uniqid((string)mt_rand(0, mt_getrandmax()), true)) . '.' . $extension;
        $errors = [];

        if (!isset($_FILES['file']) || !isset($_FILES['file']['tmp_name'])) {
            $errors[] = $this->trans('File is empty.', [], 'Admin.Catalog.Notification');
        }

        if (!(strtolower($extension) == 'pdf')) {
            $errors[] = $this->trans('Wrong extension, You can upload only pdf file.', [], 'Admin.Catalog.Notification');
        }

        if (empty($errors)) {
            @mkdir(Sinvoice::$dir, 0777, true);
            if (!move_uploaded_file($_FILES['file']['tmp_name'], Sinvoice::$dir . $file_name)) {
                $errors[] = $this->trans('An error occurred during the image upload process.', [], 'Admin.Catalog.Notification');
            }
        }
        if (empty($errors)) {
            $seigoInvoce = new Sinvoice();
            $seigoInvoce->filename = $file_name;
            $seigoInvoce->id_order = $id_order;
            $seigoInvoce->save();

            $order = new Order($id_order);

            $this->sendEmail($order->id_customer, $id_order);
        }
        $this->context->smarty->assign([
            'errors' => $errors
        ]);
        $this->displayAjaxFormInvoice();
    }

    public function displayAjaxRemoveInvoice()
    {
        if (Tools::getAdminToken('AdminDashboard') != Tools::getValue('seigotoken')) {
            die();
        }

        $id_order =  (int) Tools::getValue('id_order');
        $sinvoice = Sinvoice::getInvoiceByIdOrder($id_order);
        @unlink(Sinvoice::$dir . $sinvoice->filename);
        $sinvoice->delete();

        die(json_encode([
            'success' => true
        ]));
    }

    public function displayAjaxFormInvoice()
    {
        if (Tools::getAdminToken('AdminDashboard') != Tools::getValue('seigotoken')) {
            die();
        }
        $id_order =  (int) Tools::getValue('id_order');
        $filename = Sinvoice::getFilenameByIdOrder($id_order);
        $this->context->smarty->assign([
            'id_order' => $id_order,
            'filename' => $filename,
            'filePath' => Context::getContext()->shop->getBaseURL(true) . 'upload/seigoinvoice/' . $filename
        ]);

        die(json_encode([
            'form' => $this->context->smarty->fetch(
                $this->module->getLocalPath() . 'views/templates/front/form.tpl'
            )
        ]));
    }

    private function sendEmail(int $id_customer, int $id_order): bool
    {
        $idScustomer = Scustomer::checkCustomerExists($id_customer);

        $customer = new Customer($id_customer);
        if (empty($idScustomer))
            return false;

        $scustomer = new Scustomer($idScustomer);
        if (empty($scustomer->email_invoice))
            return false;

        $filename = Sinvoice::getFilenameByIdOrder($id_order);
        if (empty($filename))
            return false;

        $invoice_url = '<a href="' . Context::getContext()->shop->getBaseURL(true) . 'upload/seigoinvoice/' . $filename . '">' . $this->trans('Faktura', [], 'Emails.Subject') . '</a>';

        $language = new Language($customer->id_lang);

        return (bool) Mail::send(
            $this->context->language->id,
            'seigo_invoice',
            $this->trans(
                'Faktura',
                [],
                'Emails.Subject',
                $language->locale
            ),
            [
                '{invoice_url}' => $invoice_url,
            ],
            $scustomer->email_invoice,
            null,
            null,
            null,
            null,
            null,
            dirname(__FILE__) . '/../../mails/',
            false,
            $this->context->shop->id
        );
    }
}
