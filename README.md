# BytArch Bank Transfer

BytArch Bank Transfer is a payment gateway extension for [Paymenter](https://github.com/Paymenter/paymenter) that enables clients to choose their preferred Belizean bank for manual transfers and confirm payments via WhatsApp.

## Features

- **Support for Up to 3 Bank Accounts**: Configure up to three different bank accounts to receive payments.
- **Click-to-WhatsApp**: Clients can easily confirm their payments by clicking a button that redirects them to a WhatsApp chat with the seller.
- **All Currency Support**: Supports transactions in all currencies configured in the settings.
- **Belize Banks**: Compatible with major Belizean banks including Atlantic Bank, Belize Bank, Heritage Bank, National Bank of Belize, and DigiWallet.

## How It Works

1. **Select Payment Method**: Clients choose from the list of available banks during checkout.
2. **Manual Transfer**: Clients manually transfer the payment amount using their selected bank.
3. **Confirm via WhatsApp**: After transferring, clients click a button to confirm the payment through WhatsApp messaging with the seller.

## Installation

### Option 1: Install via One-line script

1. **SSH to Paymenter Machine**

2. **Installation**

Execute the command below:
```bash
git clone https://github.com/bytarch/BytArchBankTransfer.git /var/www/paymenter/extensions/Gateways/BytArchBankTransfer
```

3. **Build the Assets**

Follow this link for build the assets of the extension:

[https://paymenter.org/development/theme/assets](https://paymenter.org/development/theme/assets)

4. **Activate the Extension**:

   - Log in to your Paymenter admin panel.
   - Navigate to Extensions > Gateways.
   - Click "Create New Gateway".
   - Find **BytArchBankTransfer**, set a name, and click the "Create" button.

5. **Configuration**
   - On the Gateways List, edit the BytArch entry.
   - Fill out the required fields.

### Option 2: Manual Installation via GitHub

1. **Download the Extension**:

   - Download the ZIP file from Github.

2. **Upload to Paymenter**:

   - Place the `BytArchBankTransfer` folder into the `/var/www/paymenter/extensions/Gateways/BytArchBankTransfer` directory of your Paymenter installation.

3. **Activate the Extension**:

   - Log in to your Paymenter admin panel.
   - Navigate to Extensions > Gateways.
   - Click "Create New Gateway".
   - Find **BytArchBankTransfer**, set a name, and click the "Create" button.

4. **Configuration**
   - On the Gateways List, edit the BytArch entry.
   - Fill out the required fields.

## Configuration

After activating the extension, you need to configure it with your bank account details and other settings.

### Configuration Fields

Here are the fields that you need to fill in:

- **Order ID Prefix** (`order_id_prefix`):
  - *Type*: Text
  - *Placeholder*: BA
  - *Required*: No
  - *Description*: A prefix to be added to all order IDs.

- **Payment Confirmation ETA** (`payment_confirmation_eta`):
  - *Type*: Text
  - *Placeholder*: Estimate time for payment confirmation
  - *Required*: No
  - *Description*: Estimated time for confirming payments.

#### Payment Method 1

- **Payment 1: Bank Name** (`bank_name_1`):
  - *Type*: Dropdown
  - *Required*: Yes
  - *Options*: List of supported Belizean banks.
  - *Description*: Name of the bank to accept payment.

- **Payment 1: Account Name** (`merchant_name_1`):
  - *Type*: Text
  - *Required*: Yes
  - *Description*: Name of the account holder.

- **Payment 1: Account Number** (`bank_account_number_1`):
  - *Type*: Text
  - *Required*: Yes
  - *Description*: Account number of the bank to accept payment.

#### Payment Method 2 (Optional)

- **Payment 2: Bank Name** (`bank_name_2`):
  - *Type*: Dropdown
  - *Required*: No
  - *Options*: List of supported Belizean banks.
  - *Description*: Name of the bank to accept payment.

- **Payment 2: Account Name** (`merchant_name_2`):
  - *Type*: Text
  - *Required*: No
  - *Description*: Name of the account holder.

- **Payment 2: Account Number** (`bank_account_number_2`):
  - *Type*: Text
  - *Required*: No
  - *Description*: Account number of the bank to accept payment.

#### Payment Method 3 (Optional)

- **Payment 3: Bank Name** (`bank_name_3`):
  - *Type*: Dropdown
  - *Required*: No
  - *Options*: List of supported Belizean banks.
  - *Description*: Name of the bank to accept payment.

- **Payment 3: Account Name** (`merchant_name_3`):
  - *Type*: Text
  - *Required*: No
  - *Description*: Name of the account holder.

- **Payment 3: Account Number** (`bank_account_number_3`):
  - *Type*: Text
  - *Required*: No
  - *Description*: Account number of the bank to accept payment.

#### WhatsApp Configuration

- **WhatsApp Number** (`whatsapp_number`):
  - *Type*: Text
  - *Placeholder*: WhatsApp Number for Sending Confirmation (format 501xxxxxxxxx)
  - *Required*: Yes
  - *Description*: Your WhatsApp number in the format `501xxxxxxxxx` (Belize country code).

- **Confirmation Message** (`confirmation_message`):
  - *Type*: Text
  - *Placeholder*: e.g., "Hello, here is my payment confirmation for order #12345"
  - *Required*: Yes
  - *Description*: The message that customers will send via WhatsApp to confirm their payment.

### Steps to Configure

1. **Navigate to the Extension Settings**:

   - In the Paymenter admin panel, go to **Extensions** > **Gateways**.
   - Click "New Gateway".
   - Choose **BytArchBankTransfer**.
   - Click "Create".

2. **Fill in the Required Fields**:

   - Enter the **Order ID Prefix** (optional).
   - Set the **Payment Confirmation ETA** (optional).

3. **Configure Payment Methods**:

   - For **Payment 1**, select the **Bank Name** from the dropdown, and fill in the **Account Name** and **Account Number**.
   - Optionally, repeat the above step for **Payment 2** and **Payment 3**.

4. **Set Up WhatsApp Details**:

   - Enter your **WhatsApp Number** in the format `501xxxxxxxxx`.
   - Define the **Confirmation Message** that customers will send.

5. **Save the Settings**.

## Usage

- **For Clients**:

  1. During checkout, select **BytArchBankTransfer** as the payment method.
  2. Choose your preferred bank from the available options.
  3. Transfer the payment amount manually using the provided account details.
  4. Click the **Send payment confirmation via WhatsApp** button to send the predefined message and confirm the payment.

- **For Sellers**:

  - Ensure your WhatsApp contact information and configured bank details are correct.
  - Monitor WhatsApp for payment confirmations from clients.
  - Verify the payment and process the order accordingly.

## Requirements

- **Paymenter**: Ensure you have Paymenter installed and properly configured.
- **WhatsApp**: Clients need WhatsApp installed on their device to confirm payments.
- **Belize Bank Accounts**: Only Belizean financial institutions are supported.

## Screenshot

Below is a screenshot of the BytArch Bank Transfer extension in action:

![BytArch Bank Transfer](img/GPM-tf.png)

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch: `git checkout -b feature/your-feature-name`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature/your-feature-name`.
5. Open a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
