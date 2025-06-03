<?php

namespace Paymenter\Extensions\Gateways\GerbangPembayaranManual;

use App\Classes\Extension\Gateway;
use App\Models\Currency;
use App\Models\Invoice;
use Illuminate\Support\Facades\View;

class GerbangPembayaranManual extends Gateway
{
    public function boot()
    {
        // require __DIR__ . '/routes.php';
        // Register webhook route
        View::addNamespace('gateways.gerbangpembayaranmanual', __DIR__ . '/views');
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     * @return array
     */
    public function getConfig($values = [])
    {
        // return [];
        $bank_list = [
            [
                'label' => 'Disable',
                'value' => 0,
            ],
            [
                'label' => 'Mandiri',
                'value' => 'Bank/Bank%20Logo/Mandiri',
            ],
            [
                'label' => 'BCA',
                'value' => 'Bank/Bank%20Logo/BCA',
            ],
            [
                'label' => 'BNI',
                'value' => 'Bank/Bank%20Logo/BNI',
            ],
            [
                'label' => 'BRI',
                'value' => 'Bank/Bank%20Logo/BRI',
            ],
            [
                'label' => 'Jenius',
                'value' => 'Bank/Bank%20App/Jenius',
            ],
            [
                'label' => 'Gopay',
                'value' => 'Bill%20Payment/E-Wallet/Gopay',
            ],
            [
                'label' => 'DANA',
                'value' => 'Payment%20Channel/E-Wallet/DANA',
            ],
            [
                'label' => 'Shopee Pay',
                'value' => 'Payment%20Channel/E-Wallet/Shopee%20Pay',
            ],
            [
                'label' => 'QRIS',
                'value' => 'Payment%20Channel/Miscellaneous/QRIS',
            ],
        ];

        return [
            [
                'name' => 'order_id_prefix',
                'label' => 'Order ID Prefix',
                'type' => 'text',
                'placeholder' => 'XYZ',
                'description' => 'Order ID Prefix',
                'required' => false,
            ],
            [
                'name' => 'payment_confirmation_eta',
                'label' => 'Payment Confirmation ETA',
                'type' => 'text',
                'placeholder' => '24 Hours',
                'description' => 'Estimate time for payment confirmation',
                'required' => false,
            ],
            // Rekening 1
            [
                'name' => 'bank_name_1',
                'label' => 'Payment 1: Bank or Wallet Name',
                'type' => 'select',
                'description' => 'Name of Bank or Wallet to Accept Payment',
                'required' => true,
                'options' => $bank_list,
            ],
            [
                'name' => 'merchant_name_1',
                'label' => 'Payment 1: Merchant or Account Name',
                'type' => 'text',
                'description' => 'Name of the Merchant/Account Holder',
                'required' => true,
            ],
            [
                'name' => 'bank_account_number_1',
                'label' => 'Payment 1: Bank or Wallet Account Number',
                'type' => 'text',
                'description' => 'Account Number of Bank or Wallet to Accept Payment (Put 0 for QRIS)',
                'required' => true,
            ],
            [
                'name' => 'qris_image_url_1',
                'label' => 'Payment 1: QRIS Image URL',
                'type' => 'text',
                'description' => 'URL of QRIS image (only needed if QRIS is selected)',
                'required' => false,
            ],
            // Rekening 2
            [
                'name' => 'bank_name_2',
                'label' => 'Payment 2: Bank or Wallet Name',
                'type' => 'select',
                'description' => 'Name of Bank or Wallet to Accept Payment',
                'required' => false,
                'options' => $bank_list,
            ],
            [
                'name' => 'merchant_name_2',
                'label' => 'Payment 2: Merchant or Account Name',
                'type' => 'text',
                'description' => 'Name of the Merchant/Account Holder',
                'required' => false,
            ],
            [
                'name' => 'bank_account_number_2',
                'label' => 'Payment 2: Bank or Wallet Account Number',
                'type' => 'text',
                'description' => 'Account Number of Bank or Wallet to Accept Payment (Put 0 for QRIS)',
                'required' => false,
            ],
            [
                'name' => 'qris_image_url_2',
                'label' => 'Payment 2: QRIS Image URL',
                'type' => 'text',
                'description' => 'URL of QRIS image (only needed if QRIS is selected)',
                'required' => false,
            ],
            // Rekening 3
            [
                'name' => 'bank_name_3',
                'label' => 'Payment 3: Bank or Wallet Name',
                'type' => 'select',
                'description' => 'Name of Bank or Wallet to Accept Payment',
                'required' => false,
                'options' => $bank_list,
            ],
            [
                'name' => 'merchant_name_3',
                'label' => 'Payment 3: Merchant or Account Name',
                'type' => 'text',
                'description' => 'Name of the Merchant/Account Holder',
                'required' => false,
            ],
            [
                'name' => 'bank_account_number_3',
                'label' => 'Payment 3: Bank or Wallet Account Number',
                'type' => 'text',
                'description' => 'Account Number of Bank or Wallet to Accept Payment (Put 0 for QRIS)',
                'required' => false,
            ],
            [
                'name' => 'qris_image_url_3',
                'label' => 'Payment 3: QRIS Image URL',
                'type' => 'text',
                'description' => 'URL of QRIS image (only needed if QRIS is selected)',
                'required' => false,
            ],

            [
                'name' => 'whatsapp_number',
                'label' => 'Whatsapp Number (format 628xxxxxxxxx)',
                'type' => 'text',
                'description' => 'Whatsapp Number for Sending Confirmation (format 628xxxxxxxxx)',
                'required' => true,
            ],
            [
                'name' => 'confirmation_message',
                'label' => 'Confirmation message send by Customer via Whatsapp',
                'type' => 'text',
                'description' => 'ex: Halo Admin, berikut bukti pembayaran sewa cloud',
                'required' => true,
            ],
        ];
    }

    /**
     * Return a view or a url to redirect to
     *
     * @param  float  $total
     * @return string
     */
    public function pay(Invoice $invoice, $total)
    {
        $order_id = $this->config('order_id_prefix') . $invoice->id;
        // return view('extensions.gateways.gerbangpembayaranmanual.payment', ['order_id' => $order_id]);

        // $order_id = $request['order_id'];
        $order_id_prefix = $this->config('order_id_prefix');
        $invoiceId = substr($order_id, strlen($order_id_prefix));
        $invoice = Invoice::find($invoiceId);
        // dd($invoice);
        $total = isset($invoice->credits) ? $invoice->credits : $total;

        $bank_name_1 = $this->config('bank_name_1');
        $name_1 = urldecode(substr(strrchr($bank_name_1, '/'), 1));
        $merchant_name_1 = $this->config('merchant_name_1');
        $bank_account_number_1 = $this->config('bank_account_number_1');
        $qris_image_url_1 = $this->config('qris_image_url_1');
        $bank_list = [[$bank_name_1, $name_1]];
        $merchant_list = [$merchant_name_1];
        $bank_account_list = [$bank_account_number_1];
        $qris_image_list = [$qris_image_url_1];

        $bank_name_2 = $this->config('bank_name_2');
        $name_2 = urldecode(substr(strrchr($bank_name_2, '/'), 1));
        $merchant_name_2 = $this->config('merchant_name_2');
        $bank_account_number_2 = $this->config('bank_account_number_2');
        $qris_image_url_2 = $this->config('qris_image_url_2');
        if ($bank_name_2 != 0 && $merchant_name_2 != '' && $bank_account_number_2 != '') {
            array_push($bank_list, [$bank_name_2, $name_2]);
            array_push($merchant_list, $merchant_name_2);
            array_push($bank_account_list, $bank_account_number_2);
            array_push($qris_image_list, $qris_image_url_2);
        }

        $bank_name_3 = $this->config('bank_name_3');
        $name_3 = urldecode(substr(strrchr($bank_name_3, '/'), 1));
        $merchant_name_3 = $this->config('merchant_name_3');
        $bank_account_number_3 = $this->config('bank_account_number_3');
        $qris_image_url_3 = $this->config('qris_image_url_3');
        if ($bank_name_3 != 0 && $merchant_name_3 != '' && $bank_account_number_3 != '') {
            array_push($bank_list, [$bank_name_3, $name_3]);
            array_push($merchant_list, $merchant_name_3);
            array_push($bank_account_list, $bank_account_number_3);
            array_push($qris_image_list, $qris_image_url_3);
        }

        $whatsapp_number = $this->config('whatsapp_number');
        $confirmation_message = $this->config('confirmation_message');
        $payment_confirmation_eta = $this->config('payment_confirmation_eta');

        $back_invoice = route('invoices', $invoiceId);
        // dd([
        //     'order_id' => $order_id,
        //     'back_invoice' => $back_invoice,
        //     'total' => $total,

        //     'bank_list' => $bank_list,
        //     'merchant_list' => $merchant_list,
        //     'bank_account_list' => $bank_account_list,

        //     'whatsapp_number' => $whatsapp_number,
        //     'confirmation_message' => $confirmation_message,
        //     'payment_confirmation_eta' => $payment_confirmation_eta,
        // ]);

        return view('gateways.gerbangpembayaranmanual::pay', [
            'order_id' => $order_id,
            'back_invoice' => $back_invoice,
            'prefix' => Currency::where('code', $invoice->currency_code)->first()->prefix,
            'total' => $total,
            'suffix' => Currency::where('code', $invoice->currency_code)->first()->suffix,

            'bank_list' => $bank_list,
            'merchant_list' => $merchant_list,
            'bank_account_list' => $bank_account_list,
            'qris_image_list' => $qris_image_list,

            'whatsapp_number' => $whatsapp_number,
            'confirmation_message' => $confirmation_message,
            'payment_confirmation_eta' => $payment_confirmation_eta,
        ]);
    }
}
