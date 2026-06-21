<?php

namespace Paymenter\Extensions\Gateways\BytArchBankTransfer;

use App\Classes\Extension\Gateway;
use App\Models\Currency;
use App\Models\Invoice;
use Illuminate\Support\Facades\View;

class BytArchBankTransfer extends Gateway
{
    public function boot()
    {
        View::addNamespace('gateways.bytarchbanktransfer', __DIR__ . '/views');
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     * @return array
     */
    public function getConfig($values = [])
    {
        $bank_list = [
            [
                'label' => 'Disable',
                'value' => 0,
            ],
            [
                'label' => 'Atlantic Bank',
                'value' => 'atlantic-bank',
            ],
            [
                'label' => 'Belize Bank',
                'value' => 'belize-bank',
            ],
            [
                'label' => 'Heritage Bank',
                'value' => 'heritage-bank',
            ],
            [
                'label' => 'National Bank of Belize',
                'value' => 'national-bank-of-belize',
            ],
            [
                'label' => 'DigiWallet',
                'value' => 'digiwallet',
            ],
        ];

        return [
            [
                'name' => 'order_id_prefix',
                'label' => 'Order ID Prefix',
                'type' => 'text',
                'placeholder' => 'BA',
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
            // Account 1
            [
                'name' => 'bank_name_1',
                'label' => 'Payment 1: Bank Name',
                'type' => 'select',
                'description' => 'Name of Bank to Accept Payment',
                'required' => true,
                'options' => $bank_list,
            ],
            [
                'name' => 'merchant_name_1',
                'label' => 'Payment 1: Account Name',
                'type' => 'text',
                'description' => 'Name of the Account Holder',
                'required' => true,
            ],
            [
                'name' => 'bank_account_number_1',
                'label' => 'Payment 1: Account Number (or Phone Number for DigiWallet)',
                'type' => 'text',
                'description' => 'Account Number of Bank to Accept Payment. For DigiWallet, enter the admin phone number.',
                'required' => true,
            ],
            // Account 2
            [
                'name' => 'bank_name_2',
                'label' => 'Payment 2: Bank Name',
                'type' => 'select',
                'description' => 'Name of Bank to Accept Payment',
                'required' => false,
                'options' => $bank_list,
            ],
            [
                'name' => 'merchant_name_2',
                'label' => 'Payment 2: Account Name',
                'type' => 'text',
                'description' => 'Name of the Account Holder',
                'required' => false,
            ],
            [
                'name' => 'bank_account_number_2',
                'label' => 'Payment 2: Account Number (or Phone Number for DigiWallet)',
                'type' => 'text',
                'description' => 'Account Number of Bank to Accept Payment. For DigiWallet, enter the admin phone number.',
                'required' => false,
            ],
            // Account 3
            [
                'name' => 'bank_name_3',
                'label' => 'Payment 3: Bank Name',
                'type' => 'select',
                'description' => 'Name of Bank to Accept Payment',
                'required' => false,
                'options' => $bank_list,
            ],
            [
                'name' => 'merchant_name_3',
                'label' => 'Payment 3: Account Name',
                'type' => 'text',
                'description' => 'Name of the Account Holder',
                'required' => false,
            ],
            [
                'name' => 'bank_account_number_3',
                'label' => 'Payment 3: Account Number (or Phone Number for DigiWallet)',
                'type' => 'text',
                'description' => 'Account Number of Bank to Accept Payment. For DigiWallet, enter the admin phone number.',
                'required' => false,
            ],

            [
                'name' => 'whatsapp_number',
                'label' => 'WhatsApp Number (format 501xxxxxxxxx)',
                'type' => 'text',
                'description' => 'WhatsApp Number for Sending Confirmation (format 501xxxxxxxxx)',
                'required' => true,
            ],
            [
                'name' => 'confirmation_message',
                'label' => 'Confirmation message send by Customer via WhatsApp',
                'type' => 'text',
                'description' => 'e.g., Hello, here is my payment confirmation for order #12345',
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

        $order_id_prefix = $this->config('order_id_prefix');
        $invoiceId = substr($order_id, strlen($order_id_prefix));
        $invoice = Invoice::find($invoiceId);
        $total = isset($invoice->credits) ? $invoice->credits : $total;

        $bank_name_1 = $this->config('bank_name_1');
        $name_1 = $bank_name_1 != 0 ? ucwords(str_replace('-', ' ', $bank_name_1)) : '';
        $merchant_name_1 = $this->config('merchant_name_1');
        $bank_account_number_1 = $this->config('bank_account_number_1');
        $bank_list = [[$bank_name_1, $name_1]];
        $merchant_list = [$merchant_name_1];
        $bank_account_list = [$bank_account_number_1];

        $bank_name_2 = $this->config('bank_name_2');
        $name_2 = $bank_name_2 != 0 ? ucwords(str_replace('-', ' ', $bank_name_2)) : '';
        $merchant_name_2 = $this->config('merchant_name_2');
        $bank_account_number_2 = $this->config('bank_account_number_2');
        if ($bank_name_2 != 0 && $merchant_name_2 != '' && $bank_account_number_2 != '') {
            array_push($bank_list, [$bank_name_2, $name_2]);
            array_push($merchant_list, $merchant_name_2);
            array_push($bank_account_list, $bank_account_number_2);
        }

        $bank_name_3 = $this->config('bank_name_3');
        $name_3 = $bank_name_3 != 0 ? ucwords(str_replace('-', ' ', $bank_name_3)) : '';
        $merchant_name_3 = $this->config('merchant_name_3');
        $bank_account_number_3 = $this->config('bank_account_number_3');
        if ($bank_name_3 != 0 && $merchant_name_3 != '' && $bank_account_number_3 != '') {
            array_push($bank_list, [$bank_name_3, $name_3]);
            array_push($merchant_list, $merchant_name_3);
            array_push($bank_account_list, $bank_account_number_3);
        }

        $whatsapp_number = $this->config('whatsapp_number');
        $confirmation_message = $this->config('confirmation_message');
        $payment_confirmation_eta = $this->config('payment_confirmation_eta');

        $back_invoice = route('invoices', $invoiceId);

        $currency = Currency::where('code', $invoice->currency_code)->first();

        return view('gateways.bytarchbanktransfer::pay', [
            'order_id' => $order_id,
            'back_invoice' => $back_invoice,
            'prefix' => $currency ? $currency->prefix : '',
            'total' => $total,
            'suffix' => $currency ? $currency->suffix : '',

            'bank_list' => $bank_list,
            'merchant_list' => $merchant_list,
            'bank_account_list' => $bank_account_list,

            'whatsapp_number' => $whatsapp_number,
            'confirmation_message' => $confirmation_message,
            'payment_confirmation_eta' => $payment_confirmation_eta,
        ]);
    }
}
